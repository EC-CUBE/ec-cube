import { Client as PgClient } from 'pg';
import mysql from 'mysql2/promise';

/** バインドパラメータに渡せる値。 */
export type DbParam = string | number | boolean | null;

export interface DbClient {
  tableExists(tableName: string): Promise<boolean>;
  columnExists(tableName: string, columnName: string): Promise<boolean>;
  getPlugin(code: string): Promise<{ initialized: boolean; enabled: boolean } | null>;
  /**
   * 任意の SELECT を実行し、先頭行の先頭カラムを返す（0 行なら null）。
   * COUNT(*) 等はドライバによって string で返るため、数値として比較する場合は
   * 呼び出し側で Number() を通すこと。
   */
  fetchOne(sql: string, params?: DbParam[]): Promise<unknown>;
  /** 任意の SELECT を実行し、全行を返す。 */
  fetchAll(sql: string, params?: DbParam[]): Promise<Record<string, unknown>[]>;
  close(): Promise<void>;
}

/**
 * プレースホルダを PostgreSQL の `$n` 形式へ変換する。
 *
 * spec 側は MySQL / PostgreSQL の双方で同じ SQL を書けるよう `?` で統一する
 * （e2e-test.yml は pgsql、plugin-test.yml は pgsql と mysql の両方を回す）。
 * 単純な置換なので、文字列リテラル内に `?` を含む SQL には使えない。
 */
function toPgPlaceholders(sql: string): string {
  let index = 0;

  return sql.replace(/\?/g, () => `$${++index}`);
}

class PgDbClient implements DbClient {
  private client: PgClient;

  constructor(databaseUrl: string) {
    this.client = new PgClient({ connectionString: databaseUrl });
  }

  async connect(): Promise<void> {
    await this.client.connect();
  }

  async tableExists(tableName: string): Promise<boolean> {
    const result = await this.client.query(
      `SELECT count(*) AS count FROM information_schema.columns WHERE table_name = $1`,
      [tableName]
    );
    return parseInt(result.rows[0].count, 10) > 0;
  }

  async columnExists(tableName: string, columnName: string): Promise<boolean> {
    const result = await this.client.query(
      `SELECT count(*) AS count FROM information_schema.columns WHERE table_name = $1 AND column_name = $2`,
      [tableName, columnName]
    );
    return parseInt(result.rows[0].count, 10) === 1;
  }

  async getPlugin(code: string): Promise<{ initialized: boolean; enabled: boolean } | null> {
    const result = await this.client.query(
      `SELECT initialized, enabled FROM dtb_plugin WHERE code = $1`,
      [code]
    );
    if (result.rows.length === 0) return null;
    return {
      initialized: result.rows[0].initialized,
      enabled: result.rows[0].enabled,
    };
  }

  async fetchOne(sql: string, params: DbParam[] = []): Promise<unknown> {
    const result = await this.client.query(toPgPlaceholders(sql), params);
    if (result.rows.length === 0) return null;
    const row = result.rows[0];

    return row[Object.keys(row)[0]];
  }

  async fetchAll(sql: string, params: DbParam[] = []): Promise<Record<string, unknown>[]> {
    const result = await this.client.query(toPgPlaceholders(sql), params);

    return result.rows;
  }

  async close(): Promise<void> {
    await this.client.end();
  }
}

class MysqlDbClient implements DbClient {
  private connection: mysql.Connection | null = null;
  private databaseUrl: string;

  constructor(databaseUrl: string) {
    this.databaseUrl = databaseUrl;
  }

  async connect(): Promise<void> {
    const url = new URL(this.databaseUrl);
    this.connection = await mysql.createConnection({
      host: url.hostname,
      port: parseInt(url.port || '3306', 10),
      user: url.username,
      password: url.password,
      database: url.pathname.slice(1),
    });
  }

  private conn(): mysql.Connection {
    if (!this.connection) throw new Error('Not connected');
    return this.connection;
  }

  async tableExists(tableName: string): Promise<boolean> {
    const [rows] = await this.conn().execute(
      `SELECT count(*) AS count FROM information_schema.columns WHERE table_name = ? AND table_schema = DATABASE()`,
      [tableName]
    );
    return (rows as any)[0].count > 0;
  }

  async columnExists(tableName: string, columnName: string): Promise<boolean> {
    const [rows] = await this.conn().execute(
      `SELECT count(*) AS count FROM information_schema.columns WHERE table_name = ? AND column_name = ? AND table_schema = DATABASE()`,
      [tableName, columnName]
    );
    return (rows as any)[0].count === 1;
  }

  async getPlugin(code: string): Promise<{ initialized: boolean; enabled: boolean } | null> {
    const [rows] = await this.conn().execute(
      `SELECT initialized, enabled FROM dtb_plugin WHERE code = ?`,
      [code]
    );
    const results = rows as any[];
    if (results.length === 0) return null;
    return {
      initialized: Boolean(results[0].initialized),
      enabled: Boolean(results[0].enabled),
    };
  }

  async fetchOne(sql: string, params: DbParam[] = []): Promise<unknown> {
    const [rows] = await this.conn().execute(sql, params);
    const results = rows as Record<string, unknown>[];
    if (results.length === 0) return null;
    const row = results[0];

    return row[Object.keys(row)[0]];
  }

  async fetchAll(sql: string, params: DbParam[] = []): Promise<Record<string, unknown>[]> {
    const [rows] = await this.conn().execute(sql, params);

    return rows as Record<string, unknown>[];
  }

  async close(): Promise<void> {
    if (this.connection) {
      await this.connection.end();
    }
  }
}

export async function createDbClient(databaseUrl: string): Promise<DbClient> {
  if (databaseUrl.startsWith('postgres')) {
    const client = new PgDbClient(databaseUrl);
    await client.connect();
    return client;
  } else if (databaseUrl.startsWith('mysql')) {
    const client = new MysqlDbClient(databaseUrl);
    await client.connect();
    return client;
  }
  const protocol = new URL(databaseUrl).protocol;
  throw new Error(`Unsupported database protocol: ${protocol}`);
}

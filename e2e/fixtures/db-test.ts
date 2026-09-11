import { test as base, expect } from '@playwright/test';
import { createDbClient, DbClient } from '../helpers/db-client';

export { expect };

export type DbFixtures = {
  db: DbClient;
};

/**
 * DB を参照する spec 用の拡張 fixture。
 *
 * UI からは確認できない結果（重複レコードの有無・非正規化カラムのズレ等）を
 * 検証する spec だけがこれを import する。Playwright の fixture は遅延生成なので、
 * `db` を引数に取らないテストでは接続されない。
 *
 * plugin 系の fixture（fixtures/plugin-test.ts）もこれを継承する。
 */
export const test = base.extend<DbFixtures>({
  db: async ({}, use) => {
    const databaseUrl = process.env.DATABASE_URL;
    if (!databaseUrl) {
      throw new Error('DATABASE_URL environment variable is required');
    }
    const client = await createDbClient(databaseUrl);
    await use(client);
    await client.close();
  },
});

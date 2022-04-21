import ClientApi from 'zaproxy';
import PlaywrightConfig from '../playwright.config';

/**
 * [実行モード](https://www.zaproxy.org/docs/desktop/start/features/modes/)の列挙型.
 * 特別な理由が無い限りは `Protect` を使用してください.
 * @enum
 */
export const Mode = {
  /** セーフモード - 潜在的に危険な操作を行うことを許可しません. */
  Safe: 'safe',
  /** プロテクトモード - スコープ内のアイテム上のみ潜在的に危険な操作を行うことを許可します. */
  Protect: 'protect',
  /** 標準モード - どんな潜在的に危険な操作を行うことでも許可します. */
  Standard: 'standard',
  /** 攻撃モード - 危険なので使用しないでください! */
  // Attack: 'attack' denger!!
} as const;
type Mode = typeof Mode[keyof typeof Mode];

/**
 * コンテキストの列挙型.
 * 予め用意されているコンテキストのファイル名の列挙型です.
 * コンテキストファイルは `path/to/ec-cube/zap` 以下に保存されています.
 * @enum
 */
export const ContextType = {
  /** フロント画面/ログイン状態のコンテキスト. */
  FrontLogin: 'front_login.context',
  /** フロント画面/非会員状態のコンテキスト. */
  FrontGuest: 'front_guest.context',
  /** 管理画面のコンテキスト. */
  Admin: 'admin.context'
} as const;
type ContextType = typeof ContextType[keyof typeof ContextType];

/**
 * アラートリスクレベルの列挙型.
 * @enum
 */
export const Risk = {
  Informational: 0,
  Low: 1,
  Medium: 2,
  High: 3
} as const;
type Risk = typeof Risk[keyof typeof Risk];

/**
 * OWASP ZAP の履歴の型定義.
 * [ApiResponseConversionUtils.java](https://github.com/zaproxy/zaproxy/blob/main/zap/src/main/java/org/zaproxy/zap/extension/api/ApiResponseConversionUtils.java#L80-L122) を参考に定義しています.
 */
export type HttpMessage = {
  id: string,
  type: string,
  timestamp: string,
  rtt: string,
  cookieParams: string,
  note: string,
  requestHeader: string,
  requestBody: string,
  responseHeader: string,
  responseBody: string,
  tags: string[]
};

/**
 * アラートの型定義.
 * [Alert.java](https://github.com/zaproxy/zaproxy/blob/main/zap/src/main/java/org/parosproxy/paros/core/scanner/Alert.java#L198) を参考に型定義しています.
 */
export type Alert = {
  alertId?: string,
  pluginId?: string
  name: string,
  risk: string,
  confidence?: string,
  description?: string,
  uri?: string,
  param?: string,
  attack?: string,
  otherInfo?: string,
  solution?: string,
  reference?: string,
  evidence?: string,
  cweId?: string,
  wascId?: string,
  message?: any,
  sourceHistoryId?: string,
  historyRef?: any;
  method?: string,
  postData?: string,
  msgUri?: string
  source?: string,
  alertRef?: string
};

/**
 * ZAP API で何らかのエラーが発生した場合の例外.
 */
export class ZapClientError extends Error {
  constructor(message?: string) {
    super(message);
  }
}

/**
 * ZAP API クライアントのクラス.
 */
export class ZapClient {
  /** APIキー. */
  private apiKey: string | null;
  /** プロキシサーバーのホスト名. */
  private proxy: string;
  /** ClientApi のインスタンス. */
  private readonly zaproxy;

  /**
   * コンストラクタ.
   */
  constructor(proxy?: string | null, apiKey?: string | null) {
    this.proxy = proxy ?? PlaywrightConfig.use.proxy.server;
    this.apiKey = apiKey != undefined ? apiKey : null;
    this.zaproxy = new ClientApi({
      apiKey: this.apiKey,
      proxy: this.proxy
    });
  }

  /**
   * 実行モードを設定します.
   * [coreActionSetMode API](https://www.zaproxy.org/docs/api/#coreactionsetmode)を実行します.
   *
   * @param mode 実行モードの列挙型.
   */
  public async setMode(mode: Mode): Promise<void> {
    await this.zaproxy.core.setMode(mode);
  }

  /**
   * 新規セッションを開始します.
   * [coreActionNewSession API](https://www.zaproxy.org/docs/api/#coreactionnewsession) を実行します.
   *
   * @param name セッションファイル名
   * @param override セッションを上書きする場合 true
   */
  public async newSession(name: string, override: boolean): Promise<void> {
    await this.zaproxy.core.newSession(name, override);
  }

  /**
   * コンテキストをインポートします.
   * [contextActionImportContext API](https://www.zaproxy.org/docs/api/#contextactionimportcontext) を実行します
   *
   * @param contextType ContextType の列挙型
   */
  public async importContext(contextType: ContextType): Promise<void> {
    await this.zaproxy.context.importContext('/zap/wrk/' + contextType);
  }

  /**
   * Forced user mode が有効かどうか確認します.
   * [forcedUserViewIsForcedUserModeEnabled API](https://www.zaproxy.org/docs/api/#forceduserviewisforcedusermodeenabled) を実行します.
   *
   * @returns Forced user mode が有効な場合 true
   */
  public async isForcedUserModeEnabled(): Promise<boolean> {
    const result = await this.zaproxy.forcedUser.isForcedUserModeEnabled();
    return JSON.parse(result.forcedModeEnabled);
  }

  /**
   * Forced user mode を設定します.
   * [forcedUserActionSetForcedUserModeEnabled API](https://www.zaproxy.org/docs/api/#forceduseractionsetforcedusermodeenabled) を実行します.
   *
   * @param bool Forced user mode を有効にする場合 true
   */
  public async setForcedUserModeEnabled(bool?: boolean): Promise<void> {
    await this.zaproxy.forcedUser.setForcedUserModeEnabled(bool ?? true);
  }

  /**
   * 手動リクエストを送信します.
   * [coreActionSendRequest API](https://www.zaproxy.org/docs/api/#coreactionsendrequest) を実行します.
   *
   * @param request HTTPリクエストの文字列.
   * @param followRedirects リダイレクトを行う場合 true
   * @returns リクエスト結果の {@link HttpMessage}
   */
  public async sendRequest(request: string, followRedirects?: boolean): Promise<HttpMessage> {
    const result = await this.zaproxy.core.sendRequest(request, followRedirects ?? false);
    return result.sendRequest;
  }

  /**
   * URLに該当する履歴({@link HttpMessage})の数を返します.
   * [coreViewNumberOfMessages API](https://www.zaproxy.org/docs/api/#coreviewnumberofmessages) を実行します.
   *
   * @param url フィルタリング対象の URL
   * @returns 履歴の数
   */
  public async getNumberOfMessages(url: string): Promise<number> {
    const result = await this.zaproxy.core.numberOfMessages(url);
    return JSON.parse(result.numberOfMessages);
  }

  /**
   * URLに該当する履歴({@link HttpMessage})の一覧を取得します.
   * [coreViewMessages API](https://www.zaproxy.org/docs/api/#coreviewmessages) を実行します.
   *
   * @param url フィルタリング対象の URL
   * @param start ページネーションの開始位置
   * @param count 取得数
   * @returns 履歴({@link HttpMessage})の配列
   */
  public async getMessages(url: string, start?: number, count?: number): Promise<HttpMessage[]> {
    const result = await this.zaproxy.core.messages(url, start, count);
    return result.messages;
  }

  /**
   * URLに該当する最終の履歴({@link HttpMessage})を取得します.
   * [coreViewMessages API](https://www.zaproxy.org/docs/api/#coreviewmessages) を実行します.
   *
   * @param url フィルタリング対象の URL
   * @returns 履歴({@link HttpMessage})
   * @throws ZapClientError
   */
  public async getLastMessage(url: string): Promise<HttpMessage> {
    const result = await this.getMessages(url, await this.getNumberOfMessages(url), 10);
    const message = result.pop();
    if (message === undefined) {
      throw new ZapClientError('Invalid response');
    }

    return message;
  }

  /**
   * ユーザーを指定してアクティブスキャンを実行します.
   * [ascanActionScanAsUser API](https://www.zaproxy.org/docs/api/#ascanactionscanasuser) を実行します.
   *
   * 実行するためには、予め対象のURL及び `postData` が履歴に記録されている必要があります.
   * @param url 実行対象のURL
   * @param contextId 実行対象のコンテキストID
   * @param userId ログインするユーザーID
   * @param recurse 再帰的に実行する場合 true
   * @param scanPolicyName スキャンポリシー名. デフォルト値を使用する場合は null
   * @param method 実行対象の HTTPメソッド
   * @param postData 実行対象のリクエストボディー. method=POST の場合のみ使用する.
   * @returns スキャンID
   */
  public async activeScanAsUser(url: string, contextId: number, userId: number, recurse?: boolean, scanPolicyName?: string | null, method?: 'GET' | 'POST' | 'PUT' | 'DELETE', postData?: string | null): Promise<number> {
    const result = await this.zaproxy.ascan.scanAsUser(url, contextId, userId, recurse ?? false, scanPolicyName ?? null, method ?? 'GET', postData ?? null);
    return result.scan;
  }

  /**
   * アクティブスキャンを実行します.
   * [ascanActionScan API](https://www.zaproxy.org/docs/api/#ascanactionscan) を実行します.
   *
   * 実行するためには、予め対象のURL及び `postData` が履歴に記録されている必要があります.
   *
   * @param url 実行対象のURL
   * @param recurse 再帰的に実行する場合 true
   * @param inScopeOnly スコープを限定する場合 true. contextId が指定された場合は無視されます.
   * @param scanPolicyName スキャンポリシー名. デフォルト値を使用する場合は null
   * @param method 実行対象の HTTPメソッド
   * @param contextId 実行対象のコンテキストID
   * @param postData 実行対象のリクエストボディー. method=POST の場合のみ使用する.
   * @returns スキャンID
   */
  public async activeScan(url: string, recurse?: boolean, inScopeOnly?: boolean, scanPolicyName?: string | null, method?: 'GET' | 'POST' | 'PUT' | 'DELETE', postData?: string | null, contextId?: number | null): Promise<number> {
    const result = await this.zaproxy.ascan.scan(url, recurse ?? false, inScopeOnly ?? true, scanPolicyName ?? null, method ?? 'GET', postData ?? null, contextId ?? null)
    return result.scan;
  }

  /**
   * 実行中のスキャンステータスを返します.
   * [ascanViewStatus API](https://www.zaproxy.org/docs/api/#zap-api-ascan) を実行します
   * @param scanId 対象のスキャンID
   * @returns スキャンステータス
   */
  public async getActiveScanStatus(scanId: number): Promise<number> {
    const result = await this.zaproxy.ascan.status(scanId);
    return result.status;
  }

  /**
   * セッションのスナップショットを保存します.
   * [coreActionSnapshotSession API](https://www.zaproxy.org/docs/api/#coreactionsnapshotsession) を実行します.
   */
  public async snapshotSession(): Promise<void> {
    await this.zaproxy.core.snapshotSession();
  }

  /**
   * セッションを開始します.
   *
   * 指定したコンテキストに応じたセッションを開始します.
   * ログインが有効なコンテキストの場合は Forced user mode を有効にします.
   *
   * @param contextType セッションで使用するコンテキスト
   * @param sessionName セッション名. ここで指定したセッション名で `/zap/wrk/sessions` 以下にセッションファイルを保存します
   */
  public async startSession(contextType: ContextType, sessionName: string): Promise<void> {
    await this.setMode(Mode.Protect);
    await this.newSession(`/zap/wrk/sessions/${sessionName}`, true);
    await this.importContext(contextType);

    switch (contextType) {
      case ContextType.Admin:
      case ContextType.FrontLogin:
        if (!await this.isForcedUserModeEnabled()) {
          await this.setForcedUserModeEnabled();
        }
        break;

      default:
      case ContextType.FrontGuest:
    }
  }

  /**
   * スキャン結果のアラートの配列を取得します.
   * [coreViewAlerts API](https://www.zaproxy.org/docs/api/#coreviewalerts) を実行します.
   *
   * @param url スキャン対象のURL
   * @param start ページネーションの開始位置
   * @param count 取得数
   * @param riskid 絞り込み対象のリスクレベルの列挙型
   * @returns アラートの配列
   */
  public async getAlerts(url: string, start?: number, count?:number, riskid?: Risk): Promise<Alert[]> {
    const result = await this.zaproxy.core.alerts(url, start, count, riskid);
    return result.alerts;
  }
}

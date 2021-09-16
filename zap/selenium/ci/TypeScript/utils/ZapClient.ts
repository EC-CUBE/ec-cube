const ClientApi = require('zaproxy');
export const Mode = {
  Safe: 'safe',
  Protect: 'protect',
  Standard: 'standard',
  // Attack: 'attack' denger!!
} as const;
type Mode = typeof Mode[keyof typeof Mode];

export const ContextType = {
  FrontLogin: 'front_login.context',
  FrontGuest: 'front_guest.context',
  Admin: 'admin.context'
} as const;
type ContextType = typeof ContextType[keyof typeof ContextType];

export const Risk = {
  Informational: 0,
  Low: 1,
  Medium: 2,
  High: 3
} as const;
type Risk = typeof Risk[keyof typeof Risk];

export class ZapClient {

  private apiKey: string | null;
  private proxy: string;
  private readonly zaproxy;

  constructor(proxy: string, apiKey?: string | null) {
    this.proxy = proxy;
    this.apiKey = apiKey != undefined ? apiKey : null;
    this.zaproxy = new ClientApi({
      apiKey: this.apiKey,
      proxy: this.proxy
    });
  }

  public async setMode(mode: Mode): Promise<void> {
    await this.zaproxy.core.setMode(mode);
  }

  public async newSession(name: string, override: boolean): Promise<void> {
    await this.zaproxy.core.newSession(name, override);
  }

  public async importContext(contextType: ContextType): Promise<void> {
    await this.zaproxy.context.importContext('/zap/wrk/' + contextType);
  }

  public async isForcedUserModeEnabled(): Promise<boolean> {
    const result = await this.zaproxy.forcedUser.isForcedUserModeEnabled();
    return JSON.parse(result.forcedModeEnabled);
  }

  public async setForcedUserModeEnabled(bool?: boolean): Promise<void> {
    await this.zaproxy.forcedUser.setForcedUserModeEnabled(bool ?? true);
  }

  public async sendRequest(request: string, followRedirects?: boolean): Promise<any> {
    const result = await this.zaproxy.core.sendRequest(request, followRedirects ?? false);
    return result.sendRequest;
  }

  public async getNumberOfMessages(url: string): Promise<number> {
    const result = await this.zaproxy.core.numberOfMessages(url);
    return JSON.parse(result.numberOfMessages);
  }

  public async getMessages(url: string, start?: number, count?: number): Promise<Array<any>> {
    const result = await this.zaproxy.core.messages(url, start, count);
    return result.messages;
  }

  public async getLastMessage(url: string): Promise<any> {
    const result = await this.getMessages(url, await this.getNumberOfMessages(url), 10);
    return result.pop();
  }

  public async activeScanAsUser(url: string, contextId: number, userId: number, recurse?: boolean, scanPolicyName?: string | null, method?: 'GET' | 'POST' | 'PUT' | 'DELETE', postData?: string | null): Promise<number> {
    const result = await this.zaproxy.ascan.scanAsUser(url, contextId, userId, recurse ?? false, scanPolicyName ?? null, method ?? 'GET', postData ?? null);
    return result.scan;
  }

  public async activeScan(url: string, recurse?: boolean, inScopeOnly?: boolean, scanPolicyName?: string | null, method?: 'GET' | 'POST' | 'PUT' | 'DELETE', postData?: string | null, contextId?: number | null): Promise<number> {
    const result = await this.zaproxy.ascan.scan(url, recurse ?? false, inScopeOnly ?? true, scanPolicyName ?? null, method ?? 'GET', postData ?? null, contextId ?? null)
    return result.scan;
  }

  public async getActiveScanStatus(scanId: number): Promise<number> {
    const result = await this.zaproxy.ascan.status(scanId);
    return result.status;
  }

  public async snapshotSession(): Promise<void> {
    await this.zaproxy.core.snapshotSession();
  }

  public async getAlerts(url: string, start?: number, count?:number, riskid?: Risk): Promise<Array<any>> {
    const result = await this.zaproxy.core.alerts(url, start, count, riskid);
    return result.alerts;
  }
}

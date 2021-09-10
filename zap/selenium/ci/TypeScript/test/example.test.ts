import { Builder, By, Capabilities, Key, until, ProxyConfig } from 'selenium-webdriver'
const ClientApi = require('zaproxy');
const zaproxy = new ClientApi({
  apiKey: null,
  proxy: 'http://127.0.0.1:8090'
});

const proxy : ProxyConfig = {
  proxyType: 'manual',
  httpProxy: 'localhost:8090',
  sslProxy: 'localhost:8090'
};
const capabilities = Capabilities.chrome();
jest.setTimeout(6000000);

capabilities.set('chromeOptions', {
  args: [
    '--headless',
    '--disable-gpu',
    '--window-size=1024,768'
  ],
  w3c: false
})
  .setAcceptInsecureCerts(true)
  .setProxy(proxy);

const baseURL = 'https://ec-cube';

test('example', async () => {
  const driver = await new Builder()
    .withCapabilities(capabilities)
    .build();

  try {
    await zaproxy.core.setMode('protect');
    await zaproxy.core.newSession('/zap/wrk/front_login_contact', true);
    await zaproxy.context.importContext('/zap/wrk/front_login.context');

    if (!await zaproxy.forcedUser.isForcedUserModeEnabled()) {
      await zaproxy.forcedUser.setForcedUserModeEnabled();
      expect(await zaproxy.forcedUser.isForcedUserModeEnabled()).toBeTruthy();
    }

    await driver.get(baseURL + '/contact');
    const title = await driver.wait(
      until.elementLocated(By.className('ec-pageHeader'))
      , 10000).getText();
    expect(title).toBe('お問い合わせ');

    await driver.findElement(By.id('contact_name_name01')).sendKeys('石');
    await driver.findElement(By.id('contact_name_name02')).sendKeys('球部');
    await driver.findElement(By.id('contact_contents')).sendKeys('お問い合わせ入力');
    //expect(await driver.findElement(By.id('contact_address_addr01')).getAttribute('value')).toBe('333');

    await driver.findElement(By.xpath('//*[@id="page_contact"]/div[1]/div[2]/div/div/div[2]/div/form/div[2]/div/div/button')).click();

    const numberOfMessagesResult = await zaproxy.core.numberOfMessages(baseURL + '/contact');
    const messages = await zaproxy.core.messages(baseURL + '/contact', numberOfMessagesResult.numberOfMessages, 10);
    const requestBody = messages.messages.pop().requestBody;

    const scanResult = await zaproxy.ascan.scanAsUser(baseURL + '/contact', 2, 110, false, null, 'POST', requestBody);

    let progress = async () => {
      const status = await zaproxy.ascan.status(scanResult.scan);
      return status.status;
    }

    await intervalRepeater(progress, 5000);

    await zaproxy.core.snapshotSession();
    const alertsResult = await zaproxy.core.alerts(baseURL);
    alertsResult.alerts.forEach((alert: any) => {
      if (alert.risk == 'High') {
        console.log(alert);
        throw new Error(alert.name);
      }
    });
  } finally {
    driver && await driver.quit()
  }
});

const sleep = (msec: number) => new Promise(resolve => setTimeout(resolve, msec));
const intervalRepeater = async (callback: any, interval: number) => {
  let progress = await callback();

  while (progress < 100) {
    progress = await callback();
    console.log(`Active Scan progress : ${progress}%`);
    await sleep(interval);
  }
}

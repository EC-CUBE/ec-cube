import { Builder, By, Key, until } from 'selenium-webdriver'
import { ZapClient, Mode, ContextType, Risk } from '../utils/ZapClient';
import { intervalRepeater } from '../utils/Progress';
import { SeleniumCapabilities } from '../utils/SeleniumCapabilities';
const zapClient = new ZapClient('http://127.0.0.1:8090');

jest.setTimeout(6000000);

const baseURL = 'https://ec-cube';
const url = baseURL + '/contact';

beforeAll(async () => {
  await zapClient.setMode(Mode.Protect);
  await zapClient.newSession('/zap/wrk/sessions/front_login_contact', true);
  await zapClient.importContext(ContextType.FrontLogin);

  if (!await zapClient.isForcedUserModeEnabled()) {
    await zapClient.setForcedUserModeEnabled();
    expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
  }
});

afterEach(async () => {
  await zapClient.snapshotSession();
});

test('contact', async () => {
  const driver = await new Builder()
    .withCapabilities(SeleniumCapabilities)
    .build();

  try {
    await driver.get(url);
    const title = await driver.wait(
      until.elementLocated(By.className('ec-pageHeader'))
      , 10000).getText();
    expect(title).toBe('お問い合わせ');

    await driver.findElement(By.id('contact_name_name01')).sendKeys('石');
    await driver.findElement(By.id('contact_name_name02')).sendKeys('球部');
    await driver.findElement(By.id('contact_contents')).sendKeys('お問い合わせ入力');
    expect(await driver.findElement(By.id('contact_email')).getAttribute('value')).toBe('zap_user@example.com');
    await driver.findElement(By.xpath('//*[@id="page_contact"]/div[1]/div[2]/div/div/div[2]/div/form/div[2]/div/div/button')).click();

    const message = await zapClient.getLastMessage(url);
    const scanId = await zapClient.activeScanAsUser(url, 2, 110, false, null, 'POST', message.requestBody);

    await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000);

    await zapClient.getAlerts(url, 0, 1, Risk.High)
    .then(alerts => alerts.forEach((alert: any) => {
      throw new Error(alert.name);
    }));;
  } finally {
    driver && await driver.quit()
  }
});

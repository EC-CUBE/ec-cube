import { Builder, By, until } from 'selenium-webdriver'
import { ZapClient, Mode, ContextType, Risk } from '../../utils/ZapClient';
import { intervalRepeater } from '../../utils/Progress';
import { SeleniumCapabilities } from '../../utils/SeleniumCapabilities';
const zapClient = new ZapClient('http://127.0.0.1:8090');

jest.setTimeout(6000000);

const baseURL = 'https://ec-cube/admin';
const url = baseURL + '/order/4/mail';

beforeAll(async () => {
  await zapClient.setMode(Mode.Protect);
  await zapClient.newSession('/zap/wrk/sessions/admin_order_mail', true);
  await zapClient.importContext(ContextType.Admin);

  if (!await zapClient.isForcedUserModeEnabled()) {
    await zapClient.setForcedUserModeEnabled();
    expect(await zapClient.isForcedUserModeEnabled()).toBeTruthy();
  }
});

test('受注管理>メール通知 - GET', async () => {
  const driver = await new Builder()
    .withCapabilities(SeleniumCapabilities)
    .build();

  try {
    await driver.get(url);
    const title = await driver.wait(
      until.elementLocated(By.className('c-pageTitle__title'))
      , 10000).getText();
    expect(title).toBe('メール通知');

    const scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'GET');

    await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000);

    await zapClient.getAlerts(url, 0, 1, Risk.High)
      .then(alerts => alerts.forEach((alert: any) => {
        throw new Error(alert.name);
      }));
  } finally {
    driver && await driver.quit()
  }
});

test('受注管理>メール通知(確認ページ) - POST', async () => {
  const driver = await new Builder()
    .withCapabilities(SeleniumCapabilities)
    .build();

  try {
    await driver.get(url);
    const title = await driver.wait(
      until.elementLocated(By.className('c-pageTitle__title'))
      , 10000).getText();
    expect(title).toBe('メール通知');

    await driver.findElement(By.xpath('//*[@id="template-change"]/option[2]')).click();
    const subject = await driver.wait(
      until.elementLocated(By.xpath('//*[@id="admin_order_mail_mail_subject"]'))
      , 10000).getAttribute('value');
    expect(subject).toBe('ご注文ありがとうございます');

    await driver.findElement(By.xpath('//*[@id="order-mail-form"]/div[2]/div/div/div[2]/div/div/button')).click();

    const message = await zapClient.getLastMessage(url);
    const scanId = await zapClient.activeScanAsUser(url, 2, 55, false, null, 'POST', message.requestBody);

    await intervalRepeater(async () => await zapClient.getActiveScanStatus(scanId), 5000);

    await zapClient.getAlerts(url, 0, 1, Risk.High)
      .then(alerts => alerts.forEach((alert: any) => {
        throw new Error(alert.name);
      }));
  } finally {
    driver && await driver.quit()
  }
});

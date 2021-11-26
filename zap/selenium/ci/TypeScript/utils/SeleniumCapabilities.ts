import { Capabilities, ProxyConfig } from 'selenium-webdriver'
const proxy : ProxyConfig = {
  proxyType: 'manual',
  httpProxy: 'localhost:8090',
  sslProxy: 'localhost:8090'
};

export const SeleniumCapabilities = Capabilities.chrome();
SeleniumCapabilities.set('chromeOptions', {
  args: [
    '--headless',
    '--disable-gpu',
    '--window-size=1024,768'
  ],
  w3c: false
})
  .setAcceptInsecureCerts(true)
  .setProxy(proxy);

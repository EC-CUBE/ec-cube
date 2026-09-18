import { defineConfig, devices } from '@playwright/test';
import path from 'path';

const baseURL = process.env.BASE_URL || 'http://127.0.0.1:8000';

export default defineConfig({
  globalSetup: './global-setup.ts',
  testDir: './tests',
  fullyParallel: false,
  workers: 1,
  retries: process.env.CI ? 1 : 0,
  timeout: 600_000,
  expect: {
    timeout: 30_000,
  },
  outputDir: './test-results',
  reporter: process.env.CI
    ? [['html', { open: 'never' }], ['github']]
    : [['html', { open: 'on-failure' }]],
  use: {
    baseURL,
    trace: process.env.CI ? 'on-first-retry' : 'off',
    screenshot: 'only-on-failure',
    locale: 'ja-JP',
    viewport: { width: 1680, height: 3000 },
    actionTimeout: 30_000,
    navigationTimeout: 60_000,
  },
  projects: [
    {
      name: 'setup',
      testMatch: /auth\.setup\.ts/,
      testDir: './fixtures',
      use: {
        ...devices['Desktop Chrome'],
        storageState: undefined,
        // The permission-lanes environment runs as APP_ENV=prod behind Apache with a
        // self-signed certificate (see the `permission-lanes-tests` project below).
        // Logging in there requires HTTPS, so accept the certificate here too.
        // This has no effect on the plain HTTP runs used by the other projects.
        ignoreHTTPSErrors: true,
      },
    },
    {
      name: 'plugin-tests',
      testMatch: /plugin-.*\.spec\.ts/,
      dependencies: ['setup'],
      use: {
        ...devices['Desktop Chrome'],
        storageState: path.join(__dirname, '.auth', 'admin.json'),
      },
    },
    {
      name: 'admin-tests',
      testMatch: /admin-.*\.spec\.ts/,
      dependencies: ['setup'],
      use: {
        ...devices['Desktop Chrome'],
        storageState: path.join(__dirname, '.auth', 'admin.json'),
      },
    },
    {
      name: 'front-tests',
      testMatch: /(front-|deny).*\.spec\.ts/,
      dependencies: ['setup'],
      use: {
        ...devices['Desktop Chrome'],
        // Front tests don't need admin auth, use empty state
        storageState: { cookies: [], origins: [] },
      },
    },
    {
      name: 'mcp-tests',
      testMatch: /mcp\.spec\.ts/,
      dependencies: ['setup'],
      use: {
        ...devices['Desktop Chrome'],
        storageState: path.join(__dirname, '.auth', 'admin.json'),
      },
    },
    {
      // Runs against the environment started by docker-compose.permission-lanes.yml,
      // where the web server and the CLI have different uids. Kept out of the
      // `admin-tests` glob on purpose: with ECCUBE_RESTRICT_FILE_UPLOAD=1 the admin
      // screens that write to lane S are read-only, so the regular admin specs cannot
      // pass there.
      name: 'permission-lanes-tests',
      testMatch: /permission-lanes\.spec\.ts/,
      dependencies: ['setup'],
      use: {
        ...devices['Desktop Chrome'],
        storageState: path.join(__dirname, '.auth', 'admin.json'),
        // That environment is APP_ENV=prod, whose session cookie uses SameSite=None:
        // browsers drop it without the Secure flag, so the admin has to be reached over
        // HTTPS. Apache serves it with the snakeoil certificate baked into the image.
        ignoreHTTPSErrors: true,
      },
    },
    {
      name: 'install-tests',
      testMatch: /install-.*\.spec\.ts/,
      // The installer runs before EC-CUBE is installed: there is no admin
      // account yet, so this project must not depend on the `setup` project.
      use: {
        ...devices['Desktop Chrome'],
        storageState: { cookies: [], origins: [] },
        // Logging into the admin after the install requires HTTPS: once
        // installed the app runs as APP_ENV=prod, whose session cookie uses
        // SameSite=None, which browsers drop without the Secure flag.
        // The server is a local dev server (symfony serve) with a self-signed
        // certificate, so accept it instead of relying on the CA being
        // registered in the browser trust store.
        ignoreHTTPSErrors: true,
        // Keep the trace on failure so CI can be diagnosed (the installer is
        // not idempotent, so it is run with --retries=0 and the failing run is
        // the only run).
        trace: 'retain-on-failure',
      },
    },
  ],
});

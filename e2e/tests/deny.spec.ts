import { test, expect } from '@playwright/test';

/**
 * 機密ファイルへのアクセス拒否テスト (CL01DenyCest 相当)
 * Docker コンテナ上の EC-CUBE に対して実行する。
 * BASE_URL は Docker コンテナの URL (例: http://127.0.0.1:8080)
 */
test.describe('Deny check (CL01)', () => {
  const denyFiles = [
    { title: 'varが公開されていないか', file: 'var/cache/prod/annotations.map' },
    { title: '.envが公開されていないか', file: '.env' },
    { title: 'vendorが公開されていないか', file: 'vendor/symfony/config/README.md' },
    { title: 'codeceptionが公開されていないか', file: 'codeception/acceptance/config.ini' },
  ];

  for (const { title, file } of denyFiles) {
    test(title, async ({ request }) => {
      const response = await request.get(`/${file}`);
      expect(response.status()).toBe(403);
    });
  }
});

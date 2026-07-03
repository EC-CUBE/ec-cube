import { test as base, expect } from '@playwright/test';
import { createDbClient, DbClient } from '../helpers/db-client';
import { compressPlugin, emptyDir } from '../helpers/tar-helper';
import path from 'path';

export { expect };

export interface PluginTestConfig {
  projectDir: string;
  adminRoute: string;
  reposDir: string;
  pluginDataDir: string;
}

type PluginFixtures = {
  db: DbClient;
  config: PluginTestConfig;
  compressPluginToRepos: (pluginDirName: string) => Promise<void>;
  compressPluginToDataDir: (pluginDirName: string) => Promise<void>;
};

export const test = base.extend<PluginFixtures>({
  db: async ({}, use) => {
    const databaseUrl = process.env.DATABASE_URL;
    if (!databaseUrl) {
      throw new Error('DATABASE_URL environment variable is required');
    }
    const client = await createDbClient(databaseUrl);
    await use(client);
    await client.close();
  },

  config: async ({}, use) => {
    const config: PluginTestConfig = {
      projectDir: process.env.ECCUBE_PROJECT_DIR || path.resolve(__dirname, '..', '..'),
      adminRoute: process.env.ECCUBE_ADMIN_ROUTE || 'admin',
      reposDir: process.env.REPOS_DIR || path.resolve(__dirname, '..', '..', 'repos'),
      pluginDataDir: process.env.PLUGIN_DATA_DIR || path.resolve(__dirname, '..', '..', 'codeception', '_data', 'plugins'),
    };
    await use(config);
  },

  compressPluginToRepos: async ({ config }, use) => {
    // テスト開始前に repos をクリア
    emptyDir(config.reposDir);
    await use(async (pluginDirName: string) => {
      await compressPlugin(pluginDirName, config.reposDir, config.pluginDataDir);
    });
  },

  compressPluginToDataDir: async ({ config }, use) => {
    await use(async (pluginDirName: string) => {
      await compressPlugin(pluginDirName, config.pluginDataDir, config.pluginDataDir);
    });
  },
});

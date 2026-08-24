import { test as dbTest, expect } from './db-test';
import { ADMIN_ROUTE } from '../config/default.config';
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
  config: PluginTestConfig;
  compressPluginToRepos: (pluginDirName: string) => Promise<void>;
  compressPluginToDataDir: (pluginDirName: string) => Promise<void>;
};

// `db` は fixtures/db-test.ts から継承する（定義を 2 箇所に持たない）
export const test = dbTest.extend<PluginFixtures>({
  config: async ({}, use) => {
    const config: PluginTestConfig = {
      projectDir: process.env.ECCUBE_PROJECT_DIR || path.resolve(__dirname, '..', '..'),
      adminRoute: ADMIN_ROUTE,
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

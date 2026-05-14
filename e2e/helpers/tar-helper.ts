import * as tar from 'tar';
import path from 'path';
import fs from 'fs';

/**
 * codeception/_data/plugins/ 配下のプラグインディレクトリを .tgz に圧縮する。
 * Codeception の AcceptanceTester::compressPlugin() と同等の処理。
 */
export async function compressPlugin(pluginDirName: string, destDir: string, pluginDataDir: string): Promise<void> {
  const sourceDir = path.resolve(pluginDataDir, pluginDirName);
  const tgzPath = path.join(destDir, `${pluginDirName}.tgz`);

  // 既存の .tgz を削除
  if (fs.existsSync(tgzPath)) {
    fs.unlinkSync(tgzPath);
  }

  await tar.create(
    {
      gzip: true,
      file: tgzPath,
      cwd: sourceDir,
    },
    fs.readdirSync(sourceDir)
  );
}

/**
 * destDir の中身を空にする。
 * Codeception の FileSystem::doEmptyDir('repos') と同等。
 */
export function emptyDir(dirPath: string): void {
  if (!fs.existsSync(dirPath)) {
    fs.mkdirSync(dirPath, { recursive: true });
    return;
  }
  for (const file of fs.readdirSync(dirPath)) {
    const filePath = path.join(dirPath, file);
    fs.rmSync(filePath, { recursive: true, force: true });
  }
}

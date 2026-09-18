import fs from 'fs';
import path from 'path';

/**
 * PHP proxy entity ファイル内に指定された Trait クラス名が含まれるか確認する。
 * Codeception の Abstract_Plugin::traitExists() と同等。
 *
 * @param projectDir EC-CUBE プロジェクトルート (ECCUBE_PROJECT_DIR)
 * @param traitClassName Trait の完全修飾クラス名 (例: "Plugin\\Horizon\\Entity\\CartTrait")
 * @param proxyTarget proxy entity のパス (例: "src/Eccube/Entity/Cart")
 */
export function traitExists(projectDir: string, traitClassName: string, proxyTarget: string): boolean {
  const filePath = path.join(projectDir, 'app', 'proxy', 'entity', `${proxyTarget}.php`);
  try {
    const content = fs.readFileSync(filePath, 'utf-8');
    return content.includes(traitClassName);
  } catch {
    return false;
  }
}

/**
 * ファイルの存在確認。
 * Codeception の assertFileExists / assertFileNotExists と同等。
 */
export function fileExists(filePath: string): boolean {
  return fs.existsSync(filePath);
}

<?php

declare(strict_types=1);

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Service\Content;

use Eccube\Exception\ContentWriteException;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * テンプレートとレコードを対にしたまま削除する.
 *
 * ページ・ブロック・メールテンプレートで共通の削除手順をまとめる.
 */
trait TemplateRemovalTrait
{
    /**
     * テンプレートを一時退避したうえで $commit (DB の削除) を実行する.
     *
     * ファイルを先に消すと $commit が失敗したときにレコードだけが残り, 参照先の無いテンプレートに
     * なる (画面が「Unable to find template」で落ちる). 逆に後で消すと, 削除に失敗したときに
     * レコードだけが消える. どちらも対を壊すため, 退避 -> DB 削除 -> 退避ファイルの削除 の順にする.
     *
     * @param list<string>    $paths  削除対象のテンプレート (存在しないものは無視する)
     * @param callable():void $commit DB からレコードを削除する処理
     *
     * @return list<string> 実際に削除したパス
     *
     * @throws ContentWriteException テンプレートを退避できない場合
     */
    private function removeTemplatesAround(array $paths, callable $commit): array
    {
        /** @var array<string, string> $staged 退避元 => 退避先 */
        $staged = [];

        foreach ($paths as $path) {
            if (!is_file($path)) {
                continue;
            }

            // 同じディレクトリへ退避する. 拡張子が .twig でなくなるため twig の探索には掛からない
            $stagedPath = $path.'.removing-'.bin2hex(random_bytes(8));
            try {
                $this->filesystem->rename($path, $stagedPath);
            } catch (IOException $e) {
                $this->restoreStagedTemplates($staged);

                throw ContentWriteException::forRemove($path, $e);
            }
            $staged[$path] = $stagedPath;
        }

        try {
            $commit();
        } catch (\Throwable $e) {
            $this->restoreStagedTemplates($staged);

            throw $e;
        }

        // ここから先の失敗はレコードが消えた後のため対は壊れない. 残るのは退避ファイルだけで,
        // 拡張子が .twig ではないので表示にも影響しない
        foreach ($staged as $stagedPath) {
            try {
                $this->filesystem->remove($stagedPath);
            } catch (IOException) {
                // 退避ファイルの後始末に失敗しても削除自体は完了している
            }
        }

        return array_keys($staged);
    }

    /**
     * 退避したテンプレートを元のパスへ戻す.
     *
     * @param array<string, string> $staged 退避元 => 退避先
     */
    private function restoreStagedTemplates(array $staged): void
    {
        foreach ($staged as $path => $stagedPath) {
            try {
                $this->filesystem->rename($stagedPath, $path, true);
            } catch (IOException) {
                // 戻せない場合は退避ファイルを残す. 消してしまうと手動でも復旧できなくなる
            }
        }
    }
}

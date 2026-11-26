<?php

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

namespace Eccube\Service\AgentCommerce\CheckoutSession;

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Eccube\Entity\CheckoutSession;

/**
 * CheckoutSession の状態遷移を 1 トランザクション + 行ロックで直列化する.
 *
 * complete / update / cancel / 期限切れ回収は、いずれも「現在のステータスを読んで分岐し、書き換える」
 * 処理である。リクエスト冒頭で読んだステータスのまま分岐すると、同じセッションへの並行リクエストが
 * 双方とも同じ状態を見て在庫引当や与信を二重に実行する。本クラスはトランザクション開始直後に
 * セッション行を `SELECT ... FOR UPDATE` で読み直し、後続のリクエストを先行のコミットまで待たせる。
 *
 * SQLite は行ロック構文を持たないため、ロックは付与されない (書き込みはデータベース単位で直列化される)。
 */
class AgentCheckoutSessionLocker
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * セッション行をロックしたトランザクション内で $callback を実行し、結果を返す.
     *
     * $callback の後に flush してからコミットする。例外は巻き戻してそのまま伝播させる。
     * ロック取得時にセッションと関連する Order は DB の値で読み直されるため、呼び出し前の
     * 未 flush の変更は失われる (呼び出し側はロック後に変更すること)。
     *
     * @template T
     *
     * @param callable(CheckoutSession): T $callback
     *
     * @return T
     */
    public function transactional(CheckoutSession $session, callable $callback): mixed
    {
        $connection = $this->entityManager->getConnection();
        $connection->beginTransaction();
        try {
            $this->entityManager->refresh($session, LockMode::PESSIMISTIC_WRITE);
            // セッションの読み直しは関連 (Order) を読み直さない (cascade refresh が無い)。並行リクエストが
            // 支払方法等を変えていれば、手元の Order は古いまま。ロック後の値で判定するため Order も読み直す。
            $order = $session->getOrder();
            if ($order !== null) {
                $this->entityManager->refresh($order);
            }
            $result = $callback($session);
            $this->entityManager->flush();
            $connection->commit();

            return $result;
        } catch (\Throwable $e) {
            $connection->rollBack();

            throw $e;
        }
    }
}

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

namespace Eccube\Tests\Service\Mcp;

use Eccube\Entity\Member;
use League\Bundle\OAuth2ServerBundle\Entity\AccessToken as AccessTokenEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Client as ClientEntity;
use League\Bundle\OAuth2ServerBundle\Entity\Scope as ScopeEntity;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Manager\ClientManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken as AccessTokenModel;
use League\Bundle\OAuth2ServerBundle\Model\Client as ClientModel;
use League\Bundle\OAuth2ServerBundle\Model\ClientInterface;
use League\Bundle\OAuth2ServerBundle\ValueObject\Grant;
use League\Bundle\OAuth2ServerBundle\ValueObject\Scope as ScopeValue;
use League\OAuth2\Server\CryptKey;

/**
 * `/admin/mcp` へ投げる Bearer (有効な MCP アクセストークンの JWT) を発行するテスト用ヘルパ。
 *
 * OAuth2 client と access_token を fixture として DB に保存し、 Api44 の private key で JWT を発行する
 * (= 通常の `/token` エンドポイント経由の応答と同等の Bearer)。 Api44 install + enable 状態が前提。
 * EccubeTestCase を継承したテストで use する前提 (`static::getContainer()` に依存)。
 */
trait IssuesMcpAccessTokenTrait
{
    private function ensureMcpClient(string $clientId): ClientInterface
    {
        $clientManager = static::getContainer()->get(ClientManagerInterface::class);
        $existing = $clientManager->find($clientId);
        if (null !== $existing) {
            return $existing;
        }

        $client = new ClientModel('MCP test', $clientId, null);
        $client->setScopes(new ScopeValue('mcp:product:read'));
        $client->setGrants(new Grant('authorization_code'), new Grant('refresh_token'));
        $clientManager->save($client);

        return $client;
    }

    /**
     * AccessToken Model を保存し、 同じ identifier の JWT を発行して返す。
     */
    private function issueMcpJwt(string $identifier, ClientInterface $client, Member $member): string
    {
        $expiry = new \DateTimeImmutable('+1 hour');
        // MemberProvider::loadUserByIdentifier は login_id で検索する。 sub claim には login_id を入れる。
        $userIdentifier = $member->getUsername();

        // 1) Model を DB に保存 (league の isAccessTokenRevoked 照合先)
        $tokenModel = new AccessTokenModel($identifier, $expiry, $client, $userIdentifier, []);
        static::getContainer()->get(AccessTokenManagerInterface::class)->save($tokenModel);

        // 2) Bearer 用の JWT を発行 (通常の /token エンドポイント経由の応答と同等)
        $tokenEntity = new AccessTokenEntity();
        $tokenEntity->setIdentifier($identifier);
        $tokenEntity->setExpiryDateTime($expiry);
        $tokenEntity->setUserIdentifier($userIdentifier);

        $clientEntity = new ClientEntity();
        $clientEntity->setIdentifier($client->getIdentifier());
        $tokenEntity->setClient($clientEntity);

        // /admin/mcp の access_control は最低 1 つの mcp read scope を要求するため付与する。
        $scopeEntity = new ScopeEntity();
        $scopeEntity->setIdentifier('mcp:product:read');
        $tokenEntity->addScope($scopeEntity);

        $privateKeyPath = static::getContainer()->getParameter('kernel.project_dir').'/app/PluginData/Api44/oauth/private.key';
        \assert(\is_string($privateKeyPath));
        // テスト環境では private key の permission チェックを無効化 (CI / docker 環境差異への対応)
        $tokenEntity->setPrivateKey(new CryptKey($privateKeyPath, null, keyPermissionsCheck: false));

        return $tokenEntity->toString();
    }
}

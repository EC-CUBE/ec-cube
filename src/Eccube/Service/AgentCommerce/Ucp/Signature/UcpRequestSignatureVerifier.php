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

namespace Eccube\Service\AgentCommerce\Ucp\Signature;

use Eccube\Service\AgentCommerce\Exception\UcpSignatureException;
use phpseclib3\Crypt\EC\PublicKey;
use phpseclib3\Crypt\PublicKeyLoader;
use Symfony\Component\HttpFoundation\Request;

/**
 * UCP インバウンドリクエストの RFC 9421 HTTP Message Signatures 検証.
 *
 * UCP の標準インバウンド認証であり OAuth2/eccube-api4 に依存しない。検証手順:
 * 1. `UCP-Agent` ヘッダから profile URL を解析し、ドメイン許可リストと照合する。
 * 2. (ボディを伴う場合) `Content-Digest` を生ボディの SHA-256 と照合する。
 * 3. `Signature-Input`/`Signature` を解析し、covered component から signature base を再構成する。
 * 4. エージェントの profile から `signing_keys[]` を取得し、keyid で公開鍵を特定する。
 * 5. ES256 (EC P-256) で署名を検証する。
 *
 * 署名は仕様上 MAY (代替認証も許容) のため、本検証は「署名が提示されていれば必須検証」とする
 * present-then-verify 方針。署名が無いリクエストの扱い (拒否するか) は呼び出し側 (サブスクライバ) が
 * 設定で決める。
 *
 * @see https://github.com/Universal-Commerce-Protocol/ucp UCP signatures.md (RFC 9421, ES256, Content-Digest, profile.signing_keys[])
 * @see https://www.rfc-editor.org/rfc/rfc9421
 */
class UcpRequestSignatureVerifier
{
    /**
     * @var list<string> 許可するエージェント profile ドメイン (空なら HTTPS である限り制限しない)
     */
    private readonly array $allowedDomains;

    /**
     * @param UcpProfileFetcher          $profileFetcher    エージェント公開鍵の取得元
     * @param Rfc9421SignatureBaseBuilder $signatureBaseBuilder signature base 構築
     * @param list<string>               $allowedDomains    許可ドメインのホスト名リスト
     */
    public function __construct(
        private readonly UcpProfileFetcher $profileFetcher,
        private readonly Rfc9421SignatureBaseBuilder $signatureBaseBuilder,
        array $allowedDomains = [],
    ) {
        $this->allowedDomains = array_map(strtolower(...), $allowedDomains);
    }

    /**
     * リクエストが RFC 9421 署名を提示しているか.
     */
    public function isSigned(Request $request): bool
    {
        return $request->headers->has('Signature') && $request->headers->has('Signature-Input');
    }

    /**
     * リクエストの署名を検証する. 失敗時は例外を投げる.
     *
     * @return UcpAgentHeader 検証済みのエージェント情報
     *
     * @throws UcpSignatureException 署名欠落・不正・鍵不一致・Content-Digest 不一致・許可外ドメイン
     */
    public function verify(Request $request): UcpAgentHeader
    {
        $ucpAgentValue = $request->headers->get('UCP-Agent');
        if ($ucpAgentValue === null || $ucpAgentValue === '') {
            throw new UcpSignatureException('UCP-Agent header is required.');
        }

        try {
            $agent = UcpAgentHeader::parse($ucpAgentValue);
        } catch (\InvalidArgumentException $e) {
            throw new UcpSignatureException($e->getMessage(), 0, $e);
        }

        $this->assertDomainAllowed($agent->host());

        if (!$this->isSigned($request)) {
            throw new UcpSignatureException('Signature and Signature-Input headers are required.');
        }

        $this->verifyContentDigest($request);

        [$components, $signatureParams, $keyId] = $this->parseSignatureInput((string) $request->headers->get('Signature-Input'));
        $rawSignature = $this->parseSignature((string) $request->headers->get('Signature'));

        try {
            $signatureBase = $this->signatureBaseBuilder->build($request, $components, $signatureParams);
        } catch (\InvalidArgumentException $e) {
            throw new UcpSignatureException($e->getMessage(), 0, $e);
        }

        $jwks = $this->profileFetcher->fetchSigningKeys($agent->profileUrl);
        if (!$this->verifyAgainstKeys($signatureBase, $rawSignature, $jwks, $keyId)) {
            throw new UcpSignatureException('RFC 9421 signature verification failed.');
        }

        return $agent;
    }

    private function assertDomainAllowed(string $host): void
    {
        if ($this->allowedDomains === []) {
            return;
        }

        if (!in_array($host, $this->allowedDomains, true)) {
            throw new UcpSignatureException(sprintf('Agent domain "%s" is not allowed.', $host));
        }
    }

    /**
     * Content-Digest を生ボディの SHA-256 と照合する (ボディが無ければスキップ).
     */
    private function verifyContentDigest(Request $request): void
    {
        $body = $request->getContent();
        $header = $request->headers->get('Content-Digest');

        if ($body === '' && $header === null) {
            return;
        }

        if ($header === null) {
            throw new UcpSignatureException('Content-Digest header is required for requests with a body.');
        }

        // 形式: sha-256=:<base64>:
        if (!preg_match('/sha-256=:([^:]+):/', $header, $matches)) {
            throw new UcpSignatureException('Content-Digest must use the sha-256 algorithm.');
        }

        $expected = base64_encode(hash('sha256', $body, true));
        if (!hash_equals($expected, $matches[1])) {
            throw new UcpSignatureException('Content-Digest does not match the request body.');
        }
    }

    /**
     * Signature-Input をパースして covered components / signature-params / keyid を返す.
     *
     * @return array{0: list<string>, 1: string, 2: ?string}
     */
    private function parseSignatureInput(string $headerValue): array
    {
        // 形式: <label>=("comp1" "comp2");param=...;keyid="..."。最初のラベルのみ対象とする。
        if (!preg_match('/^[A-Za-z0-9_-]+=(\(.*)$/s', trim($headerValue), $matches)) {
            throw new UcpSignatureException('Malformed Signature-Input header.');
        }

        $signatureParams = $matches[1];

        if (!preg_match('/\((.*?)\)/', $signatureParams, $listMatch)) {
            throw new UcpSignatureException('Signature-Input is missing the covered components list.');
        }

        preg_match_all('/"([^"]+)"/', $listMatch[1], $componentMatches);
        $components = $componentMatches[1];
        if ($components === []) {
            throw new UcpSignatureException('Signature-Input declares no covered components.');
        }

        $keyId = null;
        if (preg_match('/keyid="([^"]+)"/', $signatureParams, $keyMatch)) {
            $keyId = $keyMatch[1];
        }

        return [$components, $signatureParams, $keyId];
    }

    /**
     * Signature ヘッダから生の署名バイト列を取り出す.
     */
    private function parseSignature(string $headerValue): string
    {
        // 形式: <label>=:<base64>:。RFC 9421 の sf-binary は標準 base64。
        if (!preg_match('/^[A-Za-z0-9_-]+=:([^:]+):/s', trim($headerValue), $matches)) {
            throw new UcpSignatureException('Malformed Signature header.');
        }

        $raw = base64_decode($matches[1], true);
        if ($raw === false || $raw === '') {
            throw new UcpSignatureException('Signature value is not valid base64.');
        }

        return $raw;
    }

    /**
     * 候補鍵 (keyid 一致を優先) に対して ES256 検証を試みる.
     *
     * @param list<array<string, mixed>> $jwks
     */
    private function verifyAgainstKeys(string $signatureBase, string $rawSignature, array $jwks, ?string $keyId): bool
    {
        $candidates = $jwks;
        if ($keyId !== null) {
            $matched = array_values(array_filter(
                $jwks,
                static fn (array $jwk): bool => ($jwk['kid'] ?? null) === $keyId
            ));
            if ($matched !== []) {
                $candidates = $matched;
            }
        }

        foreach ($candidates as $jwk) {
            $publicKey = $this->loadPublicKey($jwk);
            if ($publicKey === null) {
                continue;
            }

            try {
                $verified = $publicKey
                    ->withSignatureFormat('IEEE')
                    ->withHash('sha256')
                    ->verify($signatureBase, $rawSignature);
            } catch (\Throwable) {
                continue;
            }

            if ($verified) {
                return true;
            }
        }

        return false;
    }

    /**
     * EC 公開鍵 JWK を phpseclib の PublicKey へ読み込む.
     *
     * @param array<string, mixed> $jwk
     */
    private function loadPublicKey(array $jwk): ?PublicKey
    {
        if (($jwk['kty'] ?? null) !== 'EC' || !isset($jwk['x'], $jwk['y'], $jwk['crv'])) {
            return null;
        }

        $json = json_encode([
            'kty' => 'EC',
            'crv' => $jwk['crv'],
            'x' => $jwk['x'],
            'y' => $jwk['y'],
        ], JSON_UNESCAPED_SLASHES);

        try {
            $key = PublicKeyLoader::load((string) $json);
        } catch (\Throwable) {
            return null;
        }

        return $key instanceof PublicKey ? $key : null;
    }
}

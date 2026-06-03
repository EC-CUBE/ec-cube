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

namespace Eccube\Tests\Service\AgentCommerce\Security;

use Eccube\Service\AgentCommerce\Security\KeyStoreInterface;
use Eccube\Service\AgentCommerce\Security\UcpMessageSigner;
use phpseclib3\Crypt\PublicKeyLoader;
use PHPUnit\Framework\TestCase;

/**
 * Layer 5 (signing) tests for UcpMessageSigner.
 *
 * UCP uses RFC 9421 HTTP Message Signatures with EC P-256 (ES256) keys. These
 * tests verify the sign/verify round trip, tamper rejection, JWK publication
 * (public coordinates only, never the private "d" parameter), and key
 * rotation via grace public keys.
 *
 * KeyStore is replaced by an in-memory stub so no filesystem/DB is touched.
 */
class UcpMessageSignerTest extends TestCase
{
    private const PURPOSE = 'ucp_signing';

    public function testSignThenVerifyRoundTrip(): void
    {
        $signer = new UcpMessageSigner($this->createKeyStore(), self::PURPOSE);
        $base = '"@method": POST'."\n".'"@path": /checkout-sessions';

        $signature = $signer->sign($base);

        self::assertNotSame('', $signature, 'sign must return a non-empty base64url signature');
        self::assertTrue($signer->verify($base, $signature), 'A signature produced by sign must verify against the same signature base');
    }

    public function testVerifyFailsOnTamperedSignatureBase(): void
    {
        $signer = new UcpMessageSigner($this->createKeyStore(), self::PURPOSE);
        $base = 'original-signature-base';

        $signature = $signer->sign($base);

        self::assertFalse($signer->verify('tampered-signature-base', $signature), 'A signature must not verify against a tampered signature base');
    }

    public function testVerifyFailsOnTamperedSignature(): void
    {
        $signer = new UcpMessageSigner($this->createKeyStore(), self::PURPOSE);
        $base = 'signature-base';

        $signature = $signer->sign($base);
        // Flip the first character to corrupt the signature while keeping it base64url-ish.
        $corrupted = ($signature[0] === 'A' ? 'B' : 'A').substr($signature, 1);

        self::assertFalse($signer->verify($base, $corrupted), 'A corrupted signature must not verify');
    }

    public function testGetPublicJwksExposesEcPublicKeyOnly(): void
    {
        $signer = new UcpMessageSigner($this->createKeyStore(), self::PURPOSE);

        $jwks = $signer->getPublicJwks();

        self::assertNotEmpty($jwks, 'getPublicJwks must return at least the current key');
        foreach ($jwks as $jwk) {
            self::assertSame('EC', $jwk['kty'], 'UCP signing keys must be EC keys (kty=EC)');
            self::assertSame('P-256', $jwk['crv'], 'UCP signing keys must use the P-256 curve');
            self::assertArrayHasKey('x', $jwk, 'EC public JWK must expose the x coordinate');
            self::assertArrayHasKey('y', $jwk, 'EC public JWK must expose the y coordinate');
            self::assertArrayNotHasKey('d', $jwk, 'Published JWK must never contain the private key parameter d');
        }
    }

    public function testGetCurrentKidIsStableAndPresentInJwks(): void
    {
        $signer = new UcpMessageSigner($this->createKeyStore(), self::PURPOSE);

        $kid = $signer->getCurrentKid();
        self::assertNotSame('', $kid, 'getCurrentKid must return a non-empty key id');

        $kids = array_map(static fn (array $jwk) => $jwk['kid'] ?? null, $signer->getPublicJwks());
        self::assertContains($kid, $kids, 'The current kid must appear among the published JWKs');
    }

    /**
     * Rotation: a signature created with the previous key must still verify on
     * a signer whose current key is the new key and whose grace set contains
     * the old public key.
     */
    public function testKeyRotationVerifiesWithGracePublicKey(): void
    {
        // Old signer with its own (auto-generated) private key.
        $oldStore = $this->createKeyStore();
        $oldSigner = new UcpMessageSigner($oldStore, self::PURPOSE);
        $base = 'rotation-signature-base';
        $oldSignature = $oldSigner->sign($base);

        $oldPrivatePem = $oldStore->read(self::PURPOSE);
        self::assertNotNull($oldPrivatePem, 'The old key store must hold the generated private key');
        $oldPublicPem = $this->derivePublicPem($oldPrivatePem);

        // New signer with a fresh key, carrying the old public key in its grace set.
        $newStore = $this->createKeyStore();
        $newSigner = new UcpMessageSigner($newStore, self::PURPOSE, [$oldPublicPem]);

        self::assertTrue(
            $newSigner->verify($base, $oldSignature),
            'A signature made with the rotated-out key must still verify while its public key is in the grace set'
        );

        // And the grace public key must be advertised in the JWKs without leaking d.
        $jwks = $newSigner->getPublicJwks();
        self::assertGreaterThanOrEqual(2, count($jwks), 'During grace, both the current and the grace public key must be published');
        foreach ($jwks as $jwk) {
            self::assertArrayNotHasKey('d', $jwk, 'No published JWK (current or grace) may contain the private parameter d');
        }
    }

    private function derivePublicPem(string $privatePem): string
    {
        $key = PublicKeyLoader::load($privatePem);

        return $key->getPublicKey()->toString('PKCS8');
    }

    /**
     * In-memory KeyStore stub: persists PEMs in an associative array keyed by
     * purpose. No filesystem or DB access.
     */
    private function createKeyStore(): KeyStoreInterface
    {
        return new class implements KeyStoreInterface {
            /** @var array<string, string> */
            private array $store = [];

            public function read(string $purpose): ?string
            {
                return $this->store[$purpose] ?? null;
            }

            public function write(string $purpose, string $pem): void
            {
                $this->store[$purpose] = $pem;
            }
        };
    }
}

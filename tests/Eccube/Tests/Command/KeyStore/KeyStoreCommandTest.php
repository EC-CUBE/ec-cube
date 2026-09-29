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

namespace Eccube\Tests\Command\KeyStore;

use Eccube\Command\KeyStore\KeyStoreGenerateCommand;
use Eccube\Command\KeyStore\KeyStoreListCommand;
use Eccube\Command\KeyStore\KeyStoreShowCommand;
use Eccube\Service\AgentCommerce\Security\AcpWebhookKeyPurpose;
use Eccube\Service\AgentCommerce\Security\FilesystemKeyStore;
use Eccube\Service\AgentCommerce\Security\KeyPurposeRegistry;
use Eccube\Service\AgentCommerce\Security\KeyStoreInspector;
use Eccube\Service\AgentCommerce\Security\UcpSigningKeyPurpose;
use Eccube\Service\Permission\UserIdentity;
use Eccube\Service\Permission\WebServerUserResolver;
use Eccube\Tests\EffectiveUserTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 一時ディレクトリを projectDir にしてコマンドを直接組み立てるため, 実際の app/keystore には触れない.
 *
 * Web サーバーの実行ユーザーはテストごとに注入する. 既定では特定できない状態 (null) とし,
 * 権限による判定を検証するテストだけが別ユーザーを与える.
 */
final class KeyStoreCommandTest extends TestCase
{
    use EffectiveUserTrait;

    /**
     * どのローカルユーザーとも一致しない uid / gid (nobody).
     */
    private const FOREIGN_UID = 65534;

    private const KEY_PATH = '/app/keystore/agent-commerce/ucp_signing.key';

    private string $projectDir;

    private Filesystem $fs;

    private KeyPurposeRegistry $registry;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fs = new Filesystem();
        $this->projectDir = sys_get_temp_dir().'/eccube-keystore-cmd-'.bin2hex(random_bytes(6));
        $this->fs->mkdir($this->projectDir);
        // 祖先の権限で Web サーバーからの到達可否がぶれないよう, umask に依らず固定する.
        $this->fs->chmod($this->projectDir, 0755);
        $this->registry = new KeyPurposeRegistry([new UcpSigningKeyPurpose(), new AcpWebhookKeyPurpose()]);
    }

    protected function tearDown(): void
    {
        if (isset($this->fs)) {
            $this->fs->chmod($this->projectDir, 0755, 0000, true);
            $this->fs->remove($this->projectDir);
        }

        parent::tearDown();
    }

    public function testListShowsEveryPurpose(): void
    {
        $tester = $this->runList();

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('ucp_signing', $tester->getDisplay());
        $this->assertStringContainsString('acp_webhook', $tester->getDisplay());
        $this->assertStringContainsString('未生成', $tester->getDisplay());
    }

    public function testListOutputsJson(): void
    {
        $tester = $this->runList(['--format' => 'json']);

        $decoded = json_decode($tester->getDisplay(), true);
        $this->assertIsArray($decoded);
        $this->assertSame(['acp_webhook', 'ucp_signing'], array_column($decoded, 'purpose'));
        $this->assertFalse($decoded[0]['exists']);
    }

    public function testGenerateCreatesKeysAndIsIdempotent(): void
    {
        $first = $this->runGenerate();

        $this->assertSame(0, $first->getStatusCode());
        $this->assertStringContainsString('created', $first->getDisplay());
        $this->assertFileExists($this->projectDir.self::KEY_PATH);
        $material = (string) file_get_contents($this->projectDir.self::KEY_PATH);

        $second = $this->runGenerate();

        $this->assertSame(0, $second->getStatusCode());
        $this->assertStringContainsString('unchanged', $second->getDisplay());
        $this->assertSame($material, file_get_contents($this->projectDir.self::KEY_PATH), '既存の鍵は上書きしない');
    }

    public function testGenerateDryRunDoesNotWrite(): void
    {
        $tester = $this->runGenerate(['--dry-run' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('would_create', $tester->getDisplay());
        $this->assertFileDoesNotExist($this->projectDir.self::KEY_PATH);
    }

    public function testGenerateForceReplacesTheKey(): void
    {
        $this->runGenerate();
        $before = (string) file_get_contents($this->projectDir.self::KEY_PATH);

        $tester = $this->runGenerate(['purposes' => ['ucp_signing'], '--force' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('replaced', $tester->getDisplay());
        $this->assertNotSame($before, file_get_contents($this->projectDir.self::KEY_PATH));
        $this->assertStringContainsString('kid', $tester->getDisplay(), '広告済みの公開鍵が変わることを警告する');
    }

    public function testGenerateRejectsUnknownPurpose(): void
    {
        $tester = $this->runGenerate(['purposes' => ['no_such_purpose']]);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
        $this->assertFileDoesNotExist($this->projectDir.self::KEY_PATH);
    }

    /**
     * 読み取れないだけの鍵を未生成とみなして上書きしない.
     *
     * Web サーバーが実行時に生成した鍵 (0600, www-data 所有) が残っている構成で,
     * CLI から作り直すと署名鍵が黙って差し替わるため.
     */
    public function testGenerateDoesNotOverwriteAnUnreadableKey(): void
    {
        $this->skipIfRoot();

        $this->runGenerate();
        $path = $this->projectDir.self::KEY_PATH;
        $material = (string) file_get_contents($path);
        $this->fs->chmod($path, 0000);

        $tester = $this->runGenerate();

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('unchanged', $tester->getDisplay());
        $this->assertStringContainsString('読み取れません', $tester->getDisplay());

        $this->fs->chmod($path, 0644);
        $this->assertSame($material, file_get_contents($path), '読めない鍵を上書きしない');
    }

    /**
     * 既定のパーミッション (0755 / 0644) なら, 別ユーザーで動く Web サーバーからも読める.
     *
     * Phase 3c で既定を所有者専用から変更した理由そのもの.
     */
    public function testGenerateSucceedsWhenWebServerCanReadTheKey(): void
    {
        $tester = $this->runGenerate([], false, $this->foreignUser());

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('読み取り可', $tester->getDisplay());
    }

    /**
     * 厳格モードでは別ユーザーの Web サーバーから読めないため, エラーとして扱う.
     *
     * 署名はリクエスト処理中に行われるため, 読めない鍵を置いても discovery が失敗する.
     */
    public function testGenerateFailsWhenWebServerCannotReadTheKey(): void
    {
        $tester = $this->runGenerate([], true, $this->foreignUser());

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('Web サーバーから読み取れません', $tester->getDisplay());
        $this->assertFileExists($this->projectDir.self::KEY_PATH, '書き込み自体は完了している');
    }

    /**
     * 差し替えなかった鍵が読めない場合もエラーにする.
     *
     * 「実行したのにサイトが 500 のまま」を終了コード 0 で見逃さないため.
     */
    public function testGenerateFailsWhenAnExistingKeyIsUnreadableByWebServer(): void
    {
        $this->runGenerate([], true);

        $tester = $this->runGenerate([], true, $this->foreignUser());

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('unchanged', $tester->getDisplay());
        $this->assertStringContainsString('Web サーバーから読み取れません', $tester->getDisplay());
    }

    public function testShowExposesPublicMaterialOnly(): void
    {
        $this->runGenerate();
        $material = (string) file_get_contents($this->projectDir.self::KEY_PATH);

        $tester = $this->runShow(['purpose' => 'ucp_signing']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('ES256', $tester->getDisplay());
        $body = (string) preg_replace('/-----[^-]+-----|\s+/', '', $material);
        $this->assertStringNotContainsString($body, $tester->getDisplay(), '鍵素材を表示しない');
    }

    public function testShowDoesNotRevealTheSharedSecret(): void
    {
        $this->runGenerate();
        $secret = trim((string) file_get_contents($this->projectDir.'/app/keystore/agent-commerce/acp_webhook.key'));

        $tester = $this->runShow(['purpose' => 'acp_webhook']);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertStringContainsString('HMAC-SHA256', $tester->getDisplay());
        $this->assertStringNotContainsString($secret, $tester->getDisplay(), '共有シークレットを表示しない');
    }

    public function testShowFailsWhenKeyIsMissing(): void
    {
        $tester = $this->runShow(['purpose' => 'ucp_signing']);

        $this->assertSame(Command::FAILURE, $tester->getStatusCode());
        $this->assertStringContainsString('eccube:keystore:generate', $tester->getDisplay());
    }

    public function testShowRejectsUnknownPurpose(): void
    {
        $tester = $this->runShow(['purpose' => 'no_such_purpose']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    /**
     * @param array<string, mixed> $input
     */
    private function runList(array $input = []): CommandTester
    {
        return $this->runCommand(new KeyStoreListCommand($this->inspector(false, null)), $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function runShow(array $input): CommandTester
    {
        return $this->runCommand(new KeyStoreShowCommand($this->inspector(false, null), $this->registry), $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function runGenerate(array $input = [], bool $strict = false, ?UserIdentity $webServer = null): CommandTester
    {
        $command = new KeyStoreGenerateCommand(
            $this->keyStore($strict),
            $this->inspector($strict, $webServer),
            $this->registry
        );

        return $this->runCommand($command, $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function runCommand(Command $command, array $input): CommandTester
    {
        $tester = new CommandTester($command);
        $tester->execute($input);

        return $tester;
    }

    private function keyStore(bool $strict): FilesystemKeyStore
    {
        return new FilesystemKeyStore($this->projectDir, [], $strict);
    }

    private function inspector(bool $strict, ?UserIdentity $webServer): KeyStoreInspector
    {
        return new KeyStoreInspector($this->keyStore($strict), $this->registry, $this->resolver($webServer));
    }

    private function foreignUser(): UserIdentity
    {
        return new UserIdentity(self::FOREIGN_UID, self::FOREIGN_UID, 'test');
    }

    /**
     * Web サーバーの実行ユーザーを固定する差し替え.
     *
     * 実環境の var/sessions や html/upload の所有者に依存させないため, 解決結果を注入する.
     */
    private function resolver(?UserIdentity $identity): WebServerUserResolver
    {
        return new class($identity) extends WebServerUserResolver {
            public function __construct(private readonly ?UserIdentity $identity)
            {
            }

            #[\Override]
            public function resolve(): ?UserIdentity
            {
                return $this->identity;
            }
        };
    }
}

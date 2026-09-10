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

namespace Eccube\Tests\Service\Content;

use Eccube\Entity\MailTemplate;
use Eccube\Exception\ContentValidationException;
use Eccube\Exception\ContentWriteException;
use Eccube\Service\Content\ContentResult;
use Eccube\Service\Content\ContentStatus;
use Eccube\Service\Content\MailTemplateContentService;
use Eccube\Tests\EccubeTestCase;
use Eccube\Tests\EffectiveUserTrait;

final class MailTemplateContentServiceTest extends EccubeTestCase
{
    use EffectiveUserTrait;

    private ?MailTemplateContentService $mailTemplateContentService = null;

    /**
     * @var list<string>|null
     */
    private ?array $createdFiles = null;

    private ?string $fileName = null;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mailTemplateContentService = self::getContainer()->get(MailTemplateContentService::class);
        $this->createdFiles = [];
        $this->fileName = 'test_mail_'.bin2hex(random_bytes(4));
    }

    protected function tearDown(): void
    {
        foreach ($this->createdFiles ?? [] as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }

        parent::tearDown();
    }

    public function testApplyCreatesTemplate(): void
    {
        $result = $this->apply(['name' => 'テストメール', 'subject' => '件名', 'body' => 'created body']);

        $this->assertSame(ContentStatus::Created, $result->status);

        $Mail = $this->mailTemplateContentService->findByFileName((string) $this->fileName);
        $this->assertInstanceOf(MailTemplate::class, $Mail);
        $this->assertSame('Mail/'.$this->fileName.'.twig', $Mail->getFileName(), 'ファイル名は Mail/xxx.twig へ変換される');
        $this->assertTrue($Mail->isDeletable());
        $this->assertSame('created body', file_get_contents((string) $result->path()));
    }

    public function testApplyIsIdempotent(): void
    {
        $this->apply(['name' => 'テストメール', 'subject' => '件名', 'body' => 'same body']);
        $result = $this->apply(['body' => 'same body']);

        $this->assertSame(ContentStatus::Unchanged, $result->status);
    }

    public function testApplyWritesAndRemovesHtmlPart(): void
    {
        $this->apply(['name' => 'テストメール', 'subject' => '件名', 'body' => 'body']);

        $result = $this->apply(['html_body' => '<p>html</p>']);
        $htmlPath = $result->writtenPaths[1] ?? '';
        $this->createdFiles[] = $htmlPath;

        $this->assertSame(ContentStatus::Updated, $result->status);
        $this->assertSame('<p>html</p>', file_get_contents($htmlPath));

        $removed = $this->apply(['remove_html' => true]);

        $this->assertSame([$htmlPath], $removed->removedPaths);
        $this->assertFileDoesNotExist($htmlPath);
    }

    public function testApplyKeepsHtmlPartWhenNotSpecified(): void
    {
        $this->apply(['name' => 'テストメール', 'subject' => '件名', 'body' => 'body']);
        $result = $this->apply(['html_body' => '<p>html</p>']);
        $htmlPath = $result->writtenPaths[1] ?? '';
        $this->createdFiles[] = $htmlPath;

        $this->apply(['body' => 'updated body']);

        $this->assertFileExists($htmlPath, 'HTML パートを指定しない場合は現在の内容を維持する');
    }

    public function testApplyRejectsInvalidTwig(): void
    {
        $this->expectException(ContentValidationException::class);

        $this->apply(['name' => 'テストメール', 'subject' => '件名', 'body' => '{% block foo %}']);
    }

    public function testRemoveDeletesTemplate(): void
    {
        $created = $this->apply(['name' => 'テストメール', 'subject' => '件名', 'body' => 'body']);
        $path = (string) $created->path();

        $Mail = $this->mailTemplateContentService->findByFileName((string) $this->fileName);
        $this->assertInstanceOf(MailTemplate::class, $Mail);

        $result = $this->mailTemplateContentService->remove($Mail);

        $this->assertSame(ContentStatus::Removed, $result->status);
        $this->assertFileDoesNotExist($path);
    }

    /**
     * テンプレートファイルを削除できない場合はレコードも残す.
     *
     * 逆順にするとレコードだけが消えてテンプレートが残り, 削除に失敗したテンプレートが
     * 一覧から消える.
     */
    public function testRemoveKeepsRecordWhenTemplateIsNotRemovable(): void
    {
        $this->skipIfRoot();

        $created = $this->apply(['name' => 'テストメール', 'subject' => '件名', 'body' => 'body']);
        $path = (string) $created->path();
        $dir = \dirname($path);
        $originalMode = fileperms($dir) & 0777;

        $Mail = $this->mailTemplateContentService->findByFileName((string) $this->fileName);
        $this->assertInstanceOf(MailTemplate::class, $Mail);

        chmod($dir, 0555);
        try {
            $this->mailTemplateContentService->remove($Mail);
            self::fail('書き込めないディレクトリのテンプレートは削除できない');
        } catch (ContentWriteException $e) {
            $this->assertStringContainsString($path, $e->getPath());
        } finally {
            chmod($dir, $originalMode);
        }

        $this->entityManager->clear();
        $this->assertInstanceOf(
            MailTemplate::class,
            $this->mailTemplateContentService->findByFileName((string) $this->fileName),
            'ファイルを削除できないときはレコードも残す'
        );
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function apply(array $payload, bool $dryRun = false): ContentResult
    {
        $payload = ['file_name' => (string) $this->fileName] + $payload;
        $result = $this->mailTemplateContentService->apply($payload, $dryRun);

        foreach ($result->writtenPaths as $path) {
            $this->createdFiles[] = $path;
        }

        return $result;
    }
}

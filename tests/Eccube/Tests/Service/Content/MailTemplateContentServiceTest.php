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
use Eccube\Service\Content\ContentResult;
use Eccube\Service\Content\ContentStatus;
use Eccube\Service\Content\MailTemplateContentService;
use Eccube\Tests\EccubeTestCase;

final class MailTemplateContentServiceTest extends EccubeTestCase
{
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

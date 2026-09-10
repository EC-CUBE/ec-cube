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

namespace Eccube\Tests\Command\Content;

use Eccube\Command\Content\MailTemplateApplyCommand;
use Eccube\Command\Content\MailTemplateListCommand;
use Eccube\Command\Content\MailTemplateShowCommand;
use Eccube\Entity\MailTemplate;
use Eccube\Service\Content\MailTemplateContentService;
use Eccube\Tests\EccubeTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class MailTemplateCommandTest extends EccubeTestCase
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
        $tester = $this->apply([
            '--file-name' => $this->fileName,
            '--name' => 'テストメール',
            '--subject' => '件名',
            '--body' => 'created body',
        ]);

        $this->assertSame(0, $tester->getStatusCode());

        $Mail = $this->mailTemplateContentService->findByFileName((string) $this->fileName);
        $this->assertInstanceOf(MailTemplate::class, $Mail);
        $this->assertSame('created body', file_get_contents($this->mailTemplateContentService->getFilePath($Mail)));
    }

    public function testApplyReturnsInvalidWithoutTarget(): void
    {
        $tester = $this->apply(['--name' => 'テストメール']);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testApplyRejectsConflictingHtmlOptions(): void
    {
        $tester = $this->apply([
            '--file-name' => $this->fileName,
            '--html-body' => '<p>html</p>',
            '--remove-html' => true,
        ]);

        $this->assertSame(Command::INVALID, $tester->getStatusCode());
    }

    public function testShowOutputsHtmlPart(): void
    {
        $this->apply([
            '--file-name' => $this->fileName,
            '--name' => 'テストメール',
            '--subject' => '件名',
            '--body' => 'body',
        ]);
        $this->apply(['--file-name' => $this->fileName, '--html-body' => '<p>html</p>']);

        $Mail = $this->mailTemplateContentService->findByFileName((string) $this->fileName);
        $this->assertInstanceOf(MailTemplate::class, $Mail);
        $this->createdFiles[] = $this->mailTemplateContentService->getHtmlFilePath($Mail);

        $tester = new CommandTester(self::getContainer()->get(MailTemplateShowCommand::class));
        $tester->execute(['--file-name' => $this->fileName, '--html' => true]);

        $this->assertSame(0, $tester->getStatusCode());
        $this->assertSame('<p>html</p>', $tester->getDisplay());
    }

    public function testShowReturnsErrorWhenHtmlPartIsMissing(): void
    {
        $this->apply([
            '--file-name' => $this->fileName,
            '--name' => 'テストメール',
            '--subject' => '件名',
            '--body' => 'body',
        ]);

        $tester = new CommandTester(self::getContainer()->get(MailTemplateShowCommand::class));
        $tester->execute(['--file-name' => $this->fileName, '--html' => true]);

        $this->assertSame(1, $tester->getStatusCode());
    }

    public function testListContainsCreatedTemplate(): void
    {
        $this->apply([
            '--file-name' => $this->fileName,
            '--name' => 'テストメール',
            '--subject' => '件名',
            '--body' => 'body',
        ]);

        $tester = new CommandTester(self::getContainer()->get(MailTemplateListCommand::class));
        $tester->execute(['--format' => 'json']);

        $this->assertSame(0, $tester->getStatusCode());

        $fileNames = array_column((array) json_decode($tester->getDisplay(), true), 'file_name');
        $this->assertContains('Mail/'.$this->fileName.'.twig', $fileNames);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function apply(array $input): CommandTester
    {
        $tester = new CommandTester(self::getContainer()->get(MailTemplateApplyCommand::class));
        $tester->execute($input + ['--no-cache-clear' => true]);

        $Mail = $this->mailTemplateContentService->findByFileName((string) $this->fileName);
        if ($Mail instanceof MailTemplate) {
            $this->createdFiles[] = $this->mailTemplateContentService->getFilePath($Mail);
        }

        return $tester;
    }
}

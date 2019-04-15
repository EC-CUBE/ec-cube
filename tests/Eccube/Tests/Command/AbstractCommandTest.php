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

namespace Eccube\Tests\Command;

use Eccube\Application;
use Eccube\Command\GeneratorCommand\AbstractPluginGenerator;
use Eccube\Tests\EccubeTestCase;
use Knp\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractCommandTest extends EccubeTestCase
{
    const LOOP_MAX_LIMIT = 5;

    /**
     * @var Command
     */
    protected $command = null;

    /**
     * @var CommandTester
     */
    protected $tester = null;

    /**
     * $contentCnt
     *
     * @var int
     */
    protected $contentCnt = 0;

    /**
     * $content
     *
     * @var string
     */
    protected $content = '';

    /**
     * $loopCnt
     *
     * @var int
     */
    protected $loopCnt = 0;

    /**
     * $loopCheckSum
     *
     * @var int
     */
    protected $loopCheckSum = 0;

    public function tearDown()
    {
        parent::tearDown();
        Application::clearInstance();
    }

    /**
     * $PluginCommand
     *
     * @param Command $PluginCommand
     */
    protected function initCommand($PluginCommand)
    {
        $this->command = $PluginCommand;
        $this->addCommand($this->command);
    }

    /**
     * executeTester
     *
     * @param array $callback
     * @param array $commandArg
     */
    protected function executeTester($callback, $commandArg)
    {
        $cmd = $this->app['console']->find($this->command->getName());
        $this->assertEquals($this->command->getName(), $cmd->getName());

        $this->mockQuestionHelper($this->command, $callback);
        $this->tester = new CommandTester($cmd);
        $this->tester->execute($commandArg);
    }

    /**
     * getLastContent
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getLastContent()
    {
        $display = $this->tester->getDisplay();
        $displayCnt = mb_strlen($display);
        $oldDisplayCnt = $this->contentCnt;
        if ($displayCnt > $oldDisplayCnt) {
            $this->content = mb_substr($display, $oldDisplayCnt);
            $this->contentCnt = $displayCnt;
        }
        if (md5($this->content) == $this->loopCheckSum) {
            $this->loopCnt++;
        } else {
            $this->loopCheckSum = md5($this->content);
            $this->loopCnt = 0;
        }

        if ($this->loopCnt > self::LOOP_MAX_LIMIT) {
            throw new \Exception($this->content.' Contents reach loop limit of '.self::LOOP_MAX_LIMIT.' (AbstractCommandTest::LOOP_MAX_LIMIT)');
        }

        return $this->content;
    }

    /**
     * addCommand
     *
     * @param Command $command
     */
    protected function addCommand($command)
    {
        $this->assertInstanceOf('\Knp\Command\Command', $command);
        if ($command instanceof Command) {
            $this->app['console']->add($command);
        }
    }

    /**
     * mockQuestionHelper
     *
     * @param Command $cmd
     * @param callable $mockHandler
     */
    protected function mockQuestionHelper(Command $cmd, $mockHandler)
    {
        $helper = new QuestionHelperMock();
        $helper->setMockHandler($mockHandler);
        $cmd->getHelperSet()->set($helper, 'question');
    }

    /**
     * getQuestionMark
     *
     * @param int $no
     *
     * @return string
     */
    protected function getQuestionMark($no)
    {
        return AbstractPluginGenerator::INPUT_OPEN.$no.AbstractPluginGenerator::INPUT_CLOSE;
    }

    public function createApplication()
    {
        $app = Application::getInstance([
            'eccube.autoloader' => $GLOBALS['eccube.autoloader'],
        ]);
        $app['debug'] = true;
        $app->initialize();
        // Console
        if (!$app->offsetExists('console')) {
            $app->register(
                new \Knp\Provider\ConsoleServiceProvider(), [
                    'console.name' => 'EC-CUBE',
                    'console.version' => \Eccube\Common\Constant::VERSION,
                    'console.project_directory' => __DIR__.'/..',
                ]
            );
            $app->extend('console.command.twig.debug', function ($command, $app) {
                return new \Symfony\Bridge\Twig\Command\DebugCommand($app['twig']);
            });

            $app->extend('console.command.twig.lint', function ($command, $app) {
                return new \Symfony\Bridge\Twig\Command\LintCommand($app['twig']);
            });
        }

        // Migration
        if (!$app->offsetExists('db.migrations.path')) {
            $app->register(new \Dbtlr\MigrationProvider\Provider\MigrationServiceProvider(), [
                'db.migrations.path' => __DIR__.'/../../../../src/Eccube/Resource/doctrine/migration',
            ]);
        }
        $app->boot();
        $app['console'];

        return $app;
    }
}

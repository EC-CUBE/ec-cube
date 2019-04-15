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

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class QuestionHelperMock extends QuestionHelper
{
    public function setUp()
    {
        $this->markTestIncomplete(get_class($this).' は未実装です');
        parent::setUp();
    }

    /**
     * @var callable
     */
    private $mockHandler;

    /**
     * @param callable
     */
    public function setMockHandler($mockHandler)
    {
        $this->mockHandler = $mockHandler;
    }

    /**
     * @param OutputInterface
     * @param Question
     */
    public function doAsk(OutputInterface $output, Question $question)
    {
        $this->writePrompt($output, $question);
        $output->write(' => ');

        $response = call_user_func($this->mockHandler, $question->getQuestion(), $question);

        if (strlen($response) <= 0) {
            $response = $question->getDefault();
        }

        if ($normalizer = $question->getNormalizer()) {
            $response = $normalizer($response);
        }

        $output->writeln(print_r($response, true));

        return $response;
    }
}

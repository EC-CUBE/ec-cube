<?php

namespace Eccube\Tests\Command;

use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class QuestionHelperMock extends QuestionHelper
{

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

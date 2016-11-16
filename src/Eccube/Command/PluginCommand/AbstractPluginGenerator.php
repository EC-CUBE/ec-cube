<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */

namespace Eccube\Command\PluginCommand;

use Eccube\Common\Constant;
use Symfony\Component\Console\Question\Question;

abstract class AbstractPluginGenerator
{

    const NEW_HOOK_VERSION = '3.0.9';
    const STOP_PROCESS = 'quit';

    /**
     * app
     * @var \Eccube\Application
     */
    protected $app;

    /**
     * QuestionHelper
     * @var \Symfony\Component\Console\Helper\QuestionHelper
     */
    protected $dialog;

    /**
     * InputInterface
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    protected $input;

    /**
     * InputInterface
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * $paramList
     * @var array $paramList
     */
    protected $paramList;

    /**
     * ヘッダー
     */
    abstract protected function getHeader();

    /**
     * start()
     */
    abstract protected function start();

    /**
     * フィルドーセット
     */
    abstract protected function initFildset();

    public function __construct(\Eccube\Application $app)
    {
        $this->app = $app;
    }

    /**
     * 
     * @param \Symfony\Component\Console\Helper\QuestionHelper $dialog
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    public function init($dialog, $input, $output)
    {
        $this->dialog = $dialog;
        $this->input = $input;
        $this->output = $output;
        $this->initFildset();
    }

    public function run()
    {
        //ヘッダー部分
        $this->getHeader();

        foreach ($this->paramList as $paramKey => $params) {
            $value = $this->makeLineRequest($params);
            if ($value === false) {
                $this->exitGenerator();
                return;
            }
            $this->paramList[$paramKey]['value'] = $value;
        }

        $this->output->writeln('');
        $this->output->writeln('---入力内容確認');
        foreach ($this->paramList as $paramKey => $params) {
            if (is_array($params['value'])) {
                $this->output->writeln($params['label']);
                foreach ($params['value'] as $keys => $val) {
                    $this->output->writeln('<info>  ' . $keys . '</info>');
                }
            } else {
                if (isset($params['show'])) {
                    $disp = $params['show'][$params['value']];
                } else {
                    $disp = $params['value'];
                }
                $this->output->writeln($params['label'] . ' <info>' . $disp . '</info>');
            }
        }
        $this->output->writeln('');
        $Question = new Question('<comment>上のプラグイン作成してよろしですか? [y/n] : </comment>', '');
        $value = $this->dialog->ask($this->input, $this->output, $Question);
        if ($value != 'y') {
            $this->exitGenerator();
            return;
        }

        $this->start();
    }

    protected function exitGenerator($msg = '完了 Bye bye.')
    {
        $this->output->writeln($msg);
    }

    protected function makeLineRequest($params)
    {
        $this->output->writeln($params['name']);
        $Question = new Question('<comment>入力 : </comment>', '');
        $value = $this->dialog->ask($this->input, $this->output, $Question);
        $value = trim($value);
        if ($value === self::STOP_PROCESS) {
            return false;
        }
        foreach ($params['validation'] as $key => $row) {

            if ($key == 'isRequired' && $row == true) {
                if ($value === '' || strlen($value) == 0) {

                    $this->output->writeln('[※]入力されていません');
                    return $this->makeLineRequest($params);
                }
            } elseif ($key == 'patern' && preg_match($row, $value) == false) {
                $this->output->writeln('<error>[※]有効な値ではありません</error>');
                return $this->makeLineRequest($params);
            } elseif ($key == 'inArray' || $key == 'choice') {

                if (is_string($row)) {
                    $row = $this->$row();
                }
                if ($value == '') {
                    return $params['value'];
                }
                if (isset($row[$value])) {
                    if (!is_array($params['value'])) {
                        $value = $row[$value];
                        continue;
                    }
                    $params['value'][$value] = $row[$value];
                    $this->output->writeln('<info>---現在リスト</info>');
                    foreach ($params['value'] as $subKey => $node) {
                        $this->output->writeln('<info> - ' . $subKey . '</info>');
                    }
                    $this->output->writeln('');
                    $this->output->writeln('---※追加完了するにはエンターを入力してください---');

                    return $this->makeLineRequest($params);
                } else {
                    $searchList = array();
                    $max = 16;
                    foreach ($row as $eventKey => $eventConst) {
                        if (strpos($eventKey, $value) !== false || strpos($eventConst, $value) !== false) {
                            if (count($searchList) >= $max) {
                                $searchList['-- 件数は' . $max . '以上あります'] = '';
                                break;
                            }
                            $searchList[$eventKey] = $eventConst;
                        }
                    }
                    $this->output->writeln('<error>[※]入力値は正しくありません</error>');
                    if (!empty($searchList)) {
                        $this->output->writeln('---こちらの検索結果を確認してください');
                    }
                    foreach ($searchList as $subKey => $node) {
                        $this->output->writeln(' - ' . $subKey);
                    }

                    if (!empty($searchList)) {
                        $this->output->writeln('');
                    }
                    return $this->makeLineRequest($params);
                }
            }
        }

        return $value;
    }
}

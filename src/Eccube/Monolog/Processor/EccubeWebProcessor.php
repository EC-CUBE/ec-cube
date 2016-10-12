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

namespace Eccube\Monolog\Processor;

use Monolog\Processor\WebProcessor;

/**
 * WebProcessor拡張クラス
 * protectedであるプロパティをpublicに変更する
 *
 * @package Eccube\Monolog\Processor
 */
class EccubeWebProcessor extends WebProcessor
{
    /**
     * @var array|\ArrayAccess
     */
    public $serverData;

    /**
     * @param array|\ArrayAccess $serverData Array or object w/ ArrayAccess that provides access to the $_SERVER data
     * @param array|null $extraFields Extra field names to be added (all available by default)
     */
    public function __construct($serverData = null, array $extraFields = null)
    {
        parent::__construct($serverData, $extraFields);
    }

}

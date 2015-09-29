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

namespace Eccube\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

class MultiLineIps extends Constraint
{
    const V4 = '4';
    const V6 = '6';
    const ALL = 'all';

    // adds FILTER_FLAG_NO_PRIV_RANGE flag (skip private ranges)
    const V4_NO_PRIV = '4_no_priv';
    const V6_NO_PRIV = '6_no_priv';
    const ALL_NO_PRIV = 'all_no_priv';

    // adds FILTER_FLAG_NO_RES_RANGE flag (skip reserved ranges)
    const V4_NO_RES = '4_no_res';
    const V6_NO_RES = '6_no_res';
    const ALL_NO_RES = 'all_no_res';

    // adds FILTER_FLAG_NO_PRIV_RANGE and FILTER_FLAG_NO_RES_RANGE flags (skip both)
    const V4_ONLY_PUBLIC = '4_public';
    const V6_ONLY_PUBLIC = '6_public';
    const ALL_ONLY_PUBLIC = 'all_public';

    protected static $versions = array(
        self::V4,
        self::V6,
        self::ALL,

        self::V4_NO_PRIV,
        self::V6_NO_PRIV,
        self::ALL_NO_PRIV,

        self::V4_NO_RES,
        self::V6_NO_RES,
        self::ALL_NO_RES,

        self::V4_ONLY_PUBLIC,
        self::V6_ONLY_PUBLIC,
        self::ALL_ONLY_PUBLIC,
    );

    public $version = self::V4;

    public $message = 'This is not a valid multi-line IP address.';

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);

        if (!in_array($this->version, self::$versions)) {
            throw new ConstraintDefinitionException(sprintf('The option "version" must be one of "%s"', implode('", "', self::$versions)));
        }
    }
}

<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Doctrine\ORM\Tools\Pagination;

use Doctrine\ORM\ORMException;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;

/**
 * RowNumberOverFunction
 *
 * Provides ROW_NUMBER() OVER(ORDER BY...) construct for use in LimitSubqueryOutputWalker
 *
 * @since   2.5
 *
 * @author  Bill Schaller <bill@zeroedin.com>
 */
class RowNumberOverFunction extends FunctionNode
{
    /**
     * @var \Doctrine\ORM\Query\AST\OrderByClause
     */
    public $orderByClause;

    /**
     * @override
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return 'ROW_NUMBER() OVER('.trim($sqlWalker->walkOrderByClause(
            $this->orderByClause
        )).')';
    }

    /**
     * @override
     *
     * @throws ORMException
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        throw new ORMException('The RowNumberOverFunction is not intended for, nor is it enabled for use in DQL.');
    }
}

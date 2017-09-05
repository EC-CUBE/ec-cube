<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2017 LOCKON CO.,LTD. All Rights Reserved.
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

namespace Eccube\Common\Collection;

use Closure;
use Doctrine\Common\Collections\ArrayCollection;

abstract class StrictArrayCollection extends ArrayCollection
{
    /**
     * {@inheritdoc}
     */
    public function __construct(array $array = array())
    {
        parent::__construct();

        foreach ($array as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * @param mixed $element
     * @return bool
     */
    abstract public function checkValue($element);

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    final public function set($key, $value)
    {
        if (!$this->checkValue($value)) {
            throw new \InvalidArgumentException(sprintf("%s does not accept the passed element.", get_class($this)));
        }

        parent::set($key, $value);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    final public function add($value)
    {
        if (!$this->checkValue($value)) {
            throw new \InvalidArgumentException(sprintf('%s does not accept the passed element.', get_class($this)));
        }

        return parent::add($value);
    }

    /**
     * {@inheritdoc}
     * @throws \InvalidArgumentException
     */
    final public function map(Closure $func)
    {
        $collection = $this;

        $newFunc = function ($element) use ($collection, $func) {

            $new = $func($element);

            if (!$collection->checkValue($new)) {
                throw new \InvalidArgumentException(sprintf('Element converted to unacceptable value. Use %s::looseMap(Closure) instead.', get_class($collection)));
            }

            return $new;
        };

        return parent::map($newFunc);
    }

    /**
     * 型を保証しないmap
     *
     * @param Closure $func
     * @return ArrayCollection
     */
    final public function looseMap(Closure $func)
    {
        return new parent(array_map($func, $this->toArray()));
    }
}
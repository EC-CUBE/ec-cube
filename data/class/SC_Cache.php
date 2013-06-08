<?php
/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2013 LOCKON CO.,LTD. All Rights Reserved.
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

require DATA_REALDIR . 'module/Cache/Lite.php';

/**
 * Cache controll using PEAR::Cache_Lite.
 */
class SC_Cache
{
    /**
     * Instance of PEAR::Cache_Lite class.
     * @var object
     */
    static $_instance = NULL;

    /**
     * Default cache lifetime.
     */
    const LIFETIME = MAX_LIFETIME;

    /**
     * Directory to save cache files.
     */
    const CACHEDIR = MASTER_DATA_REALDIR;

    /**
     * Create Cache_Lite object and set it to static variable.
     *
     * @return void
     */
    public static function forge()
    {
        $options = array(
            'cacheDir' => SC_Cache_Ex::CACHEDIR,
            'lifeTime' => SC_Cache_Ex::LIFETIME,
            'automaticSerialization' => TRUE
        );
        SC_Cache_Ex::$_instance = new Cache_Lite($options);
    }

    /**
     * Get Cache_Lite object.
     *
     * @return void
     */
    public static function getInstance()
    {
        is_null(SC_Cache_Ex::$_instance) and SC_Cache_Ex::forge();

        return SC_Cache_Ex::$_instance;
    }

    /**
     * Get data from cache.
     *
     * @param   string  $id         cache id
     * @param   string  $group      name of the cache group
     * @param   int     $lifeTime   custom lifetime
     * @return  mixed   data of cache (else : false)
     */
    public static function get($id, $group = 'default', $lifeTime = NULL)
    {
        $processor = SC_Cache_Ex::getInstance();

        // set custom lifetime.
        !is_null($lifeTime) and $processor->setOption('lifeTime', $lifeTime);

        $cache = $processor->get($id, $group);

        // set back to default lifetime.
        !is_null($lifeTime) and $processor->setOption('lifeTime', SC_Cache_Ex::$_lifetime);

        return $cache;
    }

    /**
     * Save data into cache.
     *
     * @param   mixed   $data   data of cache
     * @param   string  $id     cache id
     * @param   string  $group  name of the cache group
     * @return  void
     */
    public static function save($data, $id, $group = 'default')
    {
        $processor = SC_Cache_Ex::getInstance();

        $processor->save($data, $id, $group);
    }

    /**
     * Clean cache.
     *
     * @param string $group name of the cache group
     * @return void
     */
    public static function clean($group = FALSE)
    {
        $processor = SC_Cache_Ex::getInstance();

        $processor->clean($group);
    }
}

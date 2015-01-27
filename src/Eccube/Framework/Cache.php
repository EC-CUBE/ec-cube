<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) 2000-2014 LOCKON CO.,LTD. All Rights Reserved.
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eccube\Framework;

/**
 * Cache controll using PEAR::Cache_Lite.
 */
class Cache
{
    /**
     * Instance of PEAR::Cache_Lite class.
     * @var object
     */
    public static $_instance = NULL;

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
            'cacheDir' => static::CACHEDIR,
            'lifeTime' => static::LIFETIME,
            'automaticSerialization' => TRUE
        );
        static::$_instance = new Cache_Lite($options);
    }

    /**
     * Get Cache_Lite object.
     *
     * @return void
     */
    public static function getInstance()
    {
        is_null(static::$_instance) and static::forge();

        return static::$_instance;
    }

    /**
     * Get data from cache.
     *
     * @param  string $id       cache id
     * @param  string $group    name of the cache group
     * @param  int    $lifeTime custom lifetime
     * @return mixed  data of cache (else : false)
     */
    public static function get($id, $group = 'default', $lifeTime = NULL)
    {
        $processor = static::getInstance();

        // set custom lifetime.
        !is_null($lifeTime) and $processor->setOption('lifeTime', $lifeTime);

        $cache = $processor->get($id, $group);

        // set back to default lifetime.
        !is_null($lifeTime) and $processor->setOption('lifeTime', static::$_lifetime);

        return $cache;
    }

    /**
     * Save data into cache.
     *
     * @param  mixed  $data  data of cache
     * @param  string $id    cache id
     * @param  string $group name of the cache group
     * @return void
     */
    public static function save($data, $id, $group = 'default')
    {
        $processor = static::getInstance();

        $processor->save($data, $id, $group);
    }

    /**
     * Clean cache.
     *
     * @param  string $group name of the cache group
     * @return void
     */
    public static function clean($group = FALSE)
    {
        $processor = static::getInstance();

        $processor->clean($group);
    }
}

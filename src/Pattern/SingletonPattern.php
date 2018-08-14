<?php

/*
 *
 *  This is a mercure component
 *
 *  (c) RAFINA DANY <dany.rafina@iumio.com>
 *
 *  iumio Mercure, an iumio component [https://iumio.com] [https://mercure.iumio.com]
 *
 *  To get more information about licence, please check the licence file
 *
 */

namespace Mercure\Pattern;

/**
 * Class SingletonPattern
 * This class is a Singleton Pattern
 * @package Mercure\Pattern;
 * @category Routing
 * @licence  MIT License
 * @link https://mercure.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
abstract class SingletonPattern
{
    /** @var object|null Object instance */
    protected static $instances = [];


    /** Get an instance of the class
     *
     * @return mixed The new class instance
     */
    public static function getInstance()
    {
        if (empty(self::$instances[static::class])) {
            $instance = new static();
            self::$instances[static::class] = $instance;
        } else {
            $instance = self::$instances[static::class];
        }
        return ($instance);
    }

  
    /**
     * Clone method has private to prevent the cloning instance
     * @return null
     */
    final private function __clone()
    {
        // LOCKED THE CLONE
        return (null);
    }
    /**
     * is declared as private to prevent unserializing
     * of an instance of the class via the global function unserialize()
     * @return null
     */
    final private function __wakeup()
    {
        // LOCKED THE UNSERIALIZE
        return (null);
    }
}

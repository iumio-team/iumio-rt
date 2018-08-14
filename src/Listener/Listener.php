<?php

/**
 **
 ** This is an iumio Framework component
 **
 ** (c) RAFINA DANY <dany.rafina@iumio.com>
 **
 ** iumio Mercure, an iumio component [https://www.iumio.com] [https://mercure.iumio.com]
 **
 ** To get more information about licence, please check the licence file
 **
 **/

namespace Mercure\Core\Routing\Listener;

/**
 * Interface Listener
 * @package Mercure\Core\Routing\Listener
 * @category Routing
 * @licence  MIT License
 * @link https://mercure.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

interface Listener
{

    /**
     * @return int
     */
    public function open():int;

    /**
     * @return array
     */
    public function render():array;

    /**
     * @param $oneRouter
     * @return int
     */
    public function close($oneRouter):int;

    /**
     * @return int
     */
    public function listingRouters():int;
}

<?php

/**
 **
 ** This is a mercure component
 **
 ** (c) RAFINA DANY <dany.rafina@iumio.com>
 **
 ** iumio Mercure, an iumio component [https://www.iumio.com] [https://mercure.iumio.com]
 **
 ** To get more information about licence, please check the licence file
 **
 **/

namespace Mercure\Parser;

/**
 * Class AbstractParser
 * @package Mercure\Parser
 * @category Router
 * @licence  MIT License
 * @link https://mercure.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */

class AbstractParser
{
    /**
     * @var array $methodsReq Methods allowed for HTTP Communications
     */
    protected $methodsReq = array("GET", "PUT", "DELETE", "POST",
        "PATH", "ALL", "OPTIONS", "TRACE", "HEAD", "CONNECT");
    /**
     * @var array $keywords Keywords allowed in Mercure
     * name : route name
     * path : route path
     * activity : The method (Activity) called by the route
     * m_allow : The HTTP method(s) allowed
     * route : The start keyword for a route
     * endroute : The end keyword for a route
     * visibility : The route visibility (private for PHP only, public for PHP and Javascript Routing (JSRouting) and
     * disabled to disable a route)
     *
     * parameters : Adding routing parameters (for example parameters: {hi: string, men:int}
     * api_auth: enable API authentification for only this route
     * (required a api key then an Exception will be generated)
     *
     */
    protected $keywords = array("name", "path", "activity", "m_allow",
        "visibility", "parameters", "api_auth");

    /**
     * @var array $scalar The scalar type for parameters
     */
    protected $scalar = array("string", "bool", "int", "float", "object");

    /**
     * @var array $visibilities Routes visibilities
     */
    protected $visibilities = array("public", "private", "disabled");
}
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

namespace Mercure\Core\Routing\Js;

use iumioFramework\Core\Routing\Listener\Listener;
use iumioFramework\Core\Base\Json\JsonListener as JL;
use iumioFramework\Core\Requirement\Environment\FEnv;
use iumioFramework\Core\Exception\Server500;
use iumioFramework\Core\Routing\Routing;

/**
 * Class JsRouting
 * @package Mercure\HttpRoutes
 * @category Router
 * @licence  MIT License
 * @link https://mercure.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
class JsRouting implements Listener
{
    protected $mercure_path = null;
    protected $mercure_path_base = null;
    protected $apps_path = null;
    protected $baseapps_path = null;
    protected $is_base = false;
    protected $resource = null;


    /**
     * JsRouting constructor.
     * @param bool $is_base is a Base app
     * @throws
     */
    public function __construct(bool $is_base = false)
    {
        $this->mercure_path = FEnv::get("framework.web.components.libs")."mercure/config_files/map.merc.js";
        $this->mercure_path_base = FEnv::get("framework.web.components.libs").
            "mercure/config_files/map.merc.base.js";
        $this->apps_path = FEnv::get("framework.config.core.apps.file");
        $this->baseapps_path = FEnv::get("framework.baseapps.apps.file");
        $this->is_base = $is_base;
    }

    /** Build a JS Routing File
     * @return int Operation is a success
     * @throws
     */
    final public function build():int
    {
        return ($this->buildFile());
    }

    /** Open JS Routing File
     * @return int Operation is a success
     * @throws
     */
    public function open():int
    {
        if ($this->resource == null) {
            $this->resource = ($this->is_base)? JL::open($this->mercure_path_base) : JL::open($this->mercure_path);
        }
        return (1);
    }

    /**
     * @return array
     */
    public function render(): array
    {
        return (array());
    }

    /**
     * Write in JS routing file
     * @param string $file_contains File contains
     * @return int Operation is a success
     */
    private function write(string $file_contains):int
    {
         $header = "/*\n * This is an iumio Framework component\n *\n * (c) RAFINA DANY <dany.rafina@iumio.com>";
         $headersub = "\n *\n * iumio Framework, an iumio component [https://iumio.com]\n *\n";
         $headersub2 = " * To get more information about licence, please check the licence file\n */";
         $date = new \DateTime();
         $date = $date->format('Y-m-d H:i:s');
         $header2 = "\n\n/* File generate by Mercure Routing - Time : $date. DO NOT OVERRIDE IT ! */\n\n";

        $rs = $header.$headersub.$headersub2.$header2."var mercuredata = ".$file_contains.";";
        return (($this->is_base)? JL::put($this->mercure_path_base, $rs) : JL::put($this->mercure_path, $rs));
    }


    /** Build JS Routing File with multiple stage
     * @return int Operation is a success
     * @throws Server500 If Clear was not working
     */
    private function buildFile():int
    {
        $mercure = $this->analysis();
        $mercurefinal = array();
        if ($this->clear() != 1) {
            throw new Server500(new \ArrayObject(array("explain" => "Cannot clear Routing JS File",
                "solution" => "Please check the Routing JS file")));
        }
        while ($mercure->valid()) {
            array_push($mercurefinal, $mercure->current());
            $mercure->next();
        }

        return ($this->write(json_encode((object)$mercurefinal, JSON_PRETTY_PRINT)));
    }


    /** Get app list
     * @return \stdClass The app list
     * @throws
     */
    private function getApplist():\stdClass
    {
        $apps = ($this->is_base)? JL::open($this->baseapps_path): JL::open($this->apps_path);
        JL::close($this->apps_path);
        return ($apps);
    }

    /**
     * Clear routing js file
     * @return int Operation is a success
     */
    private function clear():int
    {
        return (($this->is_base)? JL::put($this->mercure_path_base, "") :
            JL::put($this->mercure_path, ""));
    }

    /**
     * Analysis route visibility
     * @return \ArrayIterator An array interator contains all public routes
     * @throws Server500
     */
    private function analysis():\ArrayIterator
    {
        $apps = $this->getApplist();
        $routing = new \ArrayIterator();
        foreach ($apps as $one => $values) {
            $mercure = new Routing($values->name, (isset($values->prefix)? $values->prefix : ""), $this->is_base);
            $mercure->routingRegister();
            $appmercure = $mercure->routes();
            $apppublic = array();
            foreach ($appmercure as $mercure => $valk) {
                if ($valk['visibility'] == "public") {
                    array_push($apppublic, array("name" => $valk['routename'],
                        "path" => $valk['path'], "params" => $valk['params']?? ""));
                }
            }
            $routing->append(array($values->name => $apppublic));
        }
        return ($routing);
    }

    /** Close the router ressource
     * @param $oneRouter
     * @return int
     */
    public function close($oneRouter): int
    {
        if ($this->resource != null) {
            JL::close($this->mercure_path);
        }
        return (1);
    }

    /**
     * @return int
     */
    public function listingRouters(): int
    {
        return (1);
    }
}

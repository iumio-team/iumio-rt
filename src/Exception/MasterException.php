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

namespace Mercure\Exception;

abstract class MasterException extends \Exception
{

}



/**
 * Class AbstractServer
 * @package iumioFramework\Core\Exception
 * @category Framework
 * @licence  MIT License
 * @link https://framework.iumio.com
 * @author   RAFINA Dany <dany.rafina@iumio.com>
 */
abstract class AbstractServer extends \Exception
{
    /**
     * AbstractServer constructor.
     * @param ArrayObject $component Error elements
     * @param string $header_message Header message
     * @throws \Exception
     */
    public function __construct(ArrayObject $component, string $header_message)
    {
        if (isset($this->color_class[$this->code])) {
            $this->color_class_checked = $this->color_class[$this->code];
        }

        $this->time = (new \DateTime())->format("Y-m-d H:i:s");
        $this->uidie = ToolsExceptions::generateUidie();
        $this->client_ip = ToolsExceptions::getClientIp();
        $it = $component->getIterator();
        foreach ($it as $one => $value) {
            if ($it->key() == "codeTitle") {
                $this->codeTitle = $value;
            } elseif ($it->key() == "explain") {
                $this->explain =  preg_replace(
                    "/\r|\n/",
                    " ",
                    trim(preg_replace('/\s\s+/', ' ', strip_tags($value)))
                );
            } elseif ($it->key() == "solution") {
                $this->solution =  preg_replace(
                    "/\r|\n/",
                    " ",
                    trim(preg_replace('/\s\s+/', ' ', strip_tags($value)))
                );
            } elseif ($it->key() == "inlog") {
                $this->inlog = $value;
            } elseif ($it->key() == "external") {
                $this->external = ($value == "yes")? $value : "no";
            } elseif ($it->key() == "type_error") {
                $this->type_error = $value;
            } elseif ($it->key() == "file_error") {
                $this->file_error = $value;
            } elseif ($it->key() == "line_error") {
                $this->line_error = $value;
            } elseif ($it->key() == "trace") {
                $this->trace = $value;
            }

            if ($this->solution == null) {
                $this->solution = "Please check your app configuration";
            }
        }

        if ($this->inlog) {
            $this->writeFileError();
        }
        $this->display($this->code, $header_message);
    }


    /** Display server error to user
     * @param string $code Header code
     * @param string $message Header message
     * @return void
     * @throws \Exception
     */
    public function display(string $code, string $message)
    {
        if (defined("IUMIO_FCM") && IUMIO_FCM === true) {
            return($this->displayConsole($code, $message));
        }

        $libf = \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs.framework");
        $title = $this->code.' '.strtolower(ucfirst($this->codeTitle)).
            ' - Environment '.(ucfirst(strtolower($this->env)));
        $boot = \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").'bootstrap/';
        $anim = \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").
            'animate.css/';
        $im = \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").
            'iumio-manager/';
        $font =  \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs").
            'font-awesome/';

        $msg = 'An exception was generated - Environment '.(strtoupper(strtolower($this->env)));

        $fimg = \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs.framework").
            'img/';

        $libs =  \iumioFramework\Core\Requirement\Environment\FEnv::get("host.web.components.libs");

        @header($_SERVER['SERVER_PROTOCOL'] .' '.
            (($code == 000)? 500 : $code).' '.HttpResponse::getPhrase($code), true, $code);
        if ($this->checkExceptionOverride($code)) {
            $this->displayOverride($code, $message);
        } elseif ($this->external || FEnv::get("framework.env") == "prod") {
            (@include_once(FEnv::get("framework.exceptions_view").'html/'.$code.'.html'));
        } else {
            (@include_once  FEnv::get("framework.exceptions_view").'layout.exception.html.php');
        }
        die();
    }

    /** Display on console
     * @param string $code Header code
     * @param string $message Header message
     * @return mixed None
     * @throws \Exception
     */
    public function displayConsole(string $code, string $message)
    {
        $str_additionnal = "\n\n";
        if ($this->type_error != null) {
            $str_additionnal .= " \nPHP Error type : ".$this->type_error;
        }
        if ($this->file_error != null) {
            $str_additionnal .= " \nFile : ".$this->file_error;
        }
        if ($this->line_error != null) {
            $str_additionnal .= " \nLine : ".$this->line_error;
        }

        \iumioFramework\Core\Additional\Manager\Display\OutputManager::displayAsError(
            "An exception was generated\n\nUidie : ".
            $this->uidie."\nTime : ". $this->time."\nType : ".
            $this->code." ".$this->codeTitle."\nEnv : ".
            $this->env."\nExplain : ". $this->explain.
            " \nSolution : ".$this->solution.$str_additionnal
        );

        exit(1);
    }


    /** Display exception error override
     * @param string $code Error code
     * @param string $message Error message
     */
    public function displayOverride(string $code, string $message)
    {
        $sm = SmartyEngineTemplate::getSmartyInstance("iumio");
        $sm->assign(array("code" => $code, "message" => $message, "er_object" => $this));

        $sm->display($code.SmartyEngineTemplate::$viewExtention);
    }


    /** Write exception in .log file
     * @return int Success
     * @throws \Exception
     */
    final protected function writeFileError():int
    {
        $d1 = "[";
        $d2 = "]";
        $debug = array();
        $debug['time'] = $d1.$this->time.$d2;
        $debug["uidie"] = $d1.$this->uidie.$d2;
        $debug['client_ip'] = $d1.$this->client_ip.$d2;
        $debug['code'] = $d1.$this->code.$d2;
        $debug['code_title'] = $d1.$this->codeTitle.$d2;
        $debug['explain'] = $d1.$this->explain.$d2;
        $debug['solution'] = $d1.$this->solution.$d2;
        $debug['env'] = $d1.FEnv::get("framework.env").$d2;


        if (defined("IUMIO_FCM") && IUMIO_FCM === true) {
            $debug['uri'] = $d1."Framework Console Manager".$d2;

            $debug['referer'] = $d1."From Framework Console Manager".$d2;
        } else {
            $debug['method'] = $d1 . $_SERVER['REQUEST_METHOD'] . $d2;
            $debug['uri'] = $d1 . $_SERVER['REQUEST_URI'] . $d2;
        }

        $debug['referer'] = $d1.(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null).$d2;

        if ($this->type_error != null) {
            $debug['type_error'] = $d1 . $this->type_error . $d2;
            $debug['file_error'] = $d1 . $this->file_error . $d2;
            $debug['line_error'] = $d1 . $this->line_error . $d2;
        }
        $strlog =  implode(" ", $debug);
        $f = new \iumioFramework\Core\Base\File\FileListener();

        $f->open(
            FEnv::get("framework.logs").strtolower(FEnv::get("framework.env")).".log",
            "a+",
            true
        );
        $f->put($strlog);
        $f->close();
        return (1);
    }

    /**
     * @param int $code
     */
    final public function setCode(int $code)
    {
        $this->code = $code;
    }

    /** Check if file log exist
     * @param string $path Path to file log
     * @return bool If exist or not
     */
    final public static function checkFileLogExist(string $path):bool
    {
        if (!file_exists($path)) {
            return ((file_put_contents($path, "") != false)? true : false);
        }

        if (!is_readable($path)) {
            return (false);
        }

        if (!is_writable($path)) {
            return (false);
        }
        return (true);
    }

    /**
     * @return mixed|null
     */
    public function getCodeTitle()
    {
        return $this->codeTitle;
    }

    /**
     * @param mixed|null $codeTitle
     */
    public function setCodeTitle($codeTitle)
    {
        $this->codeTitle = $codeTitle;
    }

    /**
     * @return mixed|null
     */
    public function getExplain()
    {
        return $this->explain;
    }

    /**
     * @param mixed|null $explain
     */
    public function setExplain($explain)
    {
        $this->explain = $explain;
    }

    /**
     * @return null|string
     */
    public function getSolution()
    {
        return $this->solution;
    }

    /**
     * @param null|string $solution
     */
    public function setSolution($solution)
    {
        $this->solution = $solution;
    }

    /**
     * @return null|string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param null|string $env
     */
    public function setEnv($env)
    {
        $this->env = $env;
    }

    /**
     * @return bool|mixed|string
     */
    public function getExternal()
    {
        return $this->external;
    }

    /**
     * @param bool|mixed|string $external
     */
    public function setExternal($external)
    {
        $this->external = $external;
    }

    /**
     * @return bool|\DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param bool|\DateTime $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return array
     */
    public function getColorClass(): array
    {
        return $this->color_class;
    }

    /**
     * @param array $color_class
     */
    public function setColorClass(array $color_class)
    {
        $this->color_class = $color_class;
    }

    /**
     * @return mixed|string
     */
    public function getColorClassChecked()
    {
        return $this->color_class_checked;
    }

    /**
     * @param mixed|string $color_class_checked
     */
    public function setColorClassChecked($color_class_checked)
    {
        $this->color_class_checked = $color_class_checked;
    }

    /**
     * @return null|string
     */
    public function getUidie()
    {
        return $this->uidie;
    }

    /**
     * @param null|string $uidie
     */
    public function setUidie($uidie)
    {
        $this->uidie = $uidie;
    }


    /** Check if Exception template is override
     * @param int $code Code for template exception
     * @return int If is override or not
     * @throws
     */
    final private function checkExceptionOverride(int $code):int
    {
        if (file_exists(FEnv::get("framework.overrides")."Exceptions/views/$code".
                SmartyEngineTemplate::$viewExtention) &&
            FEnv::get("framework.env") == "prod") {
            return (1);
        }
        return (0);
    }


    /** Get Logs list for specific environment
     * @param $env string environment name
     * @param $end int Logs limit list
     * @param $uidie string|null Unique Identifier of iumio Exception
     * @return array Logs list
     * @throws Server500
     * @throws \Exception
     */
    public static function getLogs($env = "", $end = 0, string $uidie = null):array
    {
        return (ToolsExceptions::getLogs($env, $end, $uidie));
    }
}

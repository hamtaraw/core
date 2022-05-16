<?php
namespace Hamtaraw;

use Exception;
use Hamtaraw\Module\AbstractModule;
use Hamtaraw\Module\Head;
use Hamtaraw\Module\Modal;
use Hamtaraw\Module\Request;
use Hamtaraw\Module\Response;
use Hamtaraw\Module\Ui;

/**
 * Modules.
 * Each of them manages a complex part common to all Hamtaraw applications.
 * You can override a module by creating it in src/Module, Hamtaraw will find it.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
class Modules
{
    /**
     * Modules.
     *
     * @var AbstractModule[] $Modules
     */
    private array $Modules = [];

    /**
     * Returns the Head module instance.
     *
     * @return Head|AbstractModule
     */
    public function Head()
    {
        return $this->getModuleInstance('Head');
    }

    /**
     * Returns the Modal module instance.
     *
     * @return Modal|AbstractModule
     */
    public function Modal()
    {
        return $this->getModuleInstance('Modal');
    }

    /**
     * Returns the Request module instance.
     *
     * @return Request|AbstractModule
     */
    public function Request()
    {
        return $this->getModuleInstance('Request');
    }

    /**
     * Returns the Response module instance.
     *
     * @return Response|AbstractModule
     */
    public function Response()
    {
        return $this->getModuleInstance('Response');
    }

    /**
     * Returns the Ui module instance.
     *
     * @return Ui|AbstractModule
     */
    public function Ui()
    {
        return $this->getModuleInstance('Ui');
    }

    /**
     * Returns the module instance.
     *
     * @return AbstractModule
     */
    protected function getModuleInstance(string $sModuleName)
    {
        try {
            if (array_key_exists($sModuleName, $this->Modules)) {
                return $this->Modules[$sModuleName];
            }

            $sAppModuleNamespace = "AbstractApp\\Module\\$sModuleName";
            $sHamtaroModuleNamespace = "Hamtaraw\\Module\\$sModuleName";

            if (class_exists($sAppModuleNamespace))
            { # This module is overrided
                $Module = new $sAppModuleNamespace($this);
            }

            else if (class_exists($sHamtaroModuleNamespace))
            { # This module isn't overrided, use the default class
                $Module = new $sHamtaroModuleNamespace($this);
            }

            else
            { # No module found
                throw new Exception("Module $sModuleName isn't defined");
            }

            return $this->Modules[$sModuleName] = $Module;
        }

        catch (Exception $Exception)
        {
            die($Exception->getMessage());
        }
    }
}
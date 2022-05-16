<?php
namespace Hamtaraw\Module;

use Coercive\Utility\Render\RenderTwig;
use Exception;
use Hamtaraw\Component\AbstractComponent;

/**
 * The Ui module.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
class Ui extends AbstractModule
{
    /**
     * Returns the Twig render instance.
     *
     * @return RenderTwig
     * @throws Exception
     */
    public function RenderTwig()
    {
        return (new RenderTwig($this->Microservice->getSrc()))
            ->setCache(false, $this->Microservice->getTmpDir())
            ->addGlobals($this->getGlobalVariables())
            ->addFunction('Component', function ($sId, array $aParams = [])
            {
                $sNamespace = "App\\Component\\$sId\\$sId";
                preg_replace('`(\\\\.+)$`', "$1$1", $sNamespace);

                # The controllers allowed to be loaded are specified in src/main.php
                if (!$this->Microservice->isAllowed($sNamespace))
                {
                    throw new Exception("Not allowed : $sNamespace");
                }

                # The component class doesn't exist
                if (!class_exists($sNamespace))
                {
                    return "The component $sId doesn't exist.";
                }

                /** @var AbstractComponent $Component */
                $Component = new $sNamespace($this->Microservice, $aParams);
                return $Component->getView();
            })->addDirectory($this->Microservice->getTmpDir())->setFileExtension('.twig');
    }

    /**
     * Returns the global variables for twig templates.
     *
     * @return string[]
     */
    public function getGlobalVariables()
    {
        return [
            'Microservice' => $this->Microservice,
            'Modules' => $this->Modules,
            'jsData' => [
                'requestUri' => $this->Modules->Request()->getRequestUri(),
            ],
        ];
    }
}
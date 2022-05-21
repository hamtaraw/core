<?php
namespace Hamtaraw\Module;

use Coercive\Utility\Render\RenderTwig;
use Exception;
use Hamtaraw\Component\AbstractComponent;
use Hamtaraw\Component\AbstractPage;

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
     * @param AbstractComponent $Component
     * @return RenderTwig
     * @throws Exception
     */
    public function RenderTwig(AbstractComponent $Component)
    {
        return (new RenderTwig($Component->getMicroservice()->getSrc()))
            ->setCache(false, $this->Microservice->getTmpDir())
            ->addGlobals($this->getGlobalVariables())
            ->addFunction('Component', function ($sId) use ($Component)
            {
                if (preg_match('`(.+):([a-zA-Z0-9]+)/([a-zA-Z0-9]+)$`', $sId, $aMatches))
                {
                    $sMicroservice = $aMatches[1];
                    $sScope = $aMatches[2];
                    $sClass = $aMatches[3];
                    $sNamespace = "Hamtaraw\\Microservice\\$sMicroservice\\Component\\$sScope\\$sClass\\$sClass";
                }

                elseif (preg_match('`([a-zA-Z0-9]+)/([a-zA-Z0-9]+)$`', $sId, $aMatches))
                {
                    $sScope = $aMatches[1];
                    $sClass = $aMatches[2];
                    $sNamespace = "Hamtaraw\\Microservice\\{$Component->getMicroservice()::getId()}\\Component\\$sScope\\$sClass\\$sClass";
                }

                else
                {
                    throw new Exception("Invalid component identifier : $sId");
                }

                # The component class doesn't exist
                if (!class_exists($sNamespace))
                {
                    return "The component $sId doesn't exist.";
                }

                # The middlewares allowed to be loaded are specified in src/main.php
                if (!$this->Microservice->isAllowed($sNamespace))
                {
                    throw new Exception("Not allowed : $sNamespace");
                }

                /** @var AbstractComponent $Component */
                $Component = new $sNamespace($this->Microservice, $Component->Microservices);
                return $Component->getView();
            })
            ->addDirectories($Component->getTwigPaths())
            ->setFileExtension('.twig');
            #->addDirectory($this->Microservice->getTmpDir())->setFileExtension('.twig');
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

    /**
     * Returns the global variables for twig templates.
     *
     * @param AbstractPage $Page
     * @return string
     */
    public function getPageUrl(AbstractPage $Page)
    {
        return $Page->Urls()[0]->getPath();
    }
}
<?php
namespace Hamtaraw\Component;

use Exception;
use Hamtaraw\AbstractMicroservice;
use Hamtaraw\Middleware\AbstractMiddleware;
use JsonSerializable;

/**
 * A component.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
abstract class AbstractComponent extends AbstractMiddleware implements JsonSerializable
{
    /**
     * The Wrapper instance.
     *
     * @var Wrapper $Wrapper
     */
    protected Wrapper $Wrapper;

    /**
     * View's data.
     *
     * @var array
     */
    protected array $aView = [];

    /**
     * Javascript data.
     *
     * @var array
     */
    protected array $aJs = [];

    /**
     * The constructor.
     *
     * @param AbstractMicroservice $Microservice
     * @param AbstractMicroservice[]|null $Microservices
     * @throws Exception
     */
    public function __construct(AbstractMicroservice $Microservice, array $Microservices = null)
    {
        parent::__construct($Microservice, $Microservices);
        $this->Wrapper = (new Wrapper([
            'class' => 'hamtaro-component',
        ]));
    }

    /**
     * Returns the twig template of the component.
     * Automatically prefixed by src/Component/Template.
     *
     * @return string
     */
    public function getTemplate()
    {
        return '';
    }

    /**
     * Returns the ui path.
     *
     * @return string
     */
    public function getUiPath()
    {
        $aPatterns = [];
        $aPatterns[0] = '`(\\\\[a-zA-Z]+)$`';
        $aPatterns[1] = '`Hamtaraw\\\\`';
        $aPatterns[2] = '`App\\\\`';
        $aPatterns[3] = '`Component\\\\Ajax\\\\`';
        $aPatterns[4] = '`Component\\\\Component\\\\`';
        $aPatterns[5] = '`Component\\\\Form\\\\`';
        $aPatterns[6] = '`Component\\\\Modal\\\\`';
        $aPatterns[7] = '`Component\\\\Page\\\\`';

        $aReplacements = [];
        $aReplacements[0] = '';
        $aReplacements[1] = '';
        $aReplacements[2] = '';
        $aReplacements[3] = '';
        $aReplacements[4] = '';
        $aReplacements[5] = '';
        $aReplacements[6] = '';
        $aReplacements[7] = '';

        return (string) preg_replace($aPatterns, $aReplacements, static::class);
    }

    /**
     * Returns Wrapper.
     *
     * @return Wrapper
     */
    public function Wrapper()
    {
        return new Wrapper;
    }

    /**
     * Returns component's type.
     *
     * @return string
     */
    public function getType()
    {
        preg_match('`Hamtaraws\\\\[a-zA-Z0-9]+\\\\Component\\\\([a-zA-Z0-9]+)\\\\.+`', static::class, $aMatches);
        return $aMatches[1];
    }

    /**
     * Returns the microservice context.
     *
     * @return AbstractMicroservice[]|string[]
     */
    public function getTwigPaths()
    {
        $aTwigPaths = [];

        foreach ($this->Microservices as $Microservice)
        {
            $aTwigPaths[] = $Microservice->getSrc();
        }

        return $aTwigPaths;
    }

    /**
     * Returns the filepath relative to the src dir with $bAbsolute to false.
     *
     * @param bool $bAbsolute
     * @return string
     * @throws Exception
     */
    public function getFilepath(bool $bAbsolute = false)
    {
        preg_match('`^Hamtaraws\\\\[a-zA-Z0-9]+\\\\(Component\\\\.+\\\\[a-zA-Z0-9]+)$`', static::class, $aMatches);
        $sSrcFilepath = (string) str_replace('\\', '/', $aMatches[1]);

        if ($bAbsolute)
        {
            return "{$this->Microservice->getSrc()}/$sSrcFilepath";
        }

        return $sSrcFilepath;
    }

    /**
     * Returns the html view.
     *
     * @param array $aData
     * @return string
     * @throws Exception
     * @throws Exception
     */
    final public function getView(array $aData = [])
    {
        try
        {
            $sFilepath = $this->getFilepath();
            $oRenderTwig = $this->Modules->Ui()->RenderTwig($this)->setDebug(false);

            # All the data for the template
            $aData = array_merge($this->Modules->Ui()->getGlobalVariables(), $this->aView, $aData, ['Head' => $this->Modules->Head()]);

            # The twig file must be created
            if (!is_file("{$this->getFilepath(true)}.twig"))
            {
                throw new Exception("Exception : the .twig template doesn't exist for your component $sFilepath");
            }

            $sComponent = $oRenderTwig->setPath($sFilepath)->setDatas($aData)->render();

            if ($this instanceof AbstractPage)
            { # The wrapper
                $sComponent = "{$this->Wrapper->opening()}$sComponent{$this->Wrapper->closing()}";

                # The component is a page, so we build it with the html structure
                if ($sTwigTemplate = $this->getTemplate())
                { # Adds the twig template
                    $sComponent = str_replace(
                        '<!--COMPONENT-->',
                        $sComponent,
                        $oRenderTwig->setPath($sTwigTemplate)->setDatas($aData)->render()
                    );
                }

                return str_replace(
                    '<!--PAGE-->',
                    $sComponent,
                    $oRenderTwig->setPath('Component/Template/index.twig')->setDatas($aData)->render()
                );
            }

            elseif ($this instanceof AbstractModal)
            { # The component is a modal, so we build it with the modal structure
                if ($sTwigTemplate = $this->getTemplate())
                { # Adds the twig template
                    $sComponent = str_replace(
                        '<!--COMPONENT-->',
                        $sComponent,
                        $oRenderTwig->setPath($sTwigTemplate)->setDatas($aData)->render()
                    );
                }

                # The wrapper
                $sComponent = "{$this->Wrapper->opening()}$sComponent{$this->Wrapper->closing()}";
            }

            else if ($sTwigTemplate = $this->getTemplate())
            { # The wrapper
                $sComponent = "{$this->Wrapper->opening()}$sComponent{$this->Wrapper->closing()}";

                # Adds the twig template
                $sComponent = str_replace(
                    '<!--COMPONENT-->',
                    $sComponent,
                    $oRenderTwig->setPath($sTwigTemplate)->setDatas($aData)->render()
                );
            }

            else
            { # The wrapper
                $sComponent = "{$this->Wrapper->opening()}$sComponent{$this->Wrapper->closing()}";
            }

            return $sComponent;
        }

        catch (Exception $Exception)
        {
            die("[AbstractComponent::getView] {$Exception->getMessage()}");
        }
    }

    /**
     * @inheritDoc
     * @throws Exception
     * @see JsonSerializable::jsonSerialize()
     */
    public function jsonSerialize() {
        return [
            'uipath' => $this->getUiPath(),
            'jsData' => $this->aJs,
            'html' => $this->getView(),
        ];
    }
}
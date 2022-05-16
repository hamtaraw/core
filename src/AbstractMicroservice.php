<?php
namespace Hamtaraw;

use Exception;
use Hamtaraw\Component\AbstractPage;
use Hamtaraw\Contributor\AbstractContributor;
use Hamtaraw\Middleware\AbstractMiddleware;

/**
 * A microservice.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
abstract class AbstractMicroservice
{
    const SRC = 20;
    const MICROSERVICE = 21;

    /**
     * Basepath of your Hamtaraw application.
     * @var string
     */
    protected string $sBasepath;

    /**
     * Microservice's type.
     *
     * @var int
     */
    protected int $iType;

    /**
     * Show the log.
     *
     * @var bool
     */
    protected bool $bShowLog;

    /**
     * The constructor.
     *
     * @throws Exception
     */
    public function __construct(int $iType)
    {
        $this->sBasepath = realpath(getcwd() . '/..');
        $this->iType = $iType;
        $this->bShowLog = true;

        if ($this->showLog())
        {
            error_log("Running Hamtaraw microservice : {$this->getId()}");
            error_log("Allowed middlewares [" . ($this->getMiddlewares() ? implode(' | ', $this->getMiddlewares()) : 'n/a') . ']');
            error_log("Allowed components [" . ($this->getComponents() ? implode(' | ', $this->getComponents()) : 'n/a') . ']');
            error_log("Allowed microservices [" . ($this->getMicroservices() ? implode(' | ', $this->getMicroservices()) : 'n/a') . ']');
        }

        if ($iType === static::SRC)
        {
            foreach ($this->getMicroservices() as $sMicroservice)
            {
                /** @var AbstractMicroservice $Microservice */
                $Microservice = new $sMicroservice(static::MICROSERVICE);
            }
        }

        elseif ($iType === static::MICROSERVICE)
        {
            foreach ($this->getMiddlewares() as $sMiddleware)
            {
                /** @var AbstractMiddleware $Middleware */
                $Middleware = new $sMiddleware($this);
                $Middleware->process();
            }
        }

        else
        {
            throw new Exception("Invalid microservice type : $iType");
        }
    }

    /**
     * Returns the contributors.
     *
     * @return AbstractContributor[]
     */
    abstract public function getContributors();

    /**
     * Returns the namespaces of the components allowed to be loaded.
     *
     * @return string[]
     */
    abstract public function getComponents();

    /**
     * Returns the namespaces of the middlewares allowed to be loaded.
     *
     * @see AbstractMiddleware
     *
     * @return string[]
     */
    abstract public function getMiddlewares();

    /**
     * Returns the namespaces of the microservices allowed to be loaded.
     *
     * @return string[]
     */
    abstract public function getMicroservices();

    /**
     * Returns the cache directory.
     *
     * @return string
     */
    public function getBasepath()
    {
        return "src/Cache";
    }

    /**
     * Returns the cache directory.
     *
     * @return string
     */
    public function getDir()
    {
        return "src/Cache";
    }

    /**
     * Returns true if you want to have a trace of what happens.
     * (error_log is used).
     *
     * @param bool $bShow
     * @return bool|$this
     */
    public function showLog(bool $bShow = null)
    {
        if (is_null($bShow))
        {
            return $this->bShowLog;
        }

        $this->bShowLog = $bShow;
        return $this;
    }

    /**
     * Returns the pages instances.
     *
     * @return AbstractPage[]
     */
    public function getPages()
    {
        $Pages = [];

        foreach ($this->getMiddlewares() as $sMiddleware)
        {
            /** @var AbstractMiddleware $Middleware */
            $Middleware = new $sMiddleware($this);

            if ($Middleware instanceof AbstractPage)
            {
                $Pages[] = $Middleware;
            }
        }

        return $Pages;
    }

    /**
     * Returns true if the namespace is allowed to be loaded.
     *
     * @param string $sNamespace
     * @return bool
     */
    public function isAllowed(string $sNamespace)
    {
        return in_array($sNamespace, array_merge($this->getComponents(), $this->getMiddlewares(), $this->getMicroservices()), true);
    }

    /**
     * Returns the cache directory.
     *
     * @return string
     */
    public function getCacheDir()
    {
        return "src/Cache";
    }

    /**
     * Returns the src.
     *
     * @return string
     */
    public function getSrc()
    {
        return "$this->sBasepath/src";
    }

    /**
     * Returns the src.
     *
     * @return string
     */
    public function getSources()
    {
        return "$this->sBasepath/src";
    }

    /**
     * Returns the tmp directory.
     *
     * @return string
     */
    public function getTmpDir()
    {
        return sys_get_temp_dir();
    }

    /**
     * Returns microservice's id.
     *
     * @return string
     */
    public function getId()
    {
        preg_match('`(.+\\\\)[a-zA-Z0-9]+$`', static::class, $aMatches);
        return str_replace($aMatches[1], '', static::class);
    }

    /**
     * Start as src microservice.
     *
     * @return AbstractMicroservice
     */
    static public function startSrc()
    {
        return new static(static::SRC);
    }

    /**
     * Start as microservice.
     *
     * @return AbstractMicroservice
     */
    static public function startMicroservice()
    {
        return new static(static::MICROSERVICE);
    }
}
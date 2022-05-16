<?php
namespace Hamtaraw;

use Exception;
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
     * The constructor.
     *
     * @throws Exception
     */
    public function __construct(int $iType)
    {
        $this->sBasepath = realpath(getcwd() . '/..');
        $this->iType = $iType;

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
                $Middleware = new $sMiddleware($this, new Modules);
                $Middleware->process();
            }
        }

        else
        {
            throw new Exception("Invalid microservice type : $iType");
        }

        error_log("Microservice basepath -> $this->sBasepath");
        error_log("Microservice type -> $this->iType");
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
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
     *
     * @var string
     */
    protected string $sBasepath = '';

    /**
     * Basepath of your Hamtaraw application.
     *
     * @var string
     */
    protected string $sSrc = '';

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
     * Components's namespaces.
     *
     * @var string[]
     */
    protected array $aComponents = [];

    /**
     * The constructor.
     *
     * @throws Exception
     */
    public function __construct(int $iType)
    {
        $this->iType = $iType;
        $this->bShowLog = true;
        $this->sBasepath = realpath(getcwd() . '/..');
        $this->aComponents = $this->getComponents();

        if ($this->iType === static::SRC)
        {
            $this->sSrc = "$this->sBasepath/src";
        }

        else if ($this->iType === static::MICROSERVICE)
        {
            $this->sSrc = realpath("$this->sBasepath/vendor/hamtaraws/" . strtolower($this::getId()) . '/src');
        }

        else
        {
            throw new Exception("Invalid microservice type : $iType");
        }
    }

    /**
     * Process the microservice.
     *
     * @return void
     */
    public function process()
    {

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
     * Returns the cache directory.
     *
     * @return string
     */
    public function getBasepath()
    {
        return $this->sBasepath;
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
        return in_array($sNamespace, array_merge($this->getComponents(), $this->getMiddlewares()), true);
    }

    /**
     * Returns the cache directory.
     *
     * @return string
     */
    public function getCacheDir()
    {
        return "$this->sBasepath/src/Cache";
    }

    /**
     * Returns the src.
     *
     * @return string
     */
    public function getSrc()
    {
        return $this->sSrc;
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
    public static function getId()
    {
        preg_match('`(.+\\\\)[a-zA-Z0-9]+$`', static::class, $aMatches);
        return str_replace($aMatches[1], '', static::class);
    }

    /**
     * Returns true if if the src microservice.
     *
     * @return bool
     */
    public function isSrc()
    {
        return $this->iType === 20;
    }

    /**
     * Returns type.
     *
     * @return int
     */
    public function getType()
    {
        return $this->iType;
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
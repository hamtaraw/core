<?php
namespace Hamtaraw\Middleware;

use Exception;
use Hamtaraw\AbstractMicroservice;
use Hamtaraw\Modules;

/**
 * A middleware.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
abstract class AbstractMiddleware
{
    /**
     * The microservice id.
     *
     * @var string
     */
    public string $sMicroservice;

    /**
     * Microservices.
     *
     * @var AbstractMicroservice[]|null
     */
    public $Microservices;

    /**
     * The microservice context.
     *
     * @var AbstractMicroservice $Microservice
     */
    protected AbstractMicroservice $Microservice;

    /**
     * The Modules instances.
     *
     * @var Modules $Modules
     */
    protected Modules $Modules;

    /**
     * Inputs values.
     *
     * @var array
     */
    protected array $aInputs = [];

    /**
     * The constructor.
     *
     * @param AbstractMicroservice $Microservice
     * @param AbstractMicroservice[] $Microservices
     * @throws Exception
     */
    public function __construct(AbstractMicroservice $Microservice, array $Microservices = null)
    {
        $this->aInputs = array_merge($_GET, $_POST, json_decode(file_get_contents("php://input"), true) ?: []);
        $this->sMicroservice = $Microservice::getId();
        $this->Microservice = $Microservice;
        $this->Modules = new Modules($Microservice);
        $this->Microservices = $Microservices;

        if ($this->Microservice->showLog())
        {
            error_log("Middleware instanciation {$this::getId()}");
        }
    }

    /**
     * Returns the microservice context.
     *
     * @return AbstractMicroservice
     */
    public function getMicroservice()
    {
        return $this->Microservice;
    }

    /**
     * Returns all microservices.
     *
     * @return AbstractMicroservice[]|string[]
     */
    public function getMicroservices()
    {
        return $this->Microservices;
    }

    /**
     * Running the middleware.
     *
     * @return bool|void
     */
    abstract public function process();

    /**
     * Check the middleware request.
     *
     * @return bool|void
     */
    protected function checkRequest()
    {
        if ($this->Microservice->showLog())
        {
            error_log("Running middleware {$this::getId()}");
        }

        foreach ($this->InputConfigs() as $ParamConfig)
        {
            if (array_key_exists($ParamConfig->getName(), $this->aInputs))
            {
                $sTypeValue = $ParamConfig->getTypeValue();
                $mValue = $this->aInputs[$ParamConfig->getName()];
                settype($mValue, $sTypeValue);
                $this->aInputs[$ParamConfig->getName()] = $mValue;
            }

            elseif ($ParamConfig->isRequired())
            {

                return;
            }
        }
    }

    /**
     * Configure your inputs.
     * They are available in $this->aInputs.
     *
     * @return InputConfig[]
     */
    public function InputConfigs()
    {
        return [];
    }

    /**
     * Returns the namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return static::class;
    }

    /**
     * Returns middleware's id.
     *
     * @return string
     */
    public static function getId()
    {
        preg_match('`(.+\\\\)[a-zA-Z0-9]+$`', static::class, $aMatches);
        return str_replace($aMatches[1], '', static::class);
    }
}
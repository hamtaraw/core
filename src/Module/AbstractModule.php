<?php
namespace Hamtaraw\Module;

use Hamtaraw\AbstractMicroservice;
use Hamtaraw\Modules;

/**
 * A module.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
class AbstractModule
{
    /**
     * The context microservice instance.
     *
     * @var AbstractMicroservice $Microservice
     */
    protected AbstractMicroservice $Microservice;

    /**
     * The Modules instance.
     *
     * @var Modules $Modules
     */
    protected Modules $Modules;

    /**
     * The constructor.
     *
     * @param AbstractMicroservice $Microservice
     * @param Modules $Modules
     */
    public function __construct(AbstractMicroservice $Microservice, Modules $Modules)
    {
        $this->Microservice = $Microservice;
        $this->Modules = $Modules;
    }
}
<?php
namespace Hamtaraw;

use Hamtaraw\Middleware\AbstractMiddleware;

/**
 * Hamtaraw launcher.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
class Launcher
{
    /**
     * The constructor.
     *
     * @param array|AbstractMicroservice[] $Microservices
     */
    public function __construct($Microservices)
    {
        /** @var AbstractMicroservice[] $Microservices */
        $Microservices = array_map(function ($sMicroservice)
        {
            return new $sMicroservice;
        }, $Microservices);

        foreach ($Microservices as $Microservice)
        {
            if ($Microservice->showLog())
            {
                error_log("Running microservice {$Microservice::getId()}");
            }

            foreach ($Microservice->getMiddlewares() as $sMiddleware)
            {
                /** @var AbstractMiddleware $Middleware */
                $Middleware = new $sMiddleware($Microservice, $Microservices);

                if ($Microservice->showLog())
                {
                    error_log("Running microservice {$Microservice::getId()}");
                }

                $Middleware->process();
            }
        }
    }
}
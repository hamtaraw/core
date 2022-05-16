<?php
namespace Hamtaraw\Middleware;

use Hamtaraw\Module\Response;

/**
 * An ajax request.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
abstract class AbstractAjax extends AbstractMiddleware
{
    /**
     * Execute and return the Response instance.
     *
     * @return Response
     */
    abstract public function executeAndGetResponse();
}
<?php
namespace Hamtaraw\Module;

/**
 * Request module.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
class Request extends AbstractModule
{
    /**
     * Returns the request uri.
     *
     * @return string
     */
    public function getRequestUri()
    {
        return (string) $_SERVER['REQUEST_URI'];
    }
}
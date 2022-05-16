<?php
namespace Hamtaraw\Contributor;

/**
 * For the proper maintenance of your Hamtaraw microservice, all developers must be defined in src/Contributor.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
abstract class AbstractContributor
{
    /**
     * Returns contributor's name.
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Returns contributor's email.
     *
     * @return string
     */
    abstract public function getEmail();

    /**
     * Returns contributor's Github account.
     *
     * @return string
     */
    abstract public function getGithub();
}
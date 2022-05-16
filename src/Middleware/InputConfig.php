<?php
namespace Hamtaraw\Middleware;

/**
 * An input param config.
 *
 * @see \Hamtaraw\Middleware\AbstractMiddleware::InputConfigs()
 * 
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
class InputConfig
{
    /**
     * Input's name.
     *
     * @var string
     */
    private string $sName;

    /**
     * Input's type.
     *
     * @var string
     */
    private string $sTypeValue;

    /**
     * Is required ?
     *
     * @var bool
     */
    private bool $bRequired;

    /**
     * The constructor.
     *
     * @param string $sName
     * @param string $sTypeValue
     * @param bool $bRequired
     */
    public function __construct(string $sName, string $sTypeValue, bool $bRequired)
    {
        $this->sName = $sName;
        $this->sTypeValue = $sTypeValue;
        $this->bRequired = $bRequired;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->sName;
    }

    /**
     * @return string
     */
    public function getTypeValue(): string
    {
        return $this->sTypeValue;
    }

    /**
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->bRequired;
    }
}
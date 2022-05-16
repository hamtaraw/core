<?php
namespace Hamtaraw\Command;

use Composer\Script\Event;
use Exception;

/**
 * This class is extended by all Hamtaraw terminal commands.
 *
 * @author Phil'dy Jocelyn Belcou <pj.belcou@gmail.com>
 */
abstract class AbstractCommand
{
    /**
     * Configure the arguments of your command.
     *
     * @return ArgumentConfig[]
     */
    public static function ArgumentConfigs()
    {
        return [];
    }

    /**
     * Check the command inputs.
     *
     * @param array $aInputArguments
     * @return array
     * @throws Exception
     */
    public static function checkArguments(array $aInputArguments)
    {
        $aArguments = [];

        foreach (static::ArgumentConfigs() as $ArgumentConfig)
        {
            if ($ArgumentConfig->getName() === '#1')
            {
                if ($ArgumentConfig->isRequired() && (!$aInputArguments || !$mValue = $aInputArguments[0]))
                {
                    throw new Exception("[Missing argument] {$ArgumentConfig->getName()} : {$ArgumentConfig->getDescription()}");
                }

                $sTypeValue = $ArgumentConfig->getTypeValue();
                settype($mValue, $sTypeValue);
                $aArguments[$ArgumentConfig->getName()] = $mValue;
            }
        }

        return $aArguments;
    }

    /**
     * Run the script.
     *
     * @return void
     * @throws Exception
     */
    abstract public static function run(Event $Event);
}
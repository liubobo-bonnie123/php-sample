#!/usr/bin/php
<?php

/*
 * Redis test
 */

set_include_path(getenv ("CLICKR_GAME_HOME") . "/lib/php");
require_once "PlayGigIt/Autoloader/Autoloader.php";
require_once "PlayGigIt/Bootstrap/Bootstrap.php";

/*
 * Start here
 */

function main ($args)
{
    try
    {
        PlayGigIt\Bootstrap\Bootstrap::checkEnvironment();
    }
    catch (\Exception $e)
    {
        fail ("Can't start: " . $e->getMessage());
    }

    new PlayGigIt\Autoloader\Autoloader();

    // load config
    try
    {
        $config = new \PlayGigIt\Config\Config (getenv("CLICKR_GAME_ENDPOINT"));
    }
    catch (\Exception $e)
    {
        fail ("Can't load config: " . $e->getMessage());
    }

    $idmaker = \PlayGigIt\DB\UniqueID\UniqueIDFactory::getClass ($config);
    $id = $idmaker->getNextID();
    echo "Got {$id} from " . ((string) $idmaker) . "\n";
}
exit(main($argv));
?>

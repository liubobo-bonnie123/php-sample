#!/usr/bin/php
<?php

/*
 * Redis test
 */

set_include_path(getenv ("CLICKR_GAME_HOME") . "/lib/php");
require_once "PlayGigIt/Autoloader/Autoloader.php";
require_once "PlayGigIt/Bootstrap/Bootstrap.php";
require_once 'vendor/autoload.php';

/*
 * Start here
 */

function main ()
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
        $config = new PlayGigIt\Config\Config (getenv("CLICKR_GAME_ENDPOINT"));
    }
    catch (\Exception $e)
    {
        fail ("Can't load config: " . $e->getMessage());
    }

    $pi = new \PlayGigIt\PlayerIndex\PlayerIndex();
    $pi->init ($config);
    $shard = $pi->getShard ("630806780");
    echo "$shard\n";
}
exit(main());
?>

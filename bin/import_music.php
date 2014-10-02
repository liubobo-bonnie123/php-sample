#!/usr/bin/php
<?php

/*
 * 
 */

set_include_path(getenv ("CLICKR_GAME_HOME") . "/lib/php");
require_once 'vendor/autoload.php';
require_once "PlayGigIt/Autoloader/Autoloader.php";
require_once "PlayGigIt/Bootstrap/Bootstrap.php";

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

    // get database handle
    try
    {
        $dbh = \PlayGigIt\DB\Handle::open
        (
            $config->db_clickr ('host'),
            $config->db_clickr ('port'),
            $config->db_clickr ('user'),
            $config->db_clickr ('pass'),
            $config->db_clickr ('db')
        );
    }
    catch (\Exception $e)
    {
        die ("Can't connect to database: " . $e->getMessage());
    }

    $mp3 = new \PlayGigIt\Data\Import\Music ($config, array());

    try
    {
        $mp3->import ($dbh);
    }
    catch (\Exception $e)
    {
        die ("Failed importing music: " . $e->getMessage());
    }

}
exit(main());
?>

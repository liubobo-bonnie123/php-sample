#!/usr/bin/php
<?php

/*
 * MySQL Connections test
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

    $dbhs = array();

    $want = $config->databases ('names');

    foreach ($want as $w)
    {
        $dbh = \PlayGigIt\DB\Handle::open
        (
            $config->$w ('host'),
            $config->$w ('port'),
            $config->$w ('user'),
            $config->$w ('pass'),
            $config->$w ('db')
        );

        if (count ($args) > 1)
        {
        }

        $query = "select create_date from meta_info";
        $sth = $dbh->query ($query);
        $sth->execute();
        $rows = $sth->fetchAll();
        print_r ($rows);
        $dbhs[] = $dbh;
    }
    
    print_r (\PlayGigIt\DB\Handle::getConnections());
}
exit(main($argv));
?>

#!/usr/bin/php
<?php

/*
 * Throwaway program to rescue music stats from old music.ods file
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

    $mp3 = new \PlayGigIt\Data\Import\Music ($config, array());

    try
    {
        $files = $mp3->load();
    }
    catch (\Exception $e)
    {
        die ("Failed importing music: " . $e->getMessage());
    }
    
    $index = array();

    foreach ($files as $file)
    {
        $index[$file[10]] = $file;
    }
    
    $buf = file_get_contents ("/tmp/music.csv");
    $lines = explode ("\n", $buf);
    array_shift ($lines);
    $hurray = array();

    foreach ($lines as $line)
    {
        $old = str_getcsv ($line);
        print_r ($old);
        $file = $old[2];
        if (! isset ($index[$file]))
        {
            continue;
        }
        $new = $index[$file];

        $cooked = array
        (
            $new[1],
            $old[8],
            $old[9],
            $old[10],
            $old[11],
            $old[12],
            $old[13],
            $old[14]
        );
        $hurray[] = $cooked;
    }

    $file = "/tmp/music_stats.csv";
    $fp = fopen ($file, 'w');
    foreach ($hurray as $row)
    {
        fputcsv ($fp, $row);
    }
    fclose ($fp);
}

exit(main());
?>

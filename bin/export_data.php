#!/usr/bin/php
<?php

/*
 * Generate JSON files from MySQL tables
 */

set_include_path(getenv ("CLICKR_GAME_HOME") . "/lib/php");
require_once "PlayGigIt/Autoloader/Autoloader.php";
require_once "PlayGigIt/Bootstrap/Bootstrap.php";
require_once 'vendor/autoload.php';

/*
 * Show Usage
 */

function help()
{
    echo <<<FIN
Usage:

  {$argv[0]} [ options ]

Options:

  -h, --help                This help message
  --verbose                 Print debug messages

FIN;
    return 1;
}

/*
 * Start here
 */

function main ($options)
{
    // show help message
    if (isset ($options['h']) || isset ($options['help']))
    {
        return help();
    }

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
    
    if (isset ($options['verbose']))
    {
        PlayGigIt\Data\Export::setDebugHandler
        (
            function ($msg)
            {
                echo "$msg\n";
            }
        );
    }
    else
    {
        PlayGigIt\Data\Export::setDebugHandler ( function ($msg) { } );
    }

    // custom error handling
    PlayGigIt\Data\Export::setErrorHandler 
    (
        function ($msg)
        {
            fwrite (STDERR, $msg . "\n");
            exit (666);
        }
    );
    
    PlayGigIt\Data\Export::setWarningHandler 
    (
        function ($msg)
        {
            fwrite (STDERR, "Warning: {$msg}\n");
        }
    );

    return PlayGigIt\Data\Export::exportGameData ($config, $options);
}

/*
 * Command-line options
 */

$shortopts = "h";
$longopts = array
(
    "help",
    "verbose",
);
$options = getopt($shortopts, $longopts);

exit (main ($options));
?>

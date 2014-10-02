#!/usr/bin/php
<?php

/*
 * Generate MySQL tables from ODS 
 */

set_include_path(getenv ("CLICKR_GAME_HOME") . "/lib/php");
require_once "PlayGigIt/Autoloader/Autoloader.php";
require_once "PlayGigIt/Bootstrap/Bootstrap.php";

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
  --dry-run                 Dry run, won't modify database
  --registry                Clickr, Test
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
        PlayGigIt\Data\Import::setDebugHandler
        (
            function ($msg)
            {
                echo "$msg\n";
            }
        );
    }
    else
    {
        PlayGigIt\Data\Import::setDebugHandler ( function ($msg) { } );
    }

    // custom error handling
    PlayGigIt\Data\Import::setErrorHandler 
    (
        function ($msg)
        {
            fwrite (STDERR, "\n\n*** FAIL ***\n\n" . $msg . "\n");
            exit (666);
        }
    );

    PlayGigIt\Data\Import::setWarningHandler
    (
        function ($msg)
        {
            fwrite (STDERR, "Warning: {$msg}\n");
        }
    );

    return PlayGigIt\Data\Import::importGameData ($config, $options);
}

/*
 * Command-line options
 */

$shortopts = "h";
$longopts = array
(
    "help",
    "dry-run",
    "verbose",
    "registry::",
);
$options = getopt($shortopts, $longopts);

exit (main ($options));
?>

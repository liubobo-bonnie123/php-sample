<?php

$start_time = microtime (true);

/*
 * Proof-of-concept: Import songs from GigItScanner JSON for a user
 */

if (count ($argv) != 3)
{
    echo "Usage: {$argv[0]} [ FACEBOOK ID ] [ PATH TO SCANNER JSON ]\n";
    exit (1);
}

$facebook_id = $argv[1];

if (! is_numeric ($facebook_id))
{
    echo "Problem: Invalid facebook id\n";
    exit (1);
}

$file = $argv[2];

$music = json_decode (file_get_contents ($file), true);

if ($music === false)
{
    exit (1);
}

set_include_path(getenv ("CLICKR_GAME_HOME") . "/lib/php");
// require_once "PlayGigIt/Autoloader/Autoloader.php";
// require_once "PlayGigIt/Bootstrap/Bootstrap.php";
require_once 'vendor/autoload.php';

$namespace = "music/player";
$host = "localhost";
$port = 6379;
$uri = "tcp://" . $host . ":" . $port;
$redis = new \Predis\Client ($uri);
$key = implode ("/", array ($namespace, $facebook_id));

$redis->del ($key);

$songs = 0;

foreach ($music as $song => $data)
{
    if (! isset ($data['name']) || ! isset ($data['artist']))
    {
        continue;
    }
    $sum = md5 ($data['artist'] . $data['name']);
    $redis->sadd ($key, $sum);
    ++$songs;
}

$delta = microtime (true) - $start_time;

/*
 * Meta-data about scan
 */

$key = "scanner/player/" . $facebook_id;
$redis->hset ($key, "update_date", time());
$redis->hset ($key, "payload", $file);
$redis->hset ($key, "songs", $songs);
$redis->hset ($key, "delta", $delta);

$meta = array
(
    'songs' => $songs,
    'delta' => $delta,
);
echo json_encode ($meta);
?>

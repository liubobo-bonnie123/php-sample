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

function getTracks ($scraper, $track)
{
    $artist = $track['artist'];

    $title = $track['title'];
    $title = preg_replace('/\[.*\]/', '', $title); 

    $reply = $scraper->getTracks ($artist, $title);

    if (! isset ($reply['Success']) || $reply['Success'] != '1')
    {
        error_log ("No match: Artist {$artist} Title {$title}");
        return false;
    }

    if (count ($reply['Tracks']) == 1)
    {
        $medianet_track = $reply['Tracks'][0];
        if ((strpos($medianet_track["Title"], 'remix') !== false) ||
            (strpos($medianet_track["Title"], 'cover') !== false) ||
            (strpos($medianet_track["Title"], 'karaoke') !== false)
        )
		{
           return false;
        }
    }
    else
    {
        return false;
        /*
        $medianet_track['MnetId'] = 0;
        $medianet_track['PriceTag']['Currency'] = '';
        $medianet_track['PriceTag']['Amount'] = '';
        */
    }

    echo implode 
    (
        ',',
        array 
        (
            $track['_id'],
            $medianet_track['MnetId'], 
            $medianet_track['PriceTag']['Amount'],
            $medianet_track['PriceTag']['Currency'], 
        )
    );
    echo "\n";
	$medianet["_id"] = $track['_id'],
    $medianet["MnetId"] = $medianet_track['MnetId'], 
    $medianet["Price"] = $medianet_track['PriceTag']['Amount'],
    $medianet["Currency"] = $medianet_track['PriceTag']['Currency'],
    return $medianet;
}


function main ()
{
    echo implode (',', array ("id", "medianet_id", "price", "currency")). "\n";

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

    try
    {
        $dbh = \PlayGigIt\DB\Handle::open
        (
            $config->db_user0 ('host'),
            $config->db_user0 ('port'),
            $config->db_user0 ('user'),
            $config->db_user0 ('pass'),
            $config->db_user0 ('db')
        );
    }
    catch (\Exception $e)
    {
        die ("Can't connect to database: " . $e->getMessage());
    }

    $client = new \PlayGigIt\HTTP\Client();
    $scraper = new \PlayGigIt\Music\Store\MediaNet\Scraper 
    (
        $client,
        $config,
        $dbh
    );

	$song = new \PlayGigIt\Model\Models\Song\MediaNet ($dbh);
	
    $sth = $dbh->prepare ("select * from clickr.music_licensed order by _id");
    $sth->execute();
    $tracks = $sth->fetchAll();
    foreach ($tracks as $track)
    {
        if(empty($track["title"])||empty($track["artist"])//skip the music without full info
		{
		    continue;
		}
		$sth = $dbh->prepare ("select medianet_id from clickr.game_medianet where _id =".$track["_id"]);
		$sth->execute();
		$row = $sth->fetchAll();//looking for the new added licensed music
		if(!empty($row))
		{
		    continue;
		}
		$medianet = getTracks ($scraper, $track);
		if(!empty($medianet))
		{
			$song->add($medianet["_id"], $medianet["MnetId"],$medianet["Price"],$medianet["Currency"]);//add to database
		}
    }
}

exit(main());
?>

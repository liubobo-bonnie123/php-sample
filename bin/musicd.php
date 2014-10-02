#!/usr/bin/php
<?php

/*
 *
 */

$port = 5555;
$context = new ZMQContext(1);
$pidfile = "/web/playgigit/etc/musicd.pid";
file_put_contents ($pidfile, getmypid());

//  Socket to talk to clients
$responder = new ZMQSocket ($context, ZMQ::SOCKET_REP);
$responder->bind("tcp://*:{$port}");
echo "musicd: Listening on port {$port}\n";

while (true) 
{
    //  Wait for next request from client
    $request = $responder->recv();

    list ($facebook_id, $file) = explode (" ", $request, 2);

    printf ("musicd: %s\n", $request);

    $facebook_id = escapeshellarg ($facebook_id);

    $output = array();
    exec ("php /web/playgigit/bin/scanner_import.php $facebook_id $file", $output);

    //  Send reply back to client
    $responder->send (implode ("\n", $output));
}



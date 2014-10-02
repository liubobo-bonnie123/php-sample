#!/usr/bin/php
<?php

/*
 *
 */

$port = 5556;
$dir = "/web/playgigit/udata/analytics/logs";
$pidfile = "/web/playgigit/etc/logd.pid";
file_put_contents ($pidfile, getmypid());

$context = new ZMQContext(1);

//  Socket to talk to clients
$responder = new ZMQSocket ($context, ZMQ::SOCKET_REP);
$responder->bind("tcp://*:{$port}");
echo "logd: Listening on port {$port}\n";

while (true) 
{
    $request = $responder->recv();
    printf ("logd: %s\n", $request);
    $logfile = implode ("/", array ($dir, date ('Ymd') . ".analytics.log"));
    $status = file_put_contents ($logfile, $request . "\n", FILE_APPEND | LOCK_EX);
    $responder->send ($status === false ? 0 : $status);
}



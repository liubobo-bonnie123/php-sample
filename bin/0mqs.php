<?php

/*
 *
 */

$context = new ZMQContext(1);

//  Socket to talk to clients
$responder = new ZMQSocket ($context, ZMQ::SOCKET_REP);
$responder->bind("tcp://*:5555");
echo "Running...\n";

while (true) 
{
    //  Wait for next request from client
    $request = $responder->recv();
    printf ("%s\n", $request);

    //  Send reply back to client
    $responder->send("ok");
}



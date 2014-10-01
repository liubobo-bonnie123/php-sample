<?php

/*
 *
 */

$this->respond
(
    // TODO: In production, remove GET from the allowed list of methods
    array ('GET', 'POST'),
    '/?',
    function ($request, $response, $service) 
    {
        header ('Content-Type: text/html');

        try
        {
            $app = new \PlayGigIt\App\App\Clickr();
        }
        catch (\Exception $e)
        {
            // TODO: CLICK-550
            $app->handleException ($response, PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR, $e);
            return \PlayGigIt\Error\Message::html (PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR);
        }

        try
        {
            $service->validateParam('tracker', "Signed request missing")->notNull();
        }
        catch (\Exception $e)
        {
            // TODO: CLICK-550
            $app->handleException ($response, PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR, $e);
            return \PlayGigIt\Error\Message::html (PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR);
        }

        // this needs to be a signed request
        $facebook_id = $request->param('tracker');

        \PlayGigIt\Analytics\Logger::log 
        (
            \PlayGigIt\Analytics\Constants::$VERB_DOWNLOAD,
            \PlayGigIt\Analytics\Constants::$NOUN_SCANNER,
            array 
            (
                'facebook_id' => $facebook_id
            )
        );

        $service->app = $app;
        $template = implode ('/', array ($app->config->dir('root'), 'templates', 'scanner_download.phtml'));
        return $service->render ($template);
    }
);

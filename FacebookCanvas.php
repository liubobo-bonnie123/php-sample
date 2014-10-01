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
            return $app->handleException ($response, PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR, $e);
        }

        try
        {
            $service->validateParam('signed_request', "Signed request missing")->notNull();
        }
        catch (\Exception $e)
        {
            /*
             * This is one of 2 user-facing API calls that requires a pleasant error message in HTML
             * when something goes wrong. We ignore the JSON returned by handleException and 
             * render our own error page. 
             */
            $app->handleException ($response, PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR, $e);

            // TODO: CLICK-550
            return \PlayGigIt\Error\Message::html (PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR);
        }

        $service->escape = function ($str) 
        {
            return htmlentities($str);
        };

        $service->app = $app;
        $service->signed_request = $request->param ('signed_request');

        $template = implode ('/', array ($app->config->dir('root'), 'templates', 'facebook_canvas.phtml'));
        return $service->render ($template);
    }
);

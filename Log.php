<?php

$this->respond
(
    array ('GET', 'POST'),
    '/?',
    function ($request, $response, $service)
    {
        header('Content-Type: application/json');

        try
        {
            $app = new \PlayGigIt\App\App\Clickr();
            $app->fetchTokenFromAuthHeader();
            $context = $app->getUser ($request, $service, $app);
        }
        catch (\Exception $e)
        {
            return $app->handleException ($response, PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR, $e);
        }

        try
        {
                $service->validateParam('verb')->notNull()->isChars('a-zA-Z0-9_');
                $service->validateParam('noun')->notNull()->isChars('a-zA-Z0-9_');
        }
        catch (\Exception $e)
        {
                return $app->handleClientError ($response, PlayGigIt\HTTP\Codes::$BAD_REQUEST, "Bogus verb/noun");
        }

        $raw = file_get_contents('php://input');

        if (! $raw)
        {
            return $app->handleClientError ($response, PlayGigIt\HTTP\Codes::$BAD_REQUEST, "Missing analytics payload");
        }

        $verb = $request->param('verb');
        $noun = $request->param('noun');

        $cooked = json_decode ($raw, true);

        if (! $cooked)
        {
            \PlayGigIt\Logger\Log::warning ("Invalid JSON in /analytics/log verb {$verb} noun {$noun}: " .  \PlayGigIt\Tools\JSON::pretty_print_error (json_last_error()) . ": {$raw}");
            $cooked = array ( 'error' => $raw );
        }

        try
        {
            \PlayGigIt\Analytics\Logger::log ($verb, $noun, $cooked);
        }
        catch (\Exception $e)
        {
            return $app->handleException ($response, PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR, $e);
        }

        $view = new \PlayGigIt\View\JSON();
        return $view->render (array());
    }
);

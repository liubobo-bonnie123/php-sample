<?php

$this->respond
(
    'GET',
    '/?',
    function ($request, $response, $service)
    {
        header('Content-Type: application/json');

        try
        {
            $app = new \PlayGigIt\App\App\Clickr();
            $context = $app->getUser ($request, $service, $app);
        }
        catch (\Exception $e)
        {
            return $app->handleException ($response, PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR, $e);
        }

        if (! $context->is_success())
        {
            return $app->handleErrorContext ($response, $context);
        }

        try
        {
            $service->validateParam('payment', 'Invalid payment id')->notNull()->isInt();
            $service->validateParam('character', 'Invalid character id')->notNull()->isInt();
            //Once client added this parameter uncomment line below
            //$service->validateParam('song', 'Invalid song id')->notNull()->isInt();
        }
        catch (\Exception $e)
        {
            return $app->handleException ($response, PlayGigIt\HTTP\Codes::$INTERNAL_SERVER_ERROR, $e);
        }

        $character = new PlayGigIt\Model\Models\Character ($context->userShard);

        try
        {
            $character->load ($context->player->user_id, $request->param('character'));
        }
        catch (\Exception $e)
        {
            return $app->handleException ($response, $e->getCode(), $e);
        }
        
        $view = new \PlayGigIt\View\JSON();

        $payment_id= $request->param('payment');
        $song_id= $request->param('song');

        $purchase = \PlayGigIt\Facebook\Graph::purchase
        (
            $payment_id,
            $app->config->facebook('app_access_token')
        );

        \PlayGigIt\Analytics\Logger::log
        (
            \PlayGigIt\Analytics\Constants::$VERB_FACEBOOK,
            \PlayGigIt\Analytics\Constants::$NOUN_PURCHASE,
            array
            (
                "character" => $character,
                "payment" => $purchase,
                "song" => $song_id
            )
        );

        $wot = array
        (
            "tickets" => $context->player->values["tickets"],
            "cash" => $context->player->values["cash"]
        );

        return $view->render ($wot);
    }
);

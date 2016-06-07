<?php

namespace Nomisoft\RoyalMailTrackingApi;

/**
 * Class RoyalMail
 * @package Nomisoft\RoyalMailTrackingApi
 */
class RoyalMail
{

    /**
     * @param $clientId
     * @param $clientSecret
     * @param $appId
     *
     * @return Tracker
     */
    public static function init($clientId, $clientSecret, $appId)
    {
        $client = new Client(__DIR__.'/Tracking_API_V1_1_1.wsdl', $clientId, $clientSecret, $appId);
        return new Tracker($client);
    }
}

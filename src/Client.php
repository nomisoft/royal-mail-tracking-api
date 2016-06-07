<?php

namespace Nomisoft\RoyalMailTrackingApi;

/**
 * Class Client
 * @package Nomisoft\RoyalMailTrackingApi
 */
class Client
{

    /**
     * @var \SoapClient
     */
    private $soapClient;

    /**
     * @var
     */
    private $appId;

    /**
     * @param $wsdl
     * @param $clientId
     * @param $clientSecret
     * @param $appId
     */
    public function __construct($wsdl, $clientId, $clientSecret, $appId)
    {
        $this->appId = $appId;
        $this->soapClient = new \SoapClient($wsdl, array(
            'location' => "https://api.royalmail.net/tracking",
            'trace' => 1,
            'stream_context' => stream_context_create(array('http' =>
                array(
                    'header'  => "Accept: application/soap+xml\r\nX-IBM-Client-Id: ".$clientId."\r\nX-IBM-Client-Secret: ".$clientSecret
                )
            ))
        ));
    }

    /**
     * @return array
     */
    private function getIntegrationHeader()
    {
        return array(
            'integrationHeader' => array(
                'dateTime' => date('Y-m-d\TH:i:s'),
                'version' => '1.0',
                'identification'=>array(
                    'applicationId' => $this->appId,
                    'transactionId' => time()
                )
            )
        );
    }

    /**
     * @param $method
     * @param null $params
     *
     * @return mixed
     */
    public function call($method, $params = null)
    {
        $params = array_merge($this->getIntegrationHeader(), (array)$params);
        $response = $this->soapClient->$method( $params );
        return $response;
    }

    /**
     * @return array
     */
    public function getLastRequest()
    {
        return array(
            'request' => array(
                'header' => $this->soapClient->__getLastRequestHeaders(),
                'body' => $this->soapClient->__getLastRequest()
            ),
            'response' => array(
                'header' => $this->soapClient->__getLastResponseHeaders(),
                'body' => $this->soapClient->__getLastResponse()
            )
        );
    }
}

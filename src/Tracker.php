<?php

namespace Nomisoft\RoyalMailTrackingApi;

/**
 * Class Tracker
 * @package Nomisoft\RoyalMailTrackingApi
 */
class Tracker
{

    /**
     * @var
     */
    private $client;

    /**
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * @param $trackingNumber
     *
     * @return array|bool
     * @throws RoyalMailException
     */
    public function getSingleItemHistory($trackingNumber)
    {
        $response = $this->client->call('getSingleItemHistory', array('trackingNumber' => $trackingNumber));
        if ($response) {
            if (isset($response->integrationFooter->errors->error)) {
                throw new RoyalMailException($response->integrationFooter->errors);
            } elseif (property_exists($response, 'trackDetail')) {
                $history = array();
                if (!is_array($response->trackDetail)) {
                    $response->trackDetail = array( $response->trackDetail );
                }
                foreach ($response->trackDetail as $trackDetail) {
                    $history[] = array(
                        'datetime' => \DateTime::createFromFormat('Y-m-d H:i:s', $trackDetail->trackDate . ' ' . $trackDetail->trackTime),
                        'location' => $trackDetail->trackPoint,
                        'status'   => property_exists($trackDetail, 'header') ? $trackDetail->header : 'Unknown'
                    );
                }
                return $history;
            }
        }
        return false;
    }

    /**
     * @param $trackingNumber
     *
     * @return array|bool
     * @throws RoyalMailException
     */
    public function getSingleItemSummary($trackingNumber)
    {
        $response = $this->client->call('getSingleItemSummary', array('trackingNumber'=>$trackingNumber));
        if ($response) {
            if (isset($response->integrationFooter->errors->error)) {
                throw new RoyalMailException($response->integrationFooter->errors);
            } elseif (property_exists($response, 'itemSummary')) {
                return array(
                    'datetime' => \DateTime::createFromFormat('Y-m-d H:i:s', $response->itemSummary->eventDate . ' ' . $response->itemSummary->eventTime),
                    'status'   => $response->itemSummary->header,
                    'summary'  => $response->itemSummary->summaryLine,
                );
            }
        }
        return false;
    }

    /**
     * @param $trackingNumbers
     *
     * @return array|bool
     * @throws RoyalMailException
     */
    public function getMultiItemSummary($trackingNumbers)
    {
        $response = $this->client->call('getMultiItemSummary', array('trackingNumbers'=>$trackingNumbers));
        if ($response) {
            if (isset($response->integrationFooter->errors->error)) {
                throw new RoyalMailException($response->integrationFooter->errors);
            } elseif (property_exists($response, 'itemSummaries') && property_exists($response->itemSummaries, 'itemSummary')) {
                if (!is_array($response->itemSummaries->itemSummary)) {
                    $response->itemSummaries->itemSummary = array($response->itemSummaries->itemSummary);
                }
                $summary = array();
                foreach ($response->itemSummaries->itemSummary as $itemSummary) {
                    $summary[ $itemSummary->trackingNumber ] = array(
                        'datetime' => \DateTime::createFromFormat('Y-m-d H:i:s', $itemSummary->eventDate . ' ' . $itemSummary->eventTime),
                        'status'   => $itemSummary->header,
                        'summary'  => $itemSummary->summaryLine,
                    );
                }

                return $summary;
            }
        }
        return false;
    }

    /**
     * @param $trackingNumber
     *
     * @return array|bool
     * @throws RoyalMailException
     */
    public function getProofOfDelivery($trackingNumber)
    {
        $response = $this->client->call('getProofOfDelivery', array('trackingNumber'=>$trackingNumber));
        if ($response) {
            if (isset($response->integrationFooter->errors->error)) {
                throw new RoyalMailException($response->integrationFooter->errors);
            } elseif (property_exists($response, 'wSImageResponse')) {
                return array(
                    'datetime' => \DateTime::createFromFormat('Y-m-d\TH:i:s', $response->wSImageResponse->signatureTime),
                    'name'     => $response->wSImageResponse->printedName
                );
            }
        }
        return false;
    }
}

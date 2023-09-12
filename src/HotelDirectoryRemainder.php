<?php
namespace src;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Pool;

/**
 *
 */
class HotelDirectoryRemainder
{
    /**
     * @var
     */
    private $directoryUrl;

    /**
     * @var
     */
    private $batchSize;

    /**
     * @param $directoryUrl
     * @param $batchSize
     */
    function __construct($directoryUrl, $batchSize) {
        $this->directoryUrl = $directoryUrl;
        $this->batchSize = $batchSize;
    }

    /**
     * @return void
     */
    public function hotelDirectory() {
        if ($this->directoryUrl) {
            $directoryJson = file_get_contents($this->directoryUrl);
            $directoryData = json_decode($directoryJson, true);
            $count =  $this->checkRemainder($directoryData, $this->batchSize);
            echo "Total ".$count." GIATA-IDs divisible by rating without a remainder.\n";
        }
    }

    /**
     * @param $directoryData
     * @param $batchSize
     * @return string|void
     */
    public function checkRemainder($directoryData, $batchSize)
    {
        $successfullyCompeted = $failedToCompete = [];
        $client = new Client();
        if ($directoryData && isset($directoryData['urls'])) {
            $urlBatches = array_chunk($directoryData['urls'], $batchSize);
            $requests = function ($urls) use ($client) {
                foreach ($urls as $url) {
                    yield new Request('GET', $url);
                }
            };
            foreach ($urlBatches as $batch) {
                $pool = new Pool($client, $requests($batch), [
                    'fulfilled' => $this->successfullyCompeted($successfullyCompeted),
                    'rejected' => $this->failedToCompete($failedToCompete),
                ]);
                $promise = $pool->promise();
                $promise->wait();
            }
            return count($successfullyCompeted);
        }
    }

    /**
     * @param $responses
     * @return \Closure
     */
    private function successfullyCompeted(&$responses): \Closure
    {
        return function ($response, $index) use (&$responses) {
            $contents = $response->getBody()->getContents();
            if ($contents) {
                $contents = json_decode($contents);
                if (isset($contents) && isset($contents->ratings)) {
                    $ratingValue = $contents->ratings[0]->value;
                    $giataId = $contents->giataId;
                    if (!is_numeric($ratingValue) && !is_int($ratingValue) && strpos($ratingValue, ',') !== false){
                        $ratingValue = str_replace(',', '.', $ratingValue);
                    }
                    $this->getPositiveResponseArr($responses, $ratingValue, $giataId);
                }
            }
        };
    }

    /**
     * @param $responses
     * @return \Closure
     */
    private function failedToCompete(&$responses): \Closure
    {
        return function ($reason, $index) use (&$responses) {
            $responses[] = "Request for URL $index failed: $reason";
        };
    }

    /**
     * @param $responses
     * @param $ratingValue
     * @param $giataId
     * @return void
     */
    function getPositiveResponseArr(&$responses, $ratingValue, $giataId)
    {
        if ($ratingValue >= 2 && $ratingValue <= 7 && $giataId % $ratingValue === 0) {
            $responses[] = ['glataid' => $giataId, 'ratings' => $ratingValue];
        }
    }
}
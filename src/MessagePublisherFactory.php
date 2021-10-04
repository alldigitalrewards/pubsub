<?php

namespace AllDigitalRewards\PubSub;

use Google\Cloud\PubSub\PubSubClient;

class MessagePublisherFactory
{
    /**
     * @var PubSubClient
     */
    private static $publisher;

    /**
     * @param string $keyFile
     * @param string $projectId
     * @return PubSubClient
     */
    public static function getInstance(
        string $keyFile,
        string $projectId
    ): PubSubClient {
        if (self::$publisher === null) {
            $publisher = new PubSubClient(
                [
                    'projectId' => $projectId,
                    'keyFile' => json_decode($keyFile, true)
                ]
            );
            self::$publisher = $publisher;
        }

        return self::$publisher;
    }
}

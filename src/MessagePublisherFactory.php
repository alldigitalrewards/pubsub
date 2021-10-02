<?php

namespace AllDigitalRewards\PubSub;

use Exception;
use Google\Cloud\PubSub\PubSubClient;

class MessagePublisherFactory
{
    /**
     * @var PubSubClient
     */
    private static $publisher;

    /**
     * @param string $topicName
     * @param string $subscriptionName
     * @param string $keyFile
     * @param string $projectId
     * @return PubSubClient
     */
    public static function getInstance(
        string $topicName,
        string $subscriptionName,
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
            try {
                try {
                    $topic = $publisher->createTopic($topicName);
                } catch (Exception $exception) {
                    //exception is thrown when Topic exists so just fetch lazy
                    $topic = $publisher->topic($topicName);
                }
                if ($topic->subscription($subscriptionName)->exists() === false) {
                    $subscription = $topic->subscribe($subscriptionName);
                    $subscription->create();
                }
            } catch (Exception $exception) {
                //exist throws Exception
            }
            self::$publisher = $publisher;
        }

        return self::$publisher;
    }
}

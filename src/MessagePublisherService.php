<?php

namespace AllDigitalRewards\PubSub;

use Exception;
use Google\Cloud\PubSub\PubSubClient;
use Google\Cloud\PubSub\Topic;

class MessagePublisherService
{
    /**
     * @var Topic
     */
    private $topic;
    /**
     * @var string
     */
    private $projectId;
    /**
     * @var string
     */
    private $keyFile;
    /**
     * @var PubSubClient
     */
    private $client;
    /**
     * @var Message
     */
    private $message;

    /**
     * TOPIC will push messages to your SUBSCRIBERS
     * $topicName: Unique Name for your subscribers ex. DEV_SOMESERVICE_REPORTS
     * $subscriptionName: Unique Name for your subscribers ex. DEV_SOMESERVICE_REPORT_SUBSCRIPTION
     * $projectId: The Google project ID
     * $keyFile: The Google project key
     *
     * @param string $projectId
     * @param string $keyFile
     */
    public function __construct(
        string $projectId,
        string $keyFile
    ) {
        $this->setProjectId($projectId);
        $this->setKeyFile($keyFile);
    }

    /**
     * @return Topic
     */
    private function getTopic(): Topic
    {
        if ($this->topic === null) {
            try {
                $topic = $this->getClient()->createTopic($this->getMessage()->getTopicName());
            } catch (Exception $exception) {
                //exception is thrown when Topic exists so just fetch lazy
                $topic = $this->getClient()->topic($this->getMessage()->getTopicName());
            }
            $this->topic = $topic;
        }

        return $this->topic;
    }

    /**
     * @param Topic $topic
     */
    public function setTopic(Topic $topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getProjectId(): string
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     */
    public function setProjectId(string $projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getKeyFile(): string
    {
        return $this->keyFile;
    }

    /**
     * @param string $keyFile
     */
    public function setKeyFile(string $keyFile)
    {
        $this->keyFile = $keyFile;
    }

    /**
     * @return PubSubClient
     */
    public function getClient(): PubSubClient
    {
        if (isset($this->client) === false) {
            $this->client = MessagePublisherFactory::getInstance(
                $this->getKeyFile(),
                $this->getProjectId()
            );
        }
        return $this->client;
    }

    /**
     * @param PubSubClient $client
     */
    public function setClient(PubSubClient $client)
    {
        $this->client = $client;
    }

    /**
     * @return Message
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    /**
     * @param Message $message
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Pulls first off
     *
     * @param Message $messageConfig
     * @return array
     * @throws PubSubServiceException
     */
    public function pullMessage(Message $messageConfig): array
    {
        try {
            $this->setMessage($messageConfig);

            $message = [];
            $messages = $this->getTopic()
                ->subscription($messageConfig->getSubscriptionName())
                ->pull(['returnImmediately' => true]);
            if (empty($messages) === false) {
                $pulledMessage = $messages[0];
                $message = $pulledMessage->attributes();
                // acknowledge PULLED message
                $this->getTopic()
                    ->subscription($messageConfig->getSubscriptionName())
                    ->acknowledge($pulledMessage);
            }
            return $message;
        } catch (Exception $exception) {
            throw new PubSubServiceException($exception->getMessage());
        }
    }

    /**
     * $data must be array of key/value string pairs
     * ex.
     * [
     *  'key1' => $someValue1, //string
     *  'key2' => $someValue2 //string
     * ]
     * @param Message $message
     * @param array $data
     * @return array
     * @throws PubSubServiceException
     */
    public function publishMessage(Message $message, array $data): array
    {
        $this->setMessage($message);
        $this->setSubscription();

        $published = $this->getTopic()
            ->publish(
                [
                    'data' => $message->getSubscriptionName(),
                    'attributes' => $data
                ]
            );
        if (empty($published) === false) {
            return $published;
        }
        throw new PubSubServiceException('Publish message failed');
    }

    private function setSubscription()
    {
        try {
            if ($this->getTopic()->subscription($this->getMessage()->getSubscriptionName())->exists() === false) {
                $subscription = $this->getTopic()->subscribe($this->getMessage()->getSubscriptionName());
                $subscription->create();
            }
        } catch (\Exception $exception) {
            //SDK throws error when exists
        }
    }
}

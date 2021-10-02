<?php

namespace AllDigitalRewards\PubSub;

use Exception;
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
    private $subscriptionName;
    /**
     * @var string
     */
    private $topicName;
    /**
     * @var string
     */
    private $projectId;
    /**
     * @var string
     */
    private $keyFile;

    /**
     * TOPIC will push messages to your SUBSCRIBERS
     * $topicName: Unique Name for your subscribers ex. DEV_SOMESERVICE_REPORTS
     * $subscriptionName: Unique Name for your subscribers ex. DEV_SOMESERVICE_REPORT_SUBSCRIPTION
     * $projectId: The Google project ID
     * $keyFile: The Google project key
     *
     * @param string $topicName
     * @param string $subscriptionName
     * @param string $projectId
     * @param string $keyFile
     */
    public function __construct(
        string $topicName,
        string $subscriptionName,
        string $projectId,
        string $keyFile
    ) {
        $this->setTopicName($topicName);
        $this->setSubscriptionName($subscriptionName);
        $this->setProjectId($projectId);
        $this->setKeyFile($keyFile);
    }

    /**
     * @return Topic
     */
    private function getTopic(): Topic
    {
        if ($this->topic === null) {
            $this->topic = MessagePublisherFactory::getInstance(
                $this->getTopicName(),
                $this->getSubscriptionName(),
                $this->getKeyFile(),
                $this->getProjectId()
            )->topic($this->getTopicName());
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
    public function getTopicName(): string
    {
        return $this->topicName;
    }

    /**
     * @param string $topicName
     */
    public function setTopicName(string $topicName)
    {
        $this->topicName = $topicName;
    }

    /**
     * @return string
     */
    public function getSubscriptionName(): string
    {
        return $this->subscriptionName;
    }

    /**
     * @param string $subscriptionName
     */
    public function setSubscriptionName(string $subscriptionName)
    {
        $this->subscriptionName = $subscriptionName;
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
     * Pulls first off
     *
     * @return array
     * @throws PubSubServiceException
     */
    public function pullMessage(): array
    {
        try {
            $message = [];
            $messages = $this->getTopic()
                ->subscription($this->getSubscriptionName())
                ->pull(['returnImmediately' => true]);
            if (empty($messages) === false) {
                $pulledMessage = $messages[0];
                $message = $pulledMessage->attributes();
                // acknowledge PULLED message
                $this->getTopic()
                    ->subscription($this->getSubscriptionName())
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
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function publishMessage(array $data): array
    {
        $published = $this->getTopic()
            ->publish(
                [
                    'data' => $this->getSubscriptionName(),
                    'attributes' => $data
                ]
            );
        if (empty($published) === false) {
            return $published;
        }
        throw new PubSubServiceException('Publish message failed');
    }
}

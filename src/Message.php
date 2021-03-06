<?php

namespace AllDigitalRewards\PubSub;

class Message
{
    /**
     * @var string
     */
    private $subscriptionName;
    /**
     * @var string
     */
    private $topicName;

    /**
     * TOPIC will push messages to your SUBSCRIBERS
     * $topicName: Unique Name for your subscribers' ex. DEV_SOMESERVICE_REPORTS
     * $subscriptionName: Unique Name for your subscribers' ex. DEV_SOMESERVICE_REPORT_SUBSCRIPTION

     * @param string $topicName
     * @param string $subscriptionName
     */
    public function __construct(string $topicName, string $subscriptionName)
    {
        $this->setTopicName($topicName);
        $this->setSubscriptionName($subscriptionName);
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
}

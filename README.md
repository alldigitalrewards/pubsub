# PubSub Message Publisher/Consumer

##Usage

##Setup
```bash
scenario: Bucket Queue (Topic) to publish messages to Consumer(s) (subscriptions)
Publishing to a Report Queue:
Note: unique names for Topics, i.e. _REPORTS, _TRANSACTION_EMAIL etc. 
```
```bash
Create new instance
* If Topic and/or Subscription names dont exist they will be created
* naming convention is up to you.

$environment = getenv('ENVIRONMENT'); //dev/qa/prod
$topicName = $environment . '_SOMESERVICE_REPORTS';
$subscriptionName = $environment . '_SOMESERVICE_REPORT_SUBSCRIPTION';
$projectId = getenv('PROJECT_ID'); //The Google project ID
$keyFile = getenv('PUBSUB_KEYFILE'); //The Google project key

$messageService = new MessagePublisherService(
  $topicName,
  $subscriptionName,
  $projectId,
  $keyFile
);
```

##Publish
```bash
* array of key/value string pairs as many, ex.
* throws PubSubServiceException
$messageService->publish(
    [
        'key1' => $someValue1, //string
        'key2' => $someValue2 //string
    ]
);
```

##Pull Message
```bash
* returns array 
* throws PubSubServiceException
$message = $messageService->pullMessage($subscriptionName)
print_r($message[0]); //$someValue1
print_r($message[1]); //$someValue2
```

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

$projectId = getenv('PROJECT_ID'); //The Google project ID
$keyFile = getenv('PUBSUB_KEYFILE'); //The Google project key

$messageService = new MessagePublisherService(
  $projectId,
  $keyFile
);
```

##Publish
```bash
* array of key/value string pairs as many, ex.
* throws PubSubServiceException
$message = new Message(
    'DEV_TESTSERVICE2_TOPIC',
    'DEV_TESTSERVICE2_SUBSCRIPTION',
);
$messageService->publish(
    $message,
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
$messageConfig = new Message(
    'DEV_TESTSERVICE2_TOPIC',
    'DEV_TESTSERVICE2_SUBSCRIPTION',
);
$message = $messageService->pullMessage($messageConfig)
print_r($message['key1']); //$someValue1
print_r($message['key2']); //$someValue2
```

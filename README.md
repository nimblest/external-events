# External Events Package

*built by Dano*


##Purpose
Easily connect your PHP Laravel or Lumen app to SNS topics (and soon to be coming Kafka). This will allow you to outsource your data up to other data platforms to push Event Driven Designs. This package is solely for sending data to topics and not for consuming them.

##Setup
First create a config file called `external_events` within yoru configs folder. You will want to copy this section of code and fill it in with your aws credentials. You will get the key and secret from your IAM user in AWS. The `EVENT_ENV` will allow you to set up staging and production topics to test on. 

```
<?php
return [
    'aws_key' => env('AWS_KEY'),
    'aws_region' => env('AWS_REGION'),
    'aws_secret' => env('AWS_SECRET'),
    'environment' => env('EVENT_ENV', 'staging'),
];

```

You will need to install the following packages: 

```
composer require spatie/laravel-fractal
composer require aws/aws-sdk-php-laravel
```

When creating an event now you will need to add a couple of fields and functions in order to get the correct data sending up to SNS. First add the `ExternalEventsInterface` interface to your eventYou will want to add a public variable of `$topic` at the top and in the construct of the event add in the ARN of the topic that you are trying to connect to. You will then add 3 functions `pack`, `name`, and `shouldNotSend`. It will look something like this:

```
<?php

namespace App\Events;

use App\Transformers\UserTransformer;
use ExternalEvents\Interfaces\ExternalEventsInterface;
use App\Models\User;

class UserUpdated extends Event implements ExternalEventsInterface
{
    public $topic;
    public $user;

    public function __construct(
        User $user
    ) {
        // Example topic might be production-user-update-bus and staging-user-update-bus
        $this->topic = sprintf('arn:aws:sns:%s-YOUR-TOPIC', config('external_events.environment'));
        $this->user = $user;
    }

    public function pack(): array
    {
        return [
            'transformer' => UserTransformer::class,
            'item' => $this->user,
            'includes' => [
                'address',
            ]
        ];
    }

    public function name(): string
    {
        return 'user_updated';
    }

    public function shouldNotSend(): bool
    {
        return false;
    }
}
```

###The Functions

```
public function pack(): array
```

The pack function is to package up your data and prepare it for sending it up to SNS. There are 3 options in the array that you can send:

`transformer`: This is a spatie/fractal transformer that you want to run your object through to set the data up for your queue.
`item`: The object that you want to send up.
`includes`: These are includes on your Transformer for adding in relationships.

```
public function name(): string
```

We pass in this name in the root data object so you can do conditional logic on the topic in your lambdas.

```
public funciton shouldNotSend(): bool
```

This is just a bool that will allow you to have optional triggers of when to not send this event to SNS.


Contact me:

https://twitter.com/danodev

http://danogillette.com

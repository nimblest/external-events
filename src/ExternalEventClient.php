<?php

namespace ExternalEvents;

use Aws\Sns\SnsClient;

class ExternalEventClient
{
    private $topic;

    public function setTopic(string $topic): void
    {
        $this->topic = $topic;
    }

    public function handle(string $message, string $name): void
    {
        $client = SnsClient::factory([
            'credentials' =>[
                'key' => config('external_events.aws_key'),
                'secret' => config('external_events.aws_secret')
            ],
            'version' => '2010-03-31',
            'region'  => config('external_events.aws_region')
        ]);

        $payload = [
            'TopicArn' => $this->topic,
            'Message' => $message,
            'MessageStructure' => 'string'
        ];

        $this->publish($payload, $client);
    }

    private function publish(array $payload, $client): void
    {
        try {
            $client->publish($payload);
        } catch (Exception $e) {
        }
    }
}

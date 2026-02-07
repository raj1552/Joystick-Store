<?php
/**
 * SQS helper for decoupled processing on AWS.
 * Set AWS_REGION, SQS_QUEUE_URL, and AWS credentials (or IAM role) in environment.
 * If SQS_QUEUE_URL is not set, messages are not sent (safe for local dev).
 */

function sqs_send_message($event, array $body) {
    $queue_url = getenv('SQS_QUEUE_URL');
    if (empty($queue_url)) {
        return; // SQS not configured (e.g. local dev)
    }

    $payload = [
        'event' => $event,
        'body'  => $body,
        'at'    => date('c'),
    ];

    $vendor = dirname(__DIR__) . '/vendor/autoload.php';
    if (!is_file($vendor)) {
        error_log('Joystick-Store SQS: vendor/autoload.php not found. Run composer install.');
        return;
    }

    require_once $vendor;

    try {
        $client = new \Aws\Sqs\SqsClient([
            'version' => 'latest',
            'region'  => getenv('AWS_REGION') ?: 'us-east-1',
        ]);

        $client->sendMessage([
            'QueueUrl'    => $queue_url,
            'MessageBody' => json_encode($payload),
            'MessageAttributes' => [
                'Event' => [
                    'DataType'    => 'String',
                    'StringValue' => $event,
                ],
            ],
        ]);
    } catch (Exception $e) {
        error_log('Joystick-Store SQS send failed: ' . $e->getMessage());
    }
}

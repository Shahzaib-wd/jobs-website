<?php
require 'vendor/autoload.php';
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$conn = new PDO("mysql:host=localhost;dbname=jobs","root","");
$subs = $conn->query("SELECT * FROM push_subscriptions")->fetchAll(PDO::FETCH_ASSOC);

$auth = [
    'VAPID' => [
        'subject' => 'mailto:admin@example.com',
        'publicKey' => 'YOUR_PUBLIC_VAPID_KEY',
        'privateKey' => 'YOUR_PRIVATE_VAPID_KEY'
    ]
];

$webPush = new WebPush($auth);

foreach($subs as $sub){
    $subscription = Subscription::create([
        'endpoint' => $sub['endpoint'],
        'keys' => [
            'p256dh' => $sub['p256dh'],
            'auth' => $sub['auth']
        ]
    ]);

    $webPush->sendOneNotification(
        $subscription,
        json_encode([
            'title'=>'New Job Posted!',
            'body'=>'Check out the latest job now!',
        ])
    );
}
?>
<?php
$data = json_decode(file_get_contents('php://input'), true);
$endpoint = $data['endpoint'] ?? '';
$p256dh = $data['keys']['p256dh'] ?? '';
$auth = $data['keys']['auth'] ?? '';

if($endpoint && $p256dh && $auth){
    $conn = new PDO("mysql:host=localhost;dbname=jobs", "root", "");
    $stmt = $conn->prepare("INSERT IGNORE INTO push_subscriptions (endpoint,p256dh,auth) VALUES (?,?,?)");
    $stmt->execute([$endpoint, $p256dh, $auth]);
    echo json_encode(['status'=>'success']);
}
?>
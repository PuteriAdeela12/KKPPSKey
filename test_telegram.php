<?php
$token = "TOKEN_BOT_KAMU";
$chat_id = "CHAT_ID_KAMU_SENDIRI";

$url = "https://api.telegram.org/bot$token/sendMessage";

$data = [
  'chat_id' => $chat_id,
  'text' => 'TEST DIRECT FROM SERVER'
];

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
$res = curl_exec($ch);
curl_close($ch);

echo $res;

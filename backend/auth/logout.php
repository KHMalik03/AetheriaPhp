<?php

$apiUrl = getenv('API_URL') ?: 'http://localhost/api';

$ch = curl_init($apiUrl . '/logout');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_CUSTOMREQUEST  => 'POST',
    CURLOPT_COOKIE         => 'PHPSESSID=' . ($_COOKIE['PHPSESSID'] ?? ''),
]);
curl_exec($ch);
curl_close($ch);

setcookie('PHPSESSID', '', time() - 3600, '/');

header("Location: /index.php");
exit;


<?php

// Memuat Composer Autoload
require_once __DIR__ . "/../vendor/autoload.php";

// Membaca file .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// Mengambil API Key dari .env
define("NEWS_API_KEY", $_ENV["NEWS_API_KEY"]);

// Fungsi HTTP GET Request
function http_request_get($url)
{
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERAGENT => "NewsHub/1.0",
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);

    curl_close($ch);

    if ($response === false) {
        return [
            "success" => false,
            "error" => "Connection error: " . $curlError
        ];
    }

    $data = json_decode($response, true);

    if (!is_array($data)) {
        return [
            "success" => false,
            "error" => "Invalid API response."
        ];
    }

    if ($httpCode !== 200 || ($data["status"] ?? "") !== "ok") {
        return [
            "success" => false,
            "error" => $data["message"] ?? "API request failed."
        ];
    }

    return [
        "success" => true,
        "data" => $data
    ];
}

// Escape HTML
function e($value)
{
    return htmlspecialchars(
        (string) ($value ?? ""),
        ENT_QUOTES,
        "UTF-8"
    );
}

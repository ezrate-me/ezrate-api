<?php
// EZRate API with PHP (no library needed).  Run:  EZRATE_KEY=ezr_live_... php rates.php

$key = getenv('EZRATE_KEY') ?: exit("Set EZRATE_KEY first.\n");

function ezrate(string $path, array $query = []): array
{
    global $key;
    $url = 'https://api.ezrate.me/v1/' . $path . ($query ? '?' . http_build_query($query) : '');
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['X-API-Key: ' . $key, 'Accept: application/json'],
    ]);
    $body = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    $json = json_decode((string) $body, true);
    if ($status !== 200) {
        throw new RuntimeException($json['error']['message'] ?? "HTTP $status");
    }
    return $json;
}

// dollar, 18k gold, Emami coin and USDT in Toman
$rates = ezrate('rates', ['symbols' => 'usd,g18,emami,usdt', 'lang' => 'fa']);
foreach ($rates['data'] as $r) {
    printf("%-20s %15s تومان  (%+.2f%%)\n", $r['name'], number_format($r['sell']), $r['change_pct']);
}

// 100 dollars in euro
$c = ezrate('convert', ['from' => 'usd', 'to' => 'eur', 'amount' => 100]);
printf("100 USD = %.2f EUR\n", $c['data']['result']);

// Tip: cache the answer for a minute (APCu, Redis or a file) instead of calling the API on every page view.

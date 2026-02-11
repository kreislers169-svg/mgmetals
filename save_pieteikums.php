<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$jsonFile = 'pieteikumi.json';

// 1. Saņemam datus
$input = file_get_contents('php://input');
$newData = json_decode($input, true);

if ($newData === null) {
    http_response_code(400); // Nosūtām kļūdas kodu pārlūkam
    die("Kļūda: Saņemtie dati nav derīgs JSON.");
}

// 2. Papildus drošība: Pievienojam saņemšanas laiku un ID, ja tāda nav
if (!isset($newData['id'])) {
    $newData['id'] = time(); // Unikāls ID pēc laika zīmoga
}

// 3. Nolasām esošos datus
$currentData = [];
if (file_exists($jsonFile)) {
    $content = file_get_contents($jsonFile);
    $currentData = json_decode($content, true) ?: [];
}

// 4. Pievienojam jauno ierakstu
$currentData[] = $newData;

// 5. Saglabājam (izmantojam LOCK_EX, lai divi cilvēki neierakstītu vienlaicīgi)
$result = file_put_contents(
    $jsonFile, 
    json_encode($currentData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), 
    LOCK_EX
);

if ($result === false) {
    http_response_code(500);
    echo "KĻŪDA: Nevar ierakstīt failā. Pārbaudi mapes atļaujas (CHMOD 777)!";
} else {
    echo "VEIKSMĪGI: Pieteikums saglabāts.";
}
?>
<?php
// manage_pieteikumi.php
$jsonFile = 'pieteikumi.json';

// Saņemam datus no JavaScript
$input = file_get_contents('php://input');
$request = json_decode($input, true);

if (!$request || !isset($request['action'])) {
    die(json_encode(['success' => false, 'error' => 'Invalid request']));
}

// Nolasām esošos pieteikumus
$data = [];
if (file_exists($jsonFile)) {
    $data = json_decode(file_get_contents($jsonFile), true) ?? [];
}

$action = $request['action'];
$id = $request['id']; // Šis ir masīva indekss

if ($action === 'delete') {
    // Dzēšam ierakstu pēc indeksa
    array_splice($data, $id, 1);
} 
elseif ($action === 'update' && isset($request['status'])) {
    // Atjaunojam statusu
    if (isset($data[$id])) {
        $data[$id]['statuss'] = $request['status'];
    }
}

// Saglabājam izmaiņas atpakaļ failā
if (file_put_contents($jsonFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Failed to save']);
}
?>
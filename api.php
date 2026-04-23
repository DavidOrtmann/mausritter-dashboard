<?php
header('Content-Type: application/json');
header('Cache-Control: no-store');

$dataFile = __DIR__ . '/data/session.json';

$defaultState = [
    'turns' => [
        'count' => 1,
        'boxes' => ['', '', '', '', '', ''],
        'timeOfDay' => 'sunrise',
    ],
    'players' => [],
    'encounter' => [],
];

function loadState(string $file, array $default): array {
    if (!file_exists($file)) {
        return $default;
    }
    $raw = file_get_contents($file);
    $data = json_decode($raw, true);
    return is_array($data) ? $data : $default;
}

function saveState(string $file, array $state): bool {
    $dir = dirname($file);
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $fp = fopen($file, 'c+');
    if (!$fp) return false;
    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        return false;
    }
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);
    return true;
}

$action = $_GET['action'] ?? $_POST['action'] ?? 'load';

switch ($action) {
    case 'load':
        echo json_encode(loadState($dataFile, $defaultState));
        break;

    case 'save':
        $body = file_get_contents('php://input');
        $state = json_decode($body, true);
        if (!is_array($state)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid JSON']);
            break;
        }
        if (saveState($dataFile, $state)) {
            echo json_encode(['ok' => true]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Write failed']);
        }
        break;

    case 'reset':
        if (saveState($dataFile, $defaultState)) {
            echo json_encode($defaultState);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Write failed']);
        }
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
}

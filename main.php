<?php

require_once 'Shift.php';
require_once 'WeeklyReport.php';

$file_path = 'dataset.json';

$json_data = file_get_contents($file_path);

if ($json_data === false) {
    die("Error reading JSON file: $file_path");
}

try {
    $shifts = json_decode($json_data, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Error decoding JSON data: ' . json_last_error_msg());
    }

    $weekStart = '2021-08-23';
    $weekEnd = '2021-08-31';

    $reportGenerator = new WeeklyReport($shifts, $weekStart, $weekEnd);
    $weeklyReport = $reportGenerator->generateReport();

    echo json_encode($weeklyReport, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}

?>

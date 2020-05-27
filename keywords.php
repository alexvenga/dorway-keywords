<?php

include 'vendor/autoload.php';

use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

echo PHP_EOL . 'Load file sites.xlsx' . PHP_EOL;
$urls = [];
$reader = ReaderEntityFactory::createReaderFromFile('sites.xlsx');
$reader->open('sites.xlsx');
$i = 0;
foreach ($reader->getSheetIterator() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
        $i++;
        if ($i % 10000) {
            echo $i . ' ';
        }
        $row = $row->toArray();
        foreach ($row as &$item) {
            $item = trim($item);
            $item = str_replace("\n", ' ', $item);
            $item = str_replace('  ', ' ', $item);
        }
        if ((!isset($row[0])) || empty($row[0])) {
            continue;
        }
        if (!isset($row[1])) {
            $row[1] = '';
        }
        if (!isset($row[2])) {
            $row[2] = '';
        }
        if (!isset($row[3])) {
            $row[3] = '';
        }
        if (!isset($urls[$row[0]])) {
            $urls[$row[0]] = implode('|', $row);
        }
    }
}
$reader->close();
echo PHP_EOL . 'LOADED ' . $i . ' urls' . PHP_EOL . PHP_EOL;

echo 'Load file main.xlsx' . PHP_EOL;
$reader = ReaderEntityFactory::createReaderFromFile('main.xlsx');
$reader->open('main.xlsx');
$keywords = [];
$i = 0;
foreach ($reader->getSheetIterator() as $sheet) {
    foreach ($sheet->getRowIterator() as $row) {
        $i++;
        if ($i % 10000) {
            echo $i . ' ';
        }
        $row = $row->toArray();
        foreach ($row as &$item) {
            $item = trim($item);
        }
        if ((!isset($row[0])) || empty($row[0]) || (!isset($row[1])) || empty($row[1])) {
            continue;
        }
        if (!isset($urls[$row[1]])) {
            continue;
        }
        if (!isset($keywords[$row[0]])) {
            $keywords[$row[0]] = [];
        }
        $keywords[$row[0]][] = $urls[$row[1]];
    }
}
$reader->close();
echo PHP_EOL . 'LOADED ' . $i . ' keywords' . PHP_EOL . PHP_EOL;

echo 'Prepare data' . PHP_EOL;
$i = 0;
foreach ($keywords as $key => $keyword) {
    $keywords[$key] = $key . '|' . implode('|', $keywords[$key]);
    $i++;
    if ($i % 10000) {
        echo $i . ' ';
    }
}
echo PHP_EOL . 'PREPARED ' . $i . ' keywords' . PHP_EOL . PHP_EOL;

echo 'Save to file output.txt' . PHP_EOL;
$i = 0;
$fh = fopen('output.txt', 'w');
foreach ($keywords as $value) {
    fwrite($fh, $value . PHP_EOL);
    $i++;
    if ($i % 10000) {
        echo $i . ' ';
    }
}
fclose($fh);
echo PHP_EOL . 'Saved ' . $i . ' lines' . PHP_EOL . PHP_EOL;

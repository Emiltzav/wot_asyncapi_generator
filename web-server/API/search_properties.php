<?php
require '../../docker/vendor/autoload.php';  // for Composer

//$host = getenv('MONGO_HOST') ?: 'mongodb';
$host = 'host.docker.internal';
$port = getenv('MONGO_PORT') ?: 27017;

// Corrected line: using $client instead of $mongoClient
$client = new MongoDB\Client("mongodb://$host:$port");

$db = $client->selectDatabase('web_of_things');  // Use $client here
$collection = $db->selectCollection('TDs');

// example query
$result = $collection->find(["info.description" => ['$regex' => '(?=.*temperature)(?=.*humidity)', '$options' => 'i']]);

foreach ($result as $document) {
    // pretty print each document
    echo json_encode($document, JSON_PRETTY_PRINT) . "\n";
}
?>
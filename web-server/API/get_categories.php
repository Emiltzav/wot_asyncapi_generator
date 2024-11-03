<?php 
// database connection settings
$host = 'db';
$dbname = 'web_of_things';
$username = 'wot_user';
$password = 'web_of_things_mysql_db@';

try {
    // Establish a database connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query to select the 'category' column
    $query = "SELECT DISTINCT category FROM `thing_description`";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Fetch all category values into an array
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Encode the array as a JSON object and output it
    echo json_encode($categories);
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
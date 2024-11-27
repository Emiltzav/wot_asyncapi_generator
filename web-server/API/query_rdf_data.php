<?php
require '../../docker/vendor/autoload.php';  // for Composer

use EasyRdf\Sparql\Client;

$fusekiUrl = getenv('FUSEKI_URL') . '/sensors/sparql';
$sparql = new Client($fusekiUrl);

$query = "
    SELECT ?s ?p ?o
    WHERE {
      ?s ?p ?o
    }
    LIMIT 100
";

try {
    $result = $sparql->query($query);
    
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>Subject</th><th>Predicate</th><th>Object</th></tr>";
    
    foreach ($result as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row->s) . "</td>";
        echo "<td>" . htmlspecialchars($row->p) . "</td>";
        echo "<td>" . htmlspecialchars($row->o) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
<?php
require("../connection.php");

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['query'])) {
    $query = $_GET['query'];
    
    $sqlGetAll = "SELECT id, name, 'products' as sourceTable FROM `products` WHERE name LIKE '%$query%' 
                  UNION 
                  SELECT id, name, 'brands' as sourceTable FROM `brands` WHERE name LIKE '%$query%' 
                  UNION 
                  SELECT id, name, 'ingredients' as sourceTable FROM `ingredients` WHERE name LIKE '%$query%'"; 
                        
    $searchResult = mysqli_query($connection, $sqlGetAll);

    $results = mysqli_fetch_all($searchResult, MYSQLI_ASSOC);
    
    echo json_encode($results);
}
?>

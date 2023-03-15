<?php
    require("../connection.php");

    // Check if the request is using the GET method
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sqlQuery = "SELECT * FROM price_categories";
        $result = mysqli_query($connection, $sqlQuery);
    
        $priceCategories = [];
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $priceCategories[$i]['id'] = $row['id'];
            $priceCategories[$i]['name'] = $row['name'];
            $i++;
        }
        echo json_encode($priceCategories);
   }
?>
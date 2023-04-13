<?php
    require("../connection.php");

    // Check if the request is using the GET method
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sqlQuery = "SELECT * FROM ingredient_functions";
        $result = mysqli_query($connection, $sqlQuery);
        $ingredientFunctions = mysqli_fetch_all($result, MYSQLI_ASSOC);
        echo json_encode($ingredientFunctions);
   }
?>
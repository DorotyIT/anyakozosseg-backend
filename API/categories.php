<?php
    require("../connection.php");

    // Check if the request is using the GET method
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sqlQuery = "SELECT * FROM categories";
        $result = mysqli_query($connection, $sqlQuery);
    
        $categories = [];
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $categories[$i]['id'] = $row['id'];
            $categories[$i]['name'] = $row['name'];
            $categories[$i]['imagePath'] = $row['image_path'];
            $i++;
        }
        echo json_encode($categories);
   }
?>
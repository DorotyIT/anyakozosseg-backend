<?php
    require("../connection.php");

     if (isset($_SERVER['REQUEST_METHOD']))
     {
       switch ($_SERVER['REQUEST_METHOD'])
       {
            case 'GET' : 
                {
                    $sqlGetProductCategories = "SELECT * FROM `product_categories`";         
                    $result= mysqli_query($connection, $sqlGetProductCategories);
                    
                    if (mysqli_num_rows($result) > 0) {
                        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
                        echo json_encode($rows);
                    } else {
                        echo "0 results";
                    }
                }
                break;
        }
    }
?>
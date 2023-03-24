<?php
    require("../connection.php");

     if (isset($_SERVER['REQUEST_METHOD']))
     {
       switch ($_SERVER['REQUEST_METHOD'])
       {
            case 'POST' : {
                $requestBody = json_decode(file_get_contents('php://input'), true);
            
                $userId = $requestBody['userId'];
                $productId = $requestBody['productId'];
                $rating = (int)$requestBody['rating'];
                $comment = $requestBody['comment'];
                
                $insertIntoRatings = "INSERT 
                                      INTO `ratings` (user_id, product_id, rating, comment)
                                      VALUES('{$userId}', '{$productId}', '{$rating}', '{$comment}')";
                mysqli_query($connection, $insertIntoRatings);

                $ratingId = mysqli_insert_id($connection);
                $response['ratingId'] = $ratingId;

                echo json_encode($response);
                
                break;
           }
        }
    }
?>
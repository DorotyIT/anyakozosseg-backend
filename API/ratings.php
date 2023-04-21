<?php
    require("../connection.php");
    require("auth.php");

    if (isset($_SERVER['REQUEST_METHOD']))
    {
        switch ($_SERVER['REQUEST_METHOD'])
        {
            case 'POST':  
                doIfLoggedIn(function() use ($connection) {
                    $requestBody = json_decode(file_get_contents('php://input'), true);

                    $userId = $_SESSION['userId'];
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
                });
                break;

            case 'PUT':
                doIfLoggedIn(function() use ($connection) {
                    $requestBody = json_decode(file_get_contents('php://input'), true);
                    
                    $userId = $_SESSION['userId'];
                    $rating = (int)$requestBody['rating'];
                    $comment = $requestBody['comment'];
                    
                    $updateRating = "UPDATE `ratings` 
                                     SET rating = '{$rating}', comment = '{$comment}'
                                     WHERE user_id = '{$userId}'";
                    mysqli_query($connection, $updateRating);

                    $response['message'] = "Rating updated successfully";

                    echo json_encode($response);
                });
                break;
        }
    }
?>

<?php
    require("../connection.php");

     $request_vars = array();

     if (isset($_SERVER['REQUEST_METHOD']))
     {
       switch ($_SERVER['REQUEST_METHOD'])
       {
            case 'GET' : 
                {
                    if (isset($_GET['categoryId']) || isset($_GET['abcLetter'])) {

                        // Fetch products by categoryId     
                        if (isset($_GET['categoryId'])) {

                            $categoryId = $_GET['categoryId'];

                            $sqlGetProducts = "SELECT * FROM `products` JOIN categories_to_products ON products.id=categories_to_products.product_id WHERE category_id={$categoryId}";
                        } 
                        // Fetch products by first letter  
                        else if (isset($_GET['abcLetter'])) {
                            $abcLetter = $_GET['abcLetter'];
                            $sqlGetProducts = "SELECT * FROM `products` WHERE products.name LIKE '{$abcLetter}%'";
                        }

                        $productsResult = mysqli_query($connection, $sqlGetProducts);
                        $products = [];

                        $i = 0;
                        while ($row = mysqli_fetch_assoc($productsResult)) {
                            $productId = $row['id'];
                            $products[$i]['id'] = $productId;
                            $products[$i]['name'] = $row['name'];
                            $products[$i]['imageFile'] =  isset($row['image_file']) ? $row['image_file'] : '';
                            
                            $sqlAvgOfRatings = "SELECT AVG(rating) FROM `ratings` WHERE product_id={$productId}";
                            $avgRatingsResult = mysqli_query($connection, $sqlAvgOfRatings);
                            $products[$i]['avgRating'] = mysqli_fetch_assoc($avgRatingsResult)['AVG(rating)'];

                            $sqlNumberOfRatings = "SELECT COUNT(*) FROM `ratings` WHERE product_id={$productId}";
                            $numberOfRatingsResult = mysqli_query($connection,  $sqlNumberOfRatings);
                            $products[$i]['numberOfRatings'] = mysqli_fetch_assoc($numberOfRatingsResult)['COUNT(*)'];

                            $sqlLastRating = "SELECT * FROM `ratings` JOIN users ON ratings.user_id = users.id  WHERE product_id={$productId} ORDER BY added_on DESC LIMIT 1";
                            $lastRatingResult = mysqli_query($connection, $sqlLastRating);
                    
                            $lastRating =  mysqli_fetch_assoc($lastRatingResult);
                            if($lastRating !== NULL) {
                                $products[$i]['lastRating']['id'] = $lastRating['id'];
                                $products[$i]['lastRating']['username'] = $lastRating['username'];
                                $products[$i]['lastRating']['rating'] = $lastRating['rating'];
                                $products[$i]['lastRating']['comment'] = $lastRating['comment'];
                                $products[$i]['lastRating']['addedOn'] = $lastRating['added_on'];
                            }

                            $i++;
                        } 

                        echo json_encode($products);
                    }

                    if (isset($_GET['productId'])) {}
                }
                break;
        }
    }
?>
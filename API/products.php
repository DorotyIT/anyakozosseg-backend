<?php
    require("../connection.php");

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

                            $sqlGetProducts = "SELECT * 
                                               FROM `products` 
                                               JOIN categories_to_products ON products.id=categories_to_products.product_id 
                                               WHERE category_id={$categoryId}";
                        } 
                        // Fetch products by first letter  
                        elseif (isset($_GET['abcLetter'])) {
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
                            
                            $sqlAvgOfRatings = "SELECT AVG(rating) 
                                                FROM `ratings` 
                                                WHERE product_id={$productId}";
                            $avgRatingsResult = mysqli_query($connection, $sqlAvgOfRatings);
                            $products[$i]['avgRating'] = mysqli_fetch_assoc($avgRatingsResult)['AVG(rating)'];

                            $sqlNumberOfRatings = "SELECT COUNT(*) 
                                                   FROM `ratings` 
                                                   WHERE product_id={$productId}";
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

                    if (isset($_GET['productId'])) {
                        $productId = $_GET['productId'];
                        $sqlGetProduct = "SELECT * 
                                          FROM `products` 
                                          WHERE id={$productId}";

                        $productResult = mysqli_query($connection, $sqlGetProduct);
                        $rawProduct = mysqli_fetch_assoc($productResult);

                        if($rawProduct != NULL) {
                            $product['id'] = $rawProduct['id'];
                            $product['name'] = $rawProduct['name'];
                            $product['imageFile'] =  isset($rawProduct['image_file']) ? $rawProduct['image_file'] : '';
                            $product['priceRangeMin'] = $rawProduct['price_range_min'];
                            $product['priceRangeMax'] = $rawProduct['price_range_max'];
                            $product['canHelp'] = $rawProduct['can_help'];
                            $product['packaging'] = $rawProduct['packaging'];
                        } else {
                            echo json_encode("No product with id: {$productId} was found.");
                            return;
                        }

                        $sqlNumberOfRatings = "SELECT COUNT(*) 
                                               FROM `ratings` 
                                               WHERE product_id={$productId}";
                        $numberOfRatingsResult = mysqli_query($connection,  $sqlNumberOfRatings);
                        $product['numberOfRatings'] = mysqli_fetch_assoc($numberOfRatingsResult)['COUNT(*)'];

                        $sqlAvgOfRatings = "SELECT AVG(rating) FROM `ratings` WHERE product_id={$productId}";
                        $avgRatingsResult = mysqli_query($connection, $sqlAvgOfRatings);
                        $product['avgRating'] = mysqli_fetch_assoc($avgRatingsResult)['AVG(rating)'];

                        $sqlProductCategories = "SELECT product_categories.product_category_name 
                                                 FROM `products` 
                                                 JOIN products_to_product_categories ON products.id = products_to_product_categories.product_id 
                                                 JOIN product_categories ON product_categories.id = products_to_product_categories.product_category_id  
                                                 WHERE product_id={$productId}";
                        $productCategoriesResult = mysqli_query($connection,  $sqlProductCategories);
                        
                        $product['productCategories'] = [];
                        $c = 0;
                        while($productCategory = mysqli_fetch_assoc($productCategoriesResult)) {
                            $product['productCategories'][$c] = $productCategory['product_category_name'];
                            $c++;
                        }

                        $sqlRatings = "SELECT *, ratings.id as rating_id 
                                       FROM `ratings` 
                                       JOIN users ON ratings.user_id = users.id  
                                       WHERE product_id={$productId} 
                                       ORDER BY added_on DESC";
         
                        $ratingsResult = mysqli_query($connection, $sqlRatings);

                        $product['ratings'] = [];
                        $i = 0;
                        while($rating = mysqli_fetch_assoc($ratingsResult)) {
                            if($rating != NULL) {
                                $product['ratings'][$i]['id'] = $rating['rating_id'];
                                $product['ratings'][$i]['username'] = $rating['username'];
                                $product['ratings'][$i]['rating'] = $rating['rating'];
                                $product['ratings'][$i]['comment'] = $rating['comment'];
                                $product['ratings'][$i]['addedOn'] = $rating['added_on'];
                            }
                            $i++;
                        }

                        echo json_encode($product);
                    }
                }
                break;
        }
    }
?>
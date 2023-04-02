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

                            $sqlLastRating = "SELECT * 
                                              FROM `ratings` 
                                              JOIN users ON ratings.user_id = users.id  
                                              WHERE product_id={$productId} 
                                              ORDER BY added_on 
                                              DESC 
                                              LIMIT 1";
                            $lastRatingResult = mysqli_query($connection, $sqlLastRating);
                    
                            $lastRating =  mysqli_fetch_assoc($lastRatingResult);
                            $products[$i]['lastRating'] = null;
                            
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
                    } elseif (isset($_GET['productId'])) {
                        $productId = $_GET['productId'];
                        $sqlGetProduct = "SELECT *
                                          FROM `products`
                                          WHERE products.id={$productId}";

                        $productResult = mysqli_query($connection, $sqlGetProduct);
                        $rawProduct = mysqli_fetch_assoc($productResult);

                        if($rawProduct != NULL) {
                            $product['id'] = $productId;
                            $product['name'] = $rawProduct['name'];
                            $product['imageFile'] =  isset($rawProduct['image_file']) ? $rawProduct['image_file'] : '';
                            $product['priceRange']['min'] = $rawProduct['price_range_min'];
                            $product['priceRange']['max'] = $rawProduct['price_range_max'];
                            $product['canHelp'] = $rawProduct['can_help'];
                            $product['packaging'] = $rawProduct['packaging'];
                        
                            // Get brand name
                            $brandId = $rawProduct['brand_id'];
                            $sqlGetBrandName = "SELECT name FROM `brands` WHERE id={$brandId}";
                            $brandResult = mysqli_query($connection, $sqlGetBrandName);
                            $brand = mysqli_fetch_assoc($brandResult);
                            $product['brandName'] = $brand['name'];
                            
                            // Get category name
                            $sqlGetCategoryName = "SELECT categories.name AS category_name
                                                   FROM categories_to_products 
                                                   JOIN categories ON categories.id = categories_to_products.category_id
                                                   WHERE categories_to_products.product_id={$productId}";
                            $categoryResult = mysqli_query($connection, $sqlGetCategoryName);
                            $category = mysqli_fetch_assoc($categoryResult);
                            $product['categoryName'] = $category['category_name'];
                            
                            // Fetch the number of ratings
                            $sqlNumberOfRatings = "SELECT COUNT(*) 
                                                   FROM `ratings` 
                                                   WHERE product_id={$productId}";
                            $numberOfRatingsResult = mysqli_query($connection, $sqlNumberOfRatings);
                            $product['numberOfRatings'] = mysqli_fetch_assoc($numberOfRatingsResult)['COUNT(*)'];
                            
                            // Fetch the average rating
                            $sqlAvgOfRatings = "SELECT AVG(rating) FROM `ratings` WHERE product_id={$productId}";
                            $avgRatingsResult = mysqli_query($connection, $sqlAvgOfRatings);
                            $product['avgRating'] = mysqli_fetch_assoc($avgRatingsResult)['AVG(rating)'];
                            
                            // Fetch subcategories
                            $sqlSubcategories = "SELECT subcategories.name 
                                                 FROM `products` 
                                                 JOIN products_to_subcategories ON products_to_subcategories.product_id=products.id 
                                                 JOIN subcategories ON subcategories.id=products_to_subcategories.subcategory_id
                                                 WHERE product_id={$productId}";
                            $subcategoriesResult = mysqli_query($connection, $sqlSubcategories);
                            
                            $product['subcategories'] = [];
                            $c = 0;
                            while($productCategory = mysqli_fetch_assoc($subcategoriesResult)) {
                                $product['subcategories'][$c] = $productCategory['name'];
                                $c++;
                            }
                            // Fetch ratings
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
                        }  else {
                            echo json_encode("No product with id: {$productId} was found.");
                        } 
                    }
                }
                break;

                case 'POST':
                    {
                        $body = json_decode(file_get_contents('php://input'), true);
                    
                        $name = $body['name'];
                        $categoryId = $body['categoryId'];
                        $brandId = $body['brandId'];
                        $imageFile = isset($body['imageFile']) ? $body['imageFile'] : '';
                        $priceRangeMin = $body['priceRange']['min'];
                        $priceRangeMax = $body['priceRange']['max'];
                        $canHelp = $body['canHelp'];
                        $packaging = $body['packaging'];
                        $subcategories = $body['subcategories'];
                    
                        $sqlAddNewProduct = "INSERT INTO `products` (name, brand_id, image_file, price_range_min, price_range_max, can_help, packaging)
                                             VALUES ('{$name}', '{$brandId}', '{$imageFile}', '{$priceRangeMin}', '{$priceRangeMax}', '{$canHelp}', '{$packaging}')";
                    
                        mysqli_query($connection, $sqlAddNewProduct);
                    
                        $productId =  mysqli_insert_id($connection);

                        $sqlCategoriesToProductInsert = "INSERT INTO `categories_to_products` (category_id, product_id)
                                                         VALUES ('{$categoryId}', '{$productId}')";
                    
                        mysqli_query($connection, $sqlCategoriesToProductInsert);
                    
                        foreach ($subcategories as $subcategory) {
                            $subcategoryId = $subcategory['id'];
                            $subcategoryName = $subcategory['name'];

                            $sqlSubcategoriesToProductInsert = "INSERT INTO `products_to_subcategories` (`subcategory_id`, `product_id`) 
                                                                VALUES ('{$subcategoryId}', '{$productId}')";
                            mysqli_query($connection, $sqlSubcategoriesToProductInsert);
                        }
                    
                        $response['productId'] = $productId;
                        echo json_encode($response);
                    }
                break;

                case 'PUT':
                    {
                        $body = json_decode(file_get_contents('php://input'), true);
                    
                        $productId = $body['id'];
                        $name = $body['name'];
                        $categoryId = $body['categoryId'];
                        $brandId = $body['brandId'];
                        $imageFile = $body['imageFile'];
                        $priceRangeMin = $body['priceRange']['min'];
                        $priceRangeMax = $body['priceRange']['max'];
                        $canHelp = $body['canHelp'];
                        $packaging = $body['packaging'];
                        $subcategories = $body['subcategories'];
                    
                        $sqlUpdateProduct = "UPDATE `products`
                                             SET name='{$name}', brand_id='{$brandId}', image_file='{$imageFile}',
                                                 price_range_min='{$priceRangeMin}', price_range_max='{$priceRangeMax}',
                                                 can_help='{$canHelp}', packaging='{$packaging}'
                                             WHERE id='{$productId}'";
                    
                        mysqli_query($connection, $sqlUpdateProduct);
                    
                        $sqlCategoriesToProductDelete = "DELETE FROM `categories_to_products`
                                                         WHERE product_id='{$productId}'";
                        mysqli_query($connection, $sqlCategoriesToProductDelete);
                    
                        $sqlCategoriesToProductInsert = "INSERT INTO `categories_to_products` (category_id, product_id)
                                                         VALUES ('{$categoryId}', '{$productId}')";
                        mysqli_query($connection, $sqlCategoriesToProductInsert);
                    
                        $sqlDeleteSubcategories = "DELETE FROM `products_to_subcategories`
                                                   WHERE product_id='{$productId}'";
                        mysqli_query($connection, $sqlDeleteSubcategories);
                    
                        foreach ($subcategories as $subcategory) {
                            $subcategoryId = $subcategory['id'];
                            $subcategoryName = $subcategory['name'];

                            $sqlSubcategoriesToProductInsert = "INSERT INTO `products_to_subcategories` (`subcategory_id`, `product_id`) 
                                                                VALUES ('{$subcategoryId}', '{$productId}')";
                            mysqli_query($connection, $sqlSubcategoriesToProductInsert);
                        }
                    
                        $response['productId'] = $productId;
                        $response['message'] = "Product updated successfully.";
                        echo json_encode($response);
                    }
                    break;

               case 'DELETE':
                    $response = [];
                    if (isset($_GET['productId'])) {
                        $productId = $_GET['productId'];
                        $sqlDeleteProduct = "DELETE FROM `products` WHERE id={$productId}";
                        $deleteResult = mysqli_query($connection, $sqlDeleteProduct);
                    
                        if ($deleteResult) {
                            $response['success'] = true;
                        } else {
                            $response['success'] = false;
                            $response['message'] = 'Failed to delete product';
                        }
                    } else {
                        $response['success'] = false;
                        $response['message'] = 'productId parameter is missing';
                    }
                    echo json_encode($response);
                    break;
        }
    }
?>
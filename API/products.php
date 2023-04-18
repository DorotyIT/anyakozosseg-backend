<?php
    require("../connection.php");
    require("auth.php");

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
                        
                            // Get brand
                            $brandId = $rawProduct['brand_id'];
                            $sqlGetBrandName = "SELECT id, name FROM `brands` WHERE id={$brandId}";
                            $brandResult = mysqli_query($connection, $sqlGetBrandName);
                            $product['brand'] = mysqli_fetch_assoc($brandResult);
                            
                            // Get category
                            $sqlGetCategory = "SELECT categories.name AS category_name, categories.id AS category_id
                                                   FROM categories_to_products 
                                                   JOIN categories ON categories.id = categories_to_products.category_id
                                                   WHERE categories_to_products.product_id={$productId}";
                            $categoryResult = mysqli_query($connection, $sqlGetCategory);
                            $category = mysqli_fetch_assoc($categoryResult);
                            $product['category'] = array('id' => $category['category_id'], 'name' => $category['category_name']);
                            
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
                            $sqlSubcategories = "SELECT subcategories.id, subcategories.name 
                                                 FROM `products` 
                                                 JOIN products_to_subcategories ON products_to_subcategories.product_id=products.id 
                                                 JOIN subcategories ON subcategories.id=products_to_subcategories.subcategory_id
                                                 WHERE product_id={$productId}";
                            $subcategoriesResult = mysqli_query($connection, $sqlSubcategories);       
                            $product['subcategories'] = mysqli_fetch_all($subcategoriesResult, MYSQLI_ASSOC);

                            // Fetch ingredients
                            $sqlIngredients = "SELECT ingredients.id, ingredients.name 
                                                 FROM `products` 
                                                 JOIN products_to_ingredients ON products_to_ingredients.product_id=products.id 
                                                 JOIN ingredients ON ingredients.id=products_to_ingredients.ingredient_id
                                                 WHERE product_id={$productId}";
                            $ingredientsResult = mysqli_query($connection, $sqlIngredients);       
                            $product['ingredients'] = mysqli_fetch_all($ingredientsResult, MYSQLI_ASSOC);

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
                        } else {
                            echo json_encode("No product with id: {$productId} was found.");
                        } 
                    }
                }
                break;

            case 'POST':
                doIfHasAdminRole(function() use ($connection) {
                    $body = json_decode(file_get_contents('php://input'), true);
                
                    $name = $body['name'];
                    $brandId = $body['brand']['id'];
                    $imageFile = isset($body['imageFile']) ? $body['imageFile'] : '';
                    $priceRangeMin = $body['priceRange']['min'];
                    $priceRangeMax = $body['priceRange']['max'];
                    $canHelp = $body['canHelp'];
                    $packaging = $body['packaging'];
                    $sqlAddNewProduct = "INSERT INTO `products` (name, brand_id, image_file, price_range_min, price_range_max, can_help, packaging)
                                         VALUES ('{$name}', '{$brandId}', '{$imageFile}', '{$priceRangeMin}', '{$priceRangeMax}', '{$canHelp}', '{$packaging}')";
                
                    mysqli_query($connection, $sqlAddNewProduct);
                
                    $productId =  mysqli_insert_id($connection);
                    
                    // Insert category
                    $categoryId = $body['category']['id'];
                    $sqlCategoriesToProductInsert = "INSERT INTO `categories_to_products` (category_id, product_id)
                                                     VALUES ('{$categoryId}', '{$productId}')";
                
                    mysqli_query($connection, $sqlCategoriesToProductInsert);
                
                    // Insert subcategories
                    $subcategories = $body['subcategories'];
                    foreach ($subcategories as $subcategory) {
                        $subcategoryId = $subcategory['id'];
                        $sqlProductsToSubcategoriesInsert = "INSERT INTO `products_to_subcategories` (`subcategory_id`, `product_id`) 
                                                            VALUES ('{$subcategoryId}', '{$productId}')";
                        mysqli_query($connection, $sqlProductsToSubcategoriesInsert);
                    }  
                    // Insert ingredients
                    $ingredients = $body['ingredients'];
                    foreach ($ingredients as $ingredient) {
                        $ingredientId = $ingredient['id'];
                        $sqlSProductsToIngredientsInsert = "INSERT INTO `products_to_ingredients` (`ingredient_id`, `product_id`) 
                                                            VALUES ('{$ingredientId}', '{$productId}')";
                        mysqli_query($connection, $sqlSProductsToIngredientsInsert);
                    }  
                    $response['productId'] = $productId;
                    echo json_encode($response);
                });
                break;

            case 'PUT':
                doIfHasAdminRole(function() use ($connection) {
                    $body = json_decode(file_get_contents('php://input'), true);
                
                    $productId = $body['id'];
                    $name = $body['name'];
                    $categoryId = $body['category']['id'];
                    $brandId = $body['brand']['id'];
                    $imageFile = $body['imageFile'];
                    $priceRangeMin = $body['priceRange']['min'];
                    $priceRangeMax = $body['priceRange']['max'];
                    $canHelp = $body['canHelp'];
                    $packaging = $body['packaging'];
                
                    $sqlUpdateProduct = "UPDATE `products`
                                         SET name='{$name}', brand_id='{$brandId}', image_file='{$imageFile}',
                                             price_range_min='{$priceRangeMin}', price_range_max='{$priceRangeMax}',
                                             can_help='{$canHelp}', packaging='{$packaging}'
                                         WHERE id='{$productId}'";
                
                    // Update (DELETE, INSERT) categories
                    mysqli_query($connection, $sqlUpdateProduct);
                
                    $sqlCategoriesToProductDelete = "DELETE FROM `categories_to_products`
                                                     WHERE product_id='{$productId}'";
                    mysqli_query($connection, $sqlCategoriesToProductDelete);
                
                    $sqlCategoriesToProductInsert = "INSERT INTO `categories_to_products` (category_id, product_id)
                                                     VALUES ('{$categoryId}', '{$productId}')";
                    mysqli_query($connection, $sqlCategoriesToProductInsert);
                
                    // Update (DELETE, INSERT) subcategories
                    $sqlDeleteSubcategories = "DELETE FROM `products_to_subcategories`
                                               WHERE product_id='{$productId}'";
                    mysqli_query($connection, $sqlDeleteSubcategories);
                
                    $subcategories = $body['subcategories'];
                    foreach ($subcategories as $subcategory) {
                        $subcategoryId = $subcategory['id'];
                        $sqlProductsToSubcategoriesInsert = "INSERT INTO `products_to_subcategories` (`subcategory_id`, `product_id`) 
                                                            VALUES ('{$subcategoryId}', '{$productId}')";
                        mysqli_query($connection, $sqlProductsToSubcategoriesInsert);
                    }
                    // Update (DELETE, INSERT) ingredients
                    $sqlDeleteIngredients = "DELETE FROM `products_to_ingredients`
                    WHERE product_id='{$productId}'";
                    mysqli_query($connection, $sqlDeleteIngredients);
                    
                    $ingredients = $body['ingredients'];
                    foreach ($ingredients as $ingredient) {
                        $ingredientId = $ingredient['id'];
                        $sqlSProductsToIngredientsInsert = "INSERT INTO `products_to_ingredients` (`ingredient_id`, `product_id`) 
                                                            VALUES ('{$ingredientId}', '{$productId}')";
                        mysqli_query($connection, $sqlSProductsToIngredientsInsert);
                    }  
                
                    $response['productId'] = $productId;
                    $response['message'] = "Product updated successfully.";
                    echo json_encode($response);
                });
                break;

            case 'DELETE':
                doIfHasAdminRole(function() use ($connection) {
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
                });
                break;
        }
    }
?>
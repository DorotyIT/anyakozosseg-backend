<?php
    require("../connection.php");

    function getOptionFromBoolean(bool $isTrue): array {
        return ['id' => $isTrue ? 1 : 0, 'name' => $isTrue];
    }

     $request_vars = array();
 
     if (isset($_SERVER['REQUEST_METHOD']))
     {
       switch ($_SERVER['REQUEST_METHOD'])
       {
            case 'GET' : 
                {
                    if(!isset($_GET['ingredientId'])) {

                        // GET all ingredients for populating options
                        if (empty($_REQUEST)) {
                            $sqlQuery = "SELECT id, name 
                                         FROM `ingredients`";

                        // GET ingredients by category
                        } elseif (isset($_GET['categoryId'])) {
                            $categoryId = $_GET['categoryId'];
                            $sqlQuery = "SELECT id, name
                                         FROM `ingredients` 
                                         JOIN categories_to_ingredients ON ingredients.id=categories_to_ingredients.ingredient_id 
                                         WHERE category_id={$categoryId}";

                        // GET ingredients by first letter of ingredient-name
                        } elseif (isset($_GET['abcLetter'])) {
                            $abcLetter = $_GET['abcLetter'];
                            $sqlQuery = "SELECT id, name
                                         FROM `ingredients` 
                                         WHERE ingredients.name LIKE '{$abcLetter}%'";
                        }

                        $result = mysqli_query($connection, $sqlQuery);
                        $ingredients = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        echo json_encode($ingredients);

                    // GET ingredient by id
                    } else {
                        $ingredientId= $_GET['ingredientId'];
                        $sqlQuery = "SELECT *
                                     FROM `ingredients`
                                     WHERE ingredients.id={$ingredientId}";
                        $result = mysqli_query($connection, $sqlQuery);
            
                        $dbRecord = mysqli_fetch_assoc($result);
                        $ingredient['id'] = $dbRecord['id'];
                        $ingredient['name'] = $dbRecord['name'];
                        $ingredient['ewgRisk'] = $dbRecord['ewg_risk'];
                        $ingredient['comedogenIndex'] = $dbRecord['comedogen_index'];
                        $ingredient['irritationIndex'] = $dbRecord['irritation_index'];
                        $ingredient['imageFile'] = $dbRecord['image_file'];
                       
                        // Fetch categories
                        $sqlGetCategories = "SELECT categories.name AS category_name, categories.id AS category_id
                                             FROM categories_to_ingredients 
                                             JOIN categories ON categories.id = categories_to_ingredients.category_id
                                             WHERE categories_to_ingredients.ingredient_id={$ingredientId}";
                        $categoryResults = mysqli_query($connection, $sqlGetCategories);

                        $categories = [];
                        while($category = mysqli_fetch_assoc($categoryResults)) {
                            $categories[] = array('id' => $category['category_id'], 'name' => $category['category_name']);
                        }

                        $ingredient['categories'] = $categories;
                        
                        // Fetch ingredient-functions
                        $sqlGetIngredientFunction = "SELECT id, name FROM ingredient_functions
                                                     JOIN ingredients_to_ingredient_functions ON ingredients_to_ingredient_functions.ingredient_function_id = ingredient_functions.id
                                                     WHERE ingredients_to_ingredient_functions.ingredient_id = '{$ingredientId}'";
                        $ingredientFunctionResult = mysqli_query($connection, $sqlGetIngredientFunction);
                        $ingredient['functions'] = mysqli_fetch_all($ingredientFunctionResult, MYSQLI_ASSOC);

                        // Fetch products that contain the ingredient
                        $sqlGetIncludedInProducts = "SELECT id, name FROM products
                                                     JOIN products_to_ingredients ON products_to_ingredients.product_id = products.id
                                                     WHERE products_to_ingredients.ingredient_id = '{$ingredientId}'";
                        $includedInProductsResult = mysqli_query($connection, $sqlGetIncludedInProducts);

                        $products = [];
                        $i = 0;
                        while ($row = mysqli_fetch_assoc($includedInProductsResult)) {
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

                        $ingredient['includedInProducts'] = $products;
                        echo json_encode($ingredient);
                    }
                }
                break;
                
            case 'POST': 
                {
                    $body = json_decode(file_get_contents('php://input'), true);
                
                    $name = mysqli_real_escape_string($connection, $body['name']);
                    $ewgRisk = $body['ewgRisk'];
                    $comedogenIndex = $body['comedogenIndex'];
                    $irritationIndex = $body['irritationIndex'];
                    $imageFile = $body['imageFile'];
                    $ingredient_functions = $body['functions'];
                    
                    // Extract category ids
                    $category_ids = array();
                    foreach ($body['categories'] as $category) {
                        $category_ids[] = $category['id'];
                    }
                
                    // Insert into ingredients table
                    $sqlInsertIngredient = "INSERT INTO ingredients (name, ewg_risk, comedogen_index, irritation_index, image_file)
                                            VALUES ('{$name}', {$ewgRisk}, {$comedogenIndex}, {$irritationIndex}, '{$imageFile}')";
                    mysqli_query($connection, $sqlInsertIngredient);
                    $ingredientId = mysqli_insert_id($connection);
                
                    // Insert into categories_to_ingredients table
                    foreach ($category_ids as $category_id) {
                        $sqlInsertCategoryToIngredient = "INSERT INTO categories_to_ingredients (category_id, ingredient_id)
                                                          VALUES ({$category_id}, {$ingredientId})";
                        mysqli_query($connection, $sqlInsertCategoryToIngredient);
                    }
                
                    // Insert into ingredients_to_ingredient_functions table
                    foreach ($ingredient_functions as $function) {
                        $function_id = $function['id'];
                        $sqlInsertIngredientFunction = "INSERT INTO ingredients_to_ingredient_functions (ingredient_id, ingredient_function_id)
                                                        VALUES ({$ingredientId}, {$function_id})";
                        mysqli_query($connection, $sqlInsertIngredientFunction);
                    }
                
                    $response['ingredientId'] = $ingredientId;
                    echo json_encode($response);
                }
                break;
                    

            case 'PUT': 
                {
                    $body = json_decode(file_get_contents('php://input'), true);
                    $ingredientId = $body['id'];
                
                    $name = mysqli_real_escape_string($connection, $body['name']);
                    $ewgRisk = $body['ewgRisk'];
                    $comedogenIndex = $body['comedogenIndex'];
                    $irritationIndex = $body['irritationIndex'];
                    $imageFile = $body['imageFile'];
                    $ingredient_functions = $body['functions'];
                
                    // Update ingredients table
                    $sqlUpdateIngredient = "UPDATE ingredients 
                                            SET name='{$name}', ewg_risk={$ewgRisk}, comedogen_index={$comedogenIndex}, 
                                                irritation_index={$irritationIndex}, image_file='{$imageFile}'
                                            WHERE id={$ingredientId}";
                    mysqli_query($connection, $sqlUpdateIngredient);
                
                    // Delete old categories_to_ingredients
                    $sqlDeleteOldCategoriesToIngredients = "DELETE FROM categories_to_ingredients 
                                                             WHERE ingredient_id={$ingredientId}";
                    mysqli_query($connection, $sqlDeleteOldCategoriesToIngredients);
                
                    // Insert new categories_to_ingredients
                    foreach ($body['categories'] as $category) {
                        $category_id = $category['id'];
                        $sqlInsertCategoryToIngredient = "INSERT INTO categories_to_ingredients (category_id, ingredient_id)
                                                            VALUES ({$category_id}, {$ingredientId})";
                        mysqli_query($connection, $sqlInsertCategoryToIngredient);
                    }
                
                    // Delete old ingredient functions
                    $sqlDeleteOldIngredientFunctions = "DELETE FROM ingredients_to_ingredient_functions 
                                                        WHERE ingredient_id={$ingredientId}";
                    mysqli_query($connection, $sqlDeleteOldIngredientFunctions);
                
                    // Insert new ingredient functions
                    foreach ($ingredient_functions as $function) {
                        $function_id = $function['id'];
                        $sqlInsertIngredientFunction = "INSERT INTO ingredients_to_ingredient_functions (ingredient_id, ingredient_function_id)
                                                        VALUES ({$ingredientId}, {$function_id})";
                        mysqli_query($connection, $sqlInsertIngredientFunction);
                    }
                
                    $response['ingredientId'] = $ingredientId;
                    echo json_encode($response);
                }
                break;
                    
            case 'DELETE':
                if (isset($_GET['ingredientId'])) {
                    $ingredientId = $_GET['ingredientId'];
                    $deleteingredient = "DELETE 
                                         FROM `ingredients` 
                                         WHERE `id` = '{$ingredientId}'";

                    mysqli_query($connection, $deleteingredient);
                    $response['ingredientId'] = $_GET['ingredientId'];
                    echo json_encode($response);
                }
                break;
        }
    }
    
?>
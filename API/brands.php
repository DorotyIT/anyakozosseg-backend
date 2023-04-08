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
                    if(!isset($_GET['brandId'])) {

                        // GET all brands for populating options
                        if (empty($_REQUEST)) {
                            $sqlQuery = "SELECT id, name 
                                         FROM `brands`";

                        // GET brands by category
                        } elseif (isset($_GET['categoryId'])) {
                            $categoryId = $_GET['categoryId'];
                            $sqlQuery = "SELECT id, name
                                         FROM `brands` 
                                         JOIN categories_to_brands ON brands.id=categories_to_brands.brand_id 
                                         WHERE category_id={$categoryId}";

                        // GET brands by first letter of brand-name
                        } elseif (isset($_GET['abcLetter'])) {
                            $abcLetter = $_GET['abcLetter'];
                            $sqlQuery = "SELECT id, name
                                         FROM `brands` 
                                         WHERE brands.name LIKE '{$abcLetter}%'";
                        }

                        $result = mysqli_query($connection, $sqlQuery);
                        $brands = mysqli_fetch_all($result, MYSQLI_ASSOC);

                        echo json_encode($brands);

                    // GET brand by id
                    } else {
                        $brandId= $_GET['brandId'];
                        $sqlQuery = "SELECT *, brands.id AS brand_id, brands.name AS brand_name, price_categories.name AS price_category_name
                                     FROM `brands`
                                     JOIN price_categories ON price_categories.id = brands.price_category_id
                                     WHERE brands.id={$brandId}";
                        $result = mysqli_query($connection, $sqlQuery);
            
                        $rawBrand = mysqli_fetch_assoc($result);
                        $brand['id'] = $rawBrand['brand_id'];
                        $brand['name'] = $rawBrand['brand_name'];
                        $isCrueltyFree = (bool)$rawBrand['is_cruelty_free'];
                        $brand['isCrueltyFree'] = getOptionFromBoolean($isCrueltyFree);
                        $isVegan = (bool)$rawBrand['is_vegan'];
                        $brand['isVegan'] = getOptionFromBoolean($isVegan);
                        $brand['imageFile'] = isset($rawBrand['image_file']) ? $rawBrand['image_file'] : '';
                        $brand['priceCategory'] = array('id' => $rawBrand['price_category_id'], 'name' => $rawBrand['price_category_name']);

                        // Fetch category
                        $sqlGetCategory = "SELECT categories.name AS category_name, categories.id AS category_id
                                           FROM categories_to_brands 
                                           JOIN categories ON categories.id = categories_to_brands.category_id
                                           WHERE categories_to_brands.brand_id={$brandId}";
                        $categoryResult = mysqli_query($connection, $sqlGetCategory);
                        $category = mysqli_fetch_assoc($categoryResult);
                        $brand['category'] = array('id' => $category['category_id'], 'name' => $category['category_name']);
                        
                        
  
               

                        
                        // Fetch the overall rating
                        $sqlAvgOfRatings = "SELECT AVG(rating) 
                                            FROM `ratings` 
                                            JOIN products ON ratings.product_id=products.id 
                                            JOIN brands ON products.brand_id=brands.id 
                                            WHERE brands.id={$brandId}";
                        $avgRatingsResult = mysqli_query($connection, $sqlAvgOfRatings);
                        $brand['overallRating'] = mysqli_fetch_assoc($avgRatingsResult)['AVG(rating)'];

                         // Fetch the number of ratings for the brand
                        $sqlNumOfRatings = "SELECT COUNT(ratings.id)
                                            FROM `ratings` 
                                            JOIN products ON ratings.product_id=products.id 
                                            JOIN brands ON products.brand_id=brands.id 
                                            WHERE brands.id={$brandId}";
                        $sqlNumOfRatings = mysqli_query($connection, $sqlNumOfRatings);
                        $brand['numberOfRatings'] = mysqli_fetch_assoc($sqlNumOfRatings)['COUNT(ratings.id)'];
                        
                        echo json_encode($brand);
                    }
                }
                break;
                
            case 'POST': 
                {
                    $body = json_decode(file_get_contents('php://input'), true);
                    
                    $name = $body['name'];
                    $isCrueltyFree = $body['isCrueltyFree']['id'];
                    $isVegan = $body['isVegan']['id'];
                    $overallRating = $body['overallRating'];
                    $imageFile = isset($body['imageFile']) ? $body['imageFile'] : '';
                    $categoryId = $body['category']['id'];
                    $priceCategoryId = $body['priceCategory']['id'];
                    
                    $insertIntoBrands = "INSERT 
                                         INTO `brands`(name, is_cruelty_free, is_vegan, overall_rating, price_category_id, image_file)
                                         VALUES('{$name}', '{$isCrueltyFree}', '{$isVegan}', '{$overallRating}', '{$priceCategoryId}', '{$imageFile}');";
                    mysqli_query($connection, $insertIntoBrands);
                    
                    $brandId = mysqli_insert_id($connection);
                    
                    $insertIntoCategoriesToBrands = "INSERT 
                                                     INTO `categories_to_brands` (brand_id, category_id) 
                                                     VALUES('{$brandId}', '{$categoryId}')";
                    
                    mysqli_query($connection, $insertIntoCategoriesToBrands);

                    $response['brandId'] = $brandId;
                    echo json_encode($response);
                }
                break;

                case 'PUT': {

                    $body = json_decode(file_get_contents('php://input'), true);
                    
                    $id = $body['id'];
                    $name = $body['name'];
                    $isCrueltyFree = $body['isCrueltyFree']['id'];
                    $isVegan = $body['isVegan']['id'];
                    $overallRating = $body['overallRating'];
                    $imageFile = isset($body['imageFile']) ? $body['imageFile'] : '';
                    $categoryId = $body['category']['id'];
                    $priceCategoryId = $body['priceCategory']['id'];

                    $updateBrands = "UPDATE `brands` 
                                     SET name = '{$name}', 
                                         is_cruelty_free = '{$isCrueltyFree}', 
                                         is_vegan = '{$isVegan}',  
                                         overall_rating = '{$overallRating}', 
                                         price_category_id = '{$priceCategoryId}', 
                                         image_file = '{$imageFile}' 
                                     WHERE id = {$id}";
                    mysqli_query($connection, $updateBrands);
        
                    $updateCategoriesToBrands = "UPDATE `categories_to_brands` 
                                                 SET category_id = '{$categoryId}' 
                                                 WHERE brand_id = {$id}";
                    
                    mysqli_query($connection, $updateCategoriesToBrands);

                    $response['brandId'] = $id;
                    
                    echo json_encode($response);
                }
                break;
   
            case 'DELETE':
                if (isset($_GET['brandId'])) {
                    $brandId = $_GET['brandId'];
                    $deleteBrand = "DELETE 
                                    FROM `brands` 
                                    WHERE `id` ='{$brandId}' LIMIT 1";

                    mysqli_query($connection, $deleteBrand);
                    $response['brandId'] = $_GET['brandId'];
                    echo json_encode($response);
                }
                break;
        }
    }
    
?>



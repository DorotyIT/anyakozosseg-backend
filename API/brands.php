<?php
    require("../connection.php");

     $request_vars = array();

     if (isset($_SERVER['REQUEST_METHOD']))
     {
       switch ($_SERVER['REQUEST_METHOD'])
       {
            case 'GET' : 
                {
                    if (isset($_GET['categoryId']) && isset($_GET['abcLetter'])) {
                        $abcLetter = $_GET['abcLetter'];
                        $categoryId = $_GET['categoryId'];
                        $sqlQuery = "SELECT * 
                                     FROM `brands` 
                                     JOIN categories_to_brands ON brands.id=categories_to_brands.brand_id 
                                     WHERE category_id={$categoryId} 
                                     AND brands.name LIKE '{$abcLetter}%'";
                        $result = mysqli_query($connection, $sqlQuery);
                    
                        $brands = [];
                        $i = 0;
                        while ($row = mysqli_fetch_assoc($result)) {
                            $brands[$i]['id'] = $row['id'];
                            $brands[$i]['name'] = $row['name'];
                            $i++;
                        }
                        echo json_encode($brands);
                    } 
                    if (isset($_GET['brandId'])) {
                        $brandId= $_GET['brandId'];
                        $sqlQuery = "SELECT * 
                                     FROM `brands` 
                                     JOIN categories_to_brands ON brands.id=categories_to_brands.brand_id 
                                     WHERE brands.id={$brandId}";
                        $result = mysqli_query($connection, $sqlQuery);
            
                        $rawBrand = mysqli_fetch_assoc($result);
                        $brand['id'] = $rawBrand['id'];
                        $brand['name'] = $rawBrand['name'];
                        $brand['priceCategoryId'] = $rawBrand['price_category_id'];
                        $brand['isCrueltyFree'] = (bool)$rawBrand['is_cruelty_free'];
                        $brand['isVegan'] = (bool)$rawBrand['is_vegan'];
                        $brand['overallRating'] = $rawBrand['overall_rating'];
                        $brand['imageFile'] = isset($rawBrand['image_file']) ? $rawBrand['image_file'] : '';
                        echo json_encode($brand);
                    }
                }
                break;
                
            case 'POST': 
                {
                    $body = json_decode(file_get_contents('php://input'), true);
                    
                    $name = $body['name'];
                    $isCrueltyFree = (int)$body['isCrueltyFree'];
                    $isVegan = (int)$body['isVegan'];
                    $overallRating = $body['overallRating'];
                    $imageFile = isset($body['imageFile']) ? $body['imageFile'] : '';
                    $categoryId = $body['categoryId'];
                    $priceCategoryId = $body['priceCategoryId'];
                    
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
                    $isCrueltyFree = (int)$body['isCrueltyFree'];
                    $isVegan = (int)$body['isVegan'];
                    $overallRating = $body['overallRating'];
                    $imageFile = isset($body['imageFile']) ? $body['imageFile'] : '';
                    $categoryId = $body['categoryId'];
                    $priceCategoryId = $body['priceCategoryId'];

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

    

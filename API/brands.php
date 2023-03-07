<?php
    require("../connection.php");

    if ($_GET['abcLetter'] && $_GET['categoryId']) {
        $abcLetter = $_GET['abcLetter'];
        $categoryId = $_GET['categoryId'];
        $sqlQuery = "SELECT * FROM `brands` JOIN categories_to_brands ON brands.id=categories_to_brands.brand_id JOIN price_categories ON brands.price_category_id=price_categories.id WHERE category_id={$categoryId} AND brands.name LIKE '{$abcLetter}%'";
        $result = mysqli_query($connection, $sqlQuery);
    
        $brands = [];
        $i = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $brands[$i]['id'] = $row['id'];
            $brands[$i]['name'] = $row['name'];
            $brands[$i]['priceCategory'] = $row['price_category_name'];
            $brands[$i]['isCrueltyFree'] = (bool)$row['is_cruelty_free'];
            $brands[$i]['isVegan'] = (bool)$row['is_vegan'];
            $brands[$i]['overallRating'] = $row['overall_rating'];
            $i++;
        }
        echo json_encode($brands);
    }
?>
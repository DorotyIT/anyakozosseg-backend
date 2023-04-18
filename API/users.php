<?php
require("../connection.php");

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_GET['login'])) {
        $body = json_decode(file_get_contents('php://input'), true);
        $username = $body['username'];
        $password = $body['password'];
        $sqlQuery = "SELECT * FROM `users` WHERE users.username='{$username}'";

        $result = mysqli_query($connection, $sqlQuery);
        if (mysqli_num_rows($result) > 0) {
            $rawUser = mysqli_fetch_assoc($result);
            if (password_verify($password, $rawUser['password'])) {
                $_SESSION['userId'] = $rawUser['id'];
                $_SESSION['username'] = $rawUser['username'];
                $_SESSION['role'] = $rawUser['role'];
                $response['username'] = $rawUser['username'];
                $response['role'] = $rawUser['role'];
                $response['id'] = $rawUser['id'];
                echo json_encode($response);
            } else {
                echo json_encode("Invalid username or password");
            }
        } else {
            echo json_encode("Invalid username or password");
        }    
    } else {
        $body = json_decode(file_get_contents('php://input'), true);
        $username = $body['username'];
        $password = $body['password'];
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $sqlSelect = "SELECT * FROM `users` WHERE users.username='{$username}'";
        $result = mysqli_query($connection, $sqlSelect);

        if (mysqli_num_rows($result) > 0) {
            echo json_encode("User already exists with this username");
        } else {
            $sqlInsert = "INSERT INTO `users` (username, password) VALUE('{$username}','{$hashedPassword}')";
            $result = mysqli_query($connection, $sqlInsert);     

            $userId =  mysqli_insert_id($connection);

            echo json_encode($userId);
        } 
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if(isset($_GET['logout'])) {
        // Delete the session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_unset();
        session_destroy();
        echo json_encode("logout success!");
    }
} else {
    http_response_code(405);
    echo json_encode("Method not allowed");
}
?>
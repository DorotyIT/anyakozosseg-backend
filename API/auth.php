<?php

function doIfLoggedIn($callback) {
    session_start();
    if (isset($_SESSION['userId'])) {
        $callback();
    } else {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: text/plain');
        echo "You are logged out or have an invalid session!";
        session_unset();
        session_destroy();
    }
}

function doIfHasAdminRole($callback) {
    session_start();
    if (isset($_SESSION['userId']) && $_SESSION['role'] === "admin") {
        $callback();
    } else {
        header('HTTP/1.1 401 Unauthorized');
        header('Content-Type: text/plain');
        echo "You dont have the required role for the action!";
        session_unset();
        session_destroy();
    }
}

?>
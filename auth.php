<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['action'] === 'login') {
        $_SESSION['data'] = isset($_POST['data']) ? $_POST['data'] : [];
        $_SESSION['routine_data'] = isset($_POST['routine_data']) ? $_POST['routine_data'] : [];
        $_SESSION['exercises'] = isset($_POST['exercises']) ? $_POST['exercises'] : [];
        $_SESSION['user_name'] = isset($_POST['user_name']) ? $_POST['user_name'] : [];
        $_SESSION['logged_in'] = true;
        
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($_POST['action'] === 'logout') {
        // Clear session data
        session_destroy();
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}


// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

// Function to redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit;
    }
} 

function isLogin(){
    if(isLoggedIn()){
        header('Location: user.php');
        exit;
    }
}
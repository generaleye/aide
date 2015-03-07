<?php
require_once 'DbHandlerForWeb.php';

function register($name,$email,$password,$phone,$service,$address,$latitude,$longitude) {
    if (isValidEmail($email)) {
        $db = new DbHandlerForWeb();
        $create = $db->createProvider($name,$email,$password,$phone,$service,$address,$latitude,$longitude);
        if ($create == REGISTRATION_SUCCESSFUL) {
            $_SESSION['authorized'] = 1;
            $_SESSION['privilege'] = "provider";
            $_SESSION['email'] = $email;
            header('Location: ./dashboard.php');
            exit();
        } elseif ($create == EMAIL_ALREADY_EXISTS) {
            $_SESSION['error'] = 'Email Already Exists';
        } else {
            $_SESSION['error'] = 'An Error Occurred, Please Try Again';
        }
    } else {
        $_SESSION['error'] = 'Email Address is Not Valid';
    }
}

function login($email,$password) {
    if (isValidEmail($email)) {
        $db = new DbHandlerForWeb();
        $login = $db->providerLogin($email,$password);
        if ($login) {
            $_SESSION['authorized'] = 1;
            $_SESSION['privilege'] = "provider";
            $_SESSION['email'] = $email;
            header('Location: ./dashboard.php');
            exit();
        } else {
            $_SESSION['error'] = 'Incorrect Credentials. Please Try Again';
        }
    } else {
        $_SESSION['error'] = 'Email Address is Not Valid';
    }
}

// Authentication
function logged_in() {
    if(isset($_SESSION['authorized'])){
        if($_SESSION['authorized'] == 1) {
            return true;
        } else {
            return false;
        }
    }

}

function isProvider() {
    if(isset($_SESSION['privilege'])){
        if($_SESSION['privilege'] == "provider") {
            return true;
        } else {
            return false;
        }
    }
}

function login_required() {
    if(logged_in()) {
        return true;
    } else {
        header('Location: ./signup.php');
        exit();
    }
}

function logout(){
    unset($_SESSION['authorized']);
    session_unset();
    session_destroy();
    header('Location: ./signup.php');
    exit();
}

// Render error messages
function messages() {
    $message = '';
    if (isset($_SESSION['success'])){
        if($_SESSION['success'] != '') {
            $message = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>'.$_SESSION['success'].'</div>';
            $_SESSION['success'] = '';
        }
    }
    if (isset($_SESSION['error'])){
        if($_SESSION['error'] != '') {
            $message = '<div class="alert alert-danger"><strong>'.$_SESSION['error'].'</strong></div>';
            $_SESSION['error'] = '';
        }
    }

    echo "$message";
}


function errors($error){
    if (!empty($error))
    {
        $i = 0;
        $showError = "";
        while ($i < count($error)){
            $showError .= "<div class=\"msg-error\">".$error[$i]."</div>";
            $i ++;}
        echo $showError;
    }// close if empty errors
} // close function

/**
 * Validating email address
 */
function isValidEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return FALSE;
    } else {
        return TRUE;
    }
}
?>
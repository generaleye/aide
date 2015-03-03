<?php
require_once 'DbHandlerForWeb.php';

function register($name,$email,$password,$phone,$service,$address,$latitude,$longitude) {
    if (isValidEmail($email)) {
        $db = new DbHandlerForWeb();
        $create = $db->createProvider($name,$email,$password,$phone,$service,$address,$latitude,$longitude);
        if ($create == REGISTRATION_SUCCESSFUL) {
            $_SESSION['authorized'] = 1;
            $_SESSION['privilege'] = "provider";
            header('Location: provider.php');
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

function login($email,$password,$type) {
    if (isValidEmail($email)) {
        $db = new DbHandlerForWeb();
        if ($type=="provider") {
            $login = $db->providerLogin($email,$password);
            if ($login) {
                $_SESSION['authorized'] = 1;
                $_SESSION['privilege'] = "provider";
                header('Location: provider.php');
                exit();
            } else {
                $_SESSION['error'] = 'Incorrect Credentials. Please Try Again';
            }
        } else {
            $_SESSION['error'] = 'WE don\'t support YOU!!';
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
        if($_SESSION['privilege'] == "admin") {
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
        header('Location: '.config::DIRADMIN.'login');
        exit();
    }
}

function logout(){
    unset($_SESSION['authorized']);
    session_unset();
    session_destroy();
    header('Location: '.config::DIRADMIN.'login');
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

// Render error messages
function feedbackMessages() {
    $message = '';
    if (isset($_GET['msg'])){
        if($_GET['msg'] == '0') {
            $message = '<div class="alert alert-success"><button type="button" class="close" data-dismiss="alert">×</button>Feedback has been Sent</div>';
            //$_SESSION['success'] = '';
        }elseif($_GET['msg'] == '1') {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Sorry, Invalid Email Address</div>';

        }elseif($_GET['msg'] == '2') {
            $message = '<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Please fill the required fields appropriately</div>';

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
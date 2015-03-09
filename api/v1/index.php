<?php
ini_set("display_errors",1);
require_once '../../include/DbHandler.php';
require_once '../../include/PassHash.php';
require '../libs/Slim/Slim.php';

\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();

// User id from db - Global Variable
$user_id = NULL;

/**
 * For methods that require Auth
 * Adding Middle Layer to authenticate every request
 * Checking if the request has valid api key as part of the parameters
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
    //$headers = apache_request_headers();
    $response = array();
    $app = \Slim\Slim::getInstance();
    $apiKey = $app->request->params('apikey');

    // Verifying Authorization Header
    if (isset($apiKey)) {
        $db = new DbHandler();
        if (!$db->isValidApiKey($apiKey)) {
            // api key is not present in users table
            $response["error"] = TRUE;
            $response["message"] = "Access Denied. Invalid Api key";
            echoRespnse(401, $response);
            $app->stop();
        } else {
            global $user_id;
            // get user primary key id
            $user_id = $db->getUserId($apiKey);
        }
    } else {
        // api key is missing in header
        $response["error"] = TRUE;
        $response["message"] = "Api key is missing";
        echoRespnse(400, $response);
        $app->stop();
    }
}


/**
* REGISTER AND LOGIN FUNCTIONS
**/
$app->post('/register', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('email', 'password'));

    $response = array();
    $req = $app->request(); // Getting parameters

    // reading post params
    $email = $req->params('email');
    $password = $req->params('password');

    // validating email address
    validateEmail($email);

    $db = new DbHandler();
    $res = $db->createUser($email, $password);

    if ($res == REGISTRATION_SUCCESSFUL) {
        $user = $db->getUserByEmail($email);

        if ($user != NULL) {
            $response["error"] = FALSE;
            $response['email'] = $user['email_address'];
            $response['api_key'] = $user['api_key'];
            $response['created_time'] = $user['created_time'];
            $response["message"] = "You have been successfully registered";
        } else {
            // unknown error occurred
            $response['error'] = TRUE;
            $response['message'] = "An error occurred. Please try again";
        }
    } else if ($res == REGISTRATION_FAILED) {
        $response["error"] = TRUE;
        $response["message"] = "Oops! An error occurred while registering. Please try again";
    } else if ($res == EMAIL_ALREADY_EXISTS) {
        $response["error"] = TRUE;
        $response["message"] = "Sorry, this email already exists";
    }
    // echo json response
    echoRespnse(200, $response);
});

$app->post('/login', function() use ($app) {
    // check for required params
    verifyRequiredParams(array('email', 'password'));

    // reading post params
    $req = $app->request();

    $email = $req->params('email');
    $password = $req->params('password');
    $response = array();

    validateEmail($email);

    $db = new DbHandler();
    // check for correct email and password
    if ($db->checkLogin($email, $password)) {
        // get the user by email
        $user = $db->getUserByEmail($email);

        if ($user != NULL) {
            $response["error"] = FALSE;
            $response['email'] = $user['email_address'];
            $response['api_key'] = $user['api_key'];
            $response['created_time'] = $user['created_time'];
            $response['message'] = "You have been successfully logged-in";
        } else {
            // unknown error occurred
            $response['error'] = TRUE;
            $response['message'] = "An error occurred. Please try again";
        }
    } else {
        // user credentials are wrong
        $response['error'] = TRUE;
        $response['message'] = 'Login failed. Incorrect credentials';
    }

    echoRespnse(200, $response);
});

$app->post('/getprofile', 'authenticate', 'getProfile');
$app->post('/editprofile', 'authenticate', 'editProfile');
$app->post('/addkins', 'authenticate', 'addKins');
$app->post('/getkins', 'authenticate', 'getKins');
$app->post('/editkins', 'authenticate', 'editKins');
$app->post('/deletekins', 'authenticate', 'deleteKins');
$app->post('/requestservice', 'authenticate', 'requestService');
$app->post('/selectprovider', 'authenticate', 'selectProvider');
$app->post('/cancelrequest', 'authenticate', 'cancelRequest');
$app->post('/getnewproviders', 'authenticate', 'getNewProviders');
$app->post('/getonenewprovider', 'authenticate', 'getOneNewProvider');
$app->post('/allrequests', 'authenticate', 'getAllRequests');
$app->post('/review', 'authenticate', 'review');
$app->post('/anonymous', 'anonymousRequest');

$app->run();

function getProfile() {
    global $app;
    $apiKey = $app->request->params('apikey');
    $db = new DBHandler();
    $userId = $db->getUserId($apiKey);
    $profile = $db->getProfileById($userId);
    $response = array();
    if ($profile != NULL) {
        $response["error"] = FALSE;
        $response['fname'] = $profile['first_name'];
        $response['lname'] = $profile['last_name'];
        $response['email'] = $profile['email_address'];
        $response['profile_pic'] = $profile['profile_picture'];
        $response['sex'] = $profile['sex'];
        $response['phone_num'] = $profile['phone_number'];
        $response['address'] = $profile['address'];
    } else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function editProfile() {
    $app = \Slim\Slim::getInstance();
    //verifyRequiredParams(array('fname', 'lname', 'mname', 'description'));

    $req = $app->request(); // Getting parameters
    $fname = $req  ->params('fname');
    $lname = $req->params('lname');
    $sex = $req->params('sex');
    $phone = $req->params('phone');
    $address = $req->params('address');

    $apiKey = $app->request->params('apikey');
    $db = new DbHandler();

    $userId = $db->getUserId($apiKey);
    $update = $db->updateProfileById($userId, $fname, $lname, $sex, $phone, $address);
    $response = array();
    if ($update == TRUE) {
        $response["error"] = FALSE;
        $response['message'] = "Your Profile has been Updated";
    } else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function addKins() {
    //global $app;
    $app = \Slim\Slim::getInstance();
    verifyRequiredParams(array('fname', 'lname', 'phone'));

    $req = $app->request(); // Getting parameters
    $fname = $req->params('fname');
    $lname = $req->params('lname');
    $phone = $req->params('phone');
    $email = $req->params('email');
    $address = $req->params('address');

    $apiKey = $app->request->params('apikey');
    $db = new DbHandler();
    $userId = $db->getUserId($apiKey);
    $kin = $db->addKins($userId, $fname, $lname, $phone, $address, $email);

    $response = array();
    if ($kin['error'] == FALSE) {
        $response = $kin;
    } else {
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function getKins() {
    global $app;
    $req = $app->request(); // Getting parameters
    $apikey = $req->params('apikey');
    $db = new DBHandler();
    $userId = $db->getUserId($apikey);
    $response = array();

    $kins = $db->getKins($userId);
    if ($kins != NULL) {
        $response["error"] = FALSE;
        $response["count"] = $kins['count'];
        $response["kins"] = $kins['kins'];
    } else {
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function editKins() {
    global $app;
    verifyRequiredParams(array('id'));
    $req = $app->request(); // Getting parameters
    $kinId = $req->params('id');
    $fname = $req->params('fname');
    $lname = $req->params('lname');
    $phone = $req->params('phone');
    $email = $req->params('email');
    $address = $req->params('address');

    $apiKey = $app->request->params('apikey');
    $db = new DbHandler();

    $userId = $db->getUserId($apiKey);
    $update = $db->updateKinsById($userId, $kinId, $fname, $lname, $phone, $email, $address);
    $response = array();
    if ($update == TRUE) {
        $response["error"] = FALSE;
        $response['message'] = "Update Successful";
    } elseif ($update == FALSE) {
        $response['error'] = TRUE;
        $response['message'] = "You cannot edit that";
    }else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function deleteKins() {
    global $app;
    verifyRequiredParams(array('id'));
    $req = $app->request(); // Getting parameters
    $kinId = $req->params('id');
    $apikey = $req->params('apikey');
    $db = new DBHandler();
    $userId = $db->getUserId($apikey);
    $response = array();
    $del = $db->deleteKinsById($kinId,$userId);
    if ($del == TRUE) {
        $response["error"] = FALSE;
        $response['message'] = "Next of Kin has been deleted";
    } elseif ($del == FALSE) {
        $response['error'] = TRUE;
        $response['message'] = "You cannot delete that";
    } else {
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }

    echoRespnse(200, $response);
}

function requestService() {
    //global $app;
    $app = \Slim\Slim::getInstance();
    verifyRequiredParams(array('type'));

    $req = $app->request(); // Getting parameters
    $latitude = $req->params('latitude');
    $longitude = $req->params('longitude');
    $address = $req->params('address');
    $type = intval($req->params('type'));
    $device_id = $req->params('device_id');

    $apikey = $app->request->params('apikey');
    $db = new DbHandler();
    $userId = $db->getUserId($apikey);
    $response = array();

    if ($type==5) {
        //SOS : Send Text messages to all next of kins
        $returnValue = $db->sendTextToKins($userId,$device_id,$longitude,$latitude,$address,$type);
    } else {
        //Other forms of request like fire=1, theft=2, medical=3 and auto=4
        $returnValue = $db->getProviders($longitude,$latitude,$address,$type);
    }

    if ($returnValue != NULL) {
        $response = $returnValue;
    } else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function selectProvider() {
    //global $app;
    $app = \Slim\Slim::getInstance();
    verifyRequiredParams(array('id','type'));

    $req = $app->request(); // Getting parameters
    $providerId = $req->params('id');
    $latitude = $req->params('latitude');
    $longitude = $req->params('longitude');
    $address = $req->params('address');
    $type = intval($req->params('type'));
    $device_id = $req->params('device_id');

    $apikey = $app->request->params('apikey');
    $db = new DbHandler();
    $userId = $db->getUserId($apikey);
    $response = array();
    $returnValue = $db->selectProvider($userId,$device_id,$providerId,$longitude,$latitude,$address,$type);

    if ($returnValue != NULL) {
        $response = $returnValue;
    } else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function cancelRequest() {
    //global $app;
    $app = \Slim\Slim::getInstance();
    verifyRequiredParams(array('request_id'));

    $req = $app->request(); // Getting parameters
    $requestId = $req->params('request_id');

    $apikey = $app->request->params('apikey');
    $db = new DbHandler();
    $userId = $db->getUserId($apikey);
    $response = array();
    $returnValue = $db->cancelRequest($userId,$requestId);

    if ($returnValue == TRUE) {
        $response['error'] = FALSE;
        $response['request_id'] = $requestId;
        $response['message'] = "Your Request has been Cancelled";
    } else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function getNewProviders() {
    //global $app;
    $app = \Slim\Slim::getInstance();
    verifyRequiredParams(array('request_id'));

    $req = $app->request(); // Getting parameters
    $requestId = $req->params('request_id');

    $apikey = $app->request->params('apikey');
    $db = new DbHandler();
    $userId = $db->getUserId($apikey);
    $response = array();
    $returnValue = $db->getNewProviders($requestId,$userId);

    if ($returnValue != NULL) {
        $response = $returnValue;
    } else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function getOneNewProvider() {
    //global $app;
    $app = \Slim\Slim::getInstance();
    verifyRequiredParams(array('request_id','provider_id'));

    $req = $app->request(); // Getting parameters
    $requestId = $req->params('request_id');
    $providerId = $req->params('provider_id');

    $apikey = $app->request->params('apikey');
    $db = new DbHandler();
    $userId = $db->getUserId($apikey);
    $response = array();
    $returnValue = $db->getOneNewProvider($userId,$requestId,$providerId);

    if ($returnValue != NULL) {
        $response = $returnValue;
    } else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function getAllRequests() {
    //global $app;
    $app = \Slim\Slim::getInstance();
    //$req = $app->request(); // Getting parameters

    $apikey = $app->request->params('apikey');
    $db = new DbHandler();
    $userId = $db->getUserId($apikey);
    $response = array();
    $returnValue = $db->getAllRequests($userId);

    if ($returnValue != NULL) {
        $response = $returnValue;
    } else {
        // unknown error occurred
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function review() {
    //global $app;
    $app = \Slim\Slim::getInstance();
    verifyRequiredParams(array('provider_id','request_id'));

    $req = $app->request(); // Getting parameters
    $requestId = $req->params('request_id');
    $providerId = $req->params('provider_id');
    $rating = $req->params('rating');
    $comment = $req->params('comment');

    $apiKey = $app->request->params('apikey');
    $db = new DbHandler();
    $userId = $db->getUserId($apiKey);
    $review = $db->addReview($userId,$requestId, $providerId, $rating, $comment);

    $response = array();
    if ($review == TRUE) {
        $response["error"] = FALSE;
        $response['message'] = "Review has been recorded";
    } else {
        $response['error'] = TRUE;
        $response['message'] = "An error occurred. Please try again";
    }
    echoRespnse(200, $response);
}

function anonymousRequest() {
    $app = \Slim\Slim::getInstance();
}

// function objectToArray($d) {
//         if (is_object($d)) {
//             // Gets the properties of the given object with get_object_vars function
//             $d = get_object_vars($d);
//         }
 
//         if (is_array($d)) {
            
//             * Return array converted to object
//             * Using __FUNCTION__ (Magic constant) for recursive call
            
//             return array_map(__FUNCTION__, $d);
//         }
//         else {
//             // Return array
//             return $d;
//         }
//     }


/**
 * Verifying required params posted or not
 */
function verifyRequiredParams($required_fields) {
    $error = FALSE;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
        $app = \Slim\Slim::getInstance();
        parse_str($app->request()->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error = TRUE;
            $error_fields .= $field . ', ';
        }
    }

    if ($error) {
        // Required field(s) are missing or empty
        // echo error json and stop the app
        $response = array();
        $app = \Slim\Slim::getInstance();
        $response["error"] = TRUE;
        $response["message"] = 'Required field(s) ' . substr($error_fields, 0, -2) . ' is missing or empty';
        echoRespnse(400, $response);
        $app->stop();
    }
}

/**
 * Validating email address
 */
function validateEmail($email) {
    $app = \Slim\Slim::getInstance();
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response["error"] = TRUE;
        $response["message"] = 'Email address is not valid';
        echoRespnse(400, $response);
        $app->stop();
    }
}

function echoRespnse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);

    // setting response content type to json
    $app->contentType('application/json');

    echo json_encode($response);
}

?>

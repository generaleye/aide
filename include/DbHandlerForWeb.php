<?php
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 * Author: Generaleye
 */

if(isset($_POST['methods'])) {
    $db = new DbHandlerForWeb();
    switch ($_POST['methods']) {
        case "approveRequest":
            $db->approveRequest($_POST['request'],$_POST['provider']);
            break;
        case "declineRequest":
            $db->declineRequest($_POST['request'],$_POST['provider']);
            break;
        case "completeRequest":
            $db->completeRequest($_POST['request']);
            break;
        case "abortRequest":
            $db->abortRequest($_POST['request']);
            break;
        default:
            break;
    }
} else {
    require_once ('api/libs/sendgrid-php/sendgrid-php.php');
    require_once dirname(__FILE__) . '/SendGridEmail.php';
}

class DbHandlerForWeb {

    private $conn;
    private $sendgrid;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';

        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    /* ------------- `providers` table method ------------------ */

    /**
     * Creating new user
     */
    public function createProvider($name,$email,$password,$phone,$service,$address,$latitude,$longitude) {
        require_once ('PassHash.php');
        //require_once ('SendGridEmail.php');
        //$response = array();

        // Check if user already exists in db
        if (!$this->isEmailExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);

            // Generating API key
            $api_key = $this->generateApiKey();

            if (!$this->isApikeyExists($api_key)) {

                // insert query
                $sql = "INSERT INTO providers (`name`, `email_address`, `password`, `latitude`, `longitude`, `phone_number`, `address`, `service_type_id`, `api_key`, `created_time`)
                        VALUES (:name, :email, :password, :latitude, :longitude, :phone, :address, :service, :apikey, NOW())";
                try {
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam("name", $name);
                    $stmt->bindParam("email", $email);
                    $stmt->bindParam("password", $password_hash);
                    $stmt->bindParam("latitude", $latitude);
                    $stmt->bindParam("longitude", $longitude);
                    $stmt->bindParam("phone", $phone);
                    $stmt->bindParam("address", $address);
                    $stmt->bindParam("service", $service);
                    $stmt->bindParam("apikey", $api_key);
                    $result = $stmt->execute();
                } catch (PDOException $e) {
                    echo '{"error":{"text":' . $e->getMessage() . '}}';
                }

                // Check for successful insertion
                if ($result) {
                    // User successfully inserted
                    $sendEmail = new SendGridEmail();
                    $sendEmail->sendProviderRegistrationEmail($email);
                    //$this->sendRegistrationEmail($email);
                    $_SESSION['apikey'] = $api_key;
                    return REGISTRATION_SUCCESSFUL;
                } else {
                    // Failed to create user
                    return REGISTRATION_FAILED;
                }
            } else {
                // User with same apikey already exists in the db
                return REGISTRATION_FAILED;
            }
        } else {
            // User with same email already exists in the db
            return EMAIL_ALREADY_EXISTS;
        }

    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function providerLogin($email, $password) {
        require_once ('PassHash.php');
        // fetching user by email
        $sql = "SELECT `password` FROM `providers` WHERE `email_address` = :email";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("email", $email);
            $stmt->execute();
            $num_rows = $stmt->rowCount();
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
        if ($num_rows > 0) {
            // Found user with the email
            // Now verify the password
            $password_hash = $stmt->fetch(PDO::FETCH_ASSOC);
            if (PassHash::check_password($password_hash['password'], $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            // user doesn't exist with the email
            return FALSE;
        }
    }

    /**
     * Checking user login
     * @param String $email User login email id
     * @param String $password User login password
     * @return boolean User login status success/fail
     */
    public function userLogin($email, $password) {
        require_once ('PassHash.php');
        // fetching user by email
        $sql = "SELECT `password` FROM `users` WHERE `email_address` = :email";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("email", $email);
            $stmt->execute();
            $num_rows = $stmt->rowCount();
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
        if ($num_rows > 0) {
            // Found user with the email
            // Now verify the password
            $password_hash = $stmt->fetch(PDO::FETCH_ASSOC);
            if (PassHash::check_password($password_hash['password'], $password)) {
                // User password is correct
                return TRUE;
            } else {
                // user password is incorrect
                return FALSE;
            }
        } else {
            // user doesn't exist with the email
            return FALSE;
        }
    }

    /**
     * Checking for duplicate user by email address
     * @param String $email email to check in db
     * @return boolean
     */
    private function isEmailExists($email) {
        $sql = "SELECT `provider_id` from `providers` WHERE `email_address` = :email";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("email", $email);
            $stmt->execute();
            $num_rows = $stmt->rowCount();
            return $num_rows > 0;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    /**
     * Checking for duplicate apikey
     * @param String $apikey value to check in db
     * @return boolean
     */
    private function isApikeyExists($apikey) {
        $sql = "SELECT `provider_id` from `providers` WHERE `api_key` = :apikey";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("apikey", $apikey);
            $stmt->execute();
            $num_rows = $stmt->rowCount();
            return $num_rows > 0;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    /**
     * Fetching user by email
     * @param String $email User email id
     */
    public function getProviderByEmail($email) {
        $sql = "SELECT `provider_id`, `email_address`, `api_key`, `created_time` FROM `providers` WHERE `email_address` = :email";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("email", $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    /**
     * Fetching user api key
     * @param String $user_id user id primary key in user table
     */
    public function getApiKeyById($user_id) {
        $sql = "SELECT `api_key` FROM `providers` WHERE `id` = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("id", $user_id);
            $stmt->execute();
            $key = $stmt->fetch(PDO::FETCH_ASSOC);
            return $key['api_key'];
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    /**
     * Fetching user id by api key
     * @param String $api_key user api key
     */
    public function getProviderId($api_key) {
        $sql = "SELECT `provider_id` FROM `providers` WHERE `api_key` = :api_key";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("api_key", $api_key);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user['user_id'];
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }


    /**
     * Validating user api key
     * If the api key is there in db, it is a valid key
     * @param String $api_key user api key
     * @return boolean
     */
    public function isValidApiKey($api_key) {
        $sql = "SELECT `provider_id` from `providers` WHERE `api_key` = :api_key";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("api_key", $api_key);
            $stmt->execute();
            $num_rows = $stmt->rowCount();
            return $num_rows > 0;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    /**
     * Generating random Unique MD5 String for user Api key
     */
    private function generateApiKey() {
        return md5(time().uniqid(rand(), TRUE));
    }

    /**
     * Get the email of users using their id
     * @param $id
     */
    public function getUserEmailById($id) {
        $sql = "SELECT `email_address` FROM `users` WHERE `user_id` = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user['email_address'];
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

//    /**
//     * Get the id of providers using their email
//     * @param $email
//     */
//    public function getProviderIdByEmail($email) {
//        $sql = "SELECT `provider_id` FROM `providers` WHERE `email_address` = :email";
//        try {
//            $stmt = $this->conn->prepare($sql);
//            $stmt->bindParam("email", $email);
//            $stmt->execute();
//            $provider = $stmt->fetch(PDO::FETCH_ASSOC);
//            return $provider['provider_id'];
//        } catch(PDOException $e) {
//            echo '{"error":{"text":'. $e->getMessage() .'}}';
//        }
//    }

    /**
     * Get the email of providers using their id
     * @param $id
     */
    public function getProviderEmailById($id) {
        $sql = "SELECT `email_address` FROM `providers` WHERE `provider_id` = :id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $provider = $stmt->fetch(PDO::FETCH_ASSOC);
            return $provider['email_address'];
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }


    /**
     * ----------------------------------------------------------------------------------
     * DB Handler codes for the other calls
     */

    public function getProviderProfileById($id) {
        $sql = "SELECT `name`, `email_address`, `profile_picture`, `phone_number`, `address`, `longitude`, `latitude` FROM `providers` WHERE `provider_id` =:id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            return $profile;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function updateProviderProfileById($id, $name, $latitude, $longitude, $phone, $address) {
        $sql = "UPDATE `providers` SET `name` = :name, `latitude` = :latitude, `longitude` = :longitude, `phone_number` = :phone, `address` = :address, `modified_time` = NOW() WHERE `provider_id` =:id";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->bindParam("name", $name);
            $stmt->bindParam("latitude", $latitude);
            $stmt->bindParam("longitude", $longitude);
            $stmt->bindParam("phone", $phone);
            $stmt->bindParam("address", $address);
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function sendNotification($owner, $subject, $object, $type) {
        $sql = "INSERT INTO `notifications` (`own_id`, `sub_id`, `obj_id`, `notification_type`, `created_time`) VALUES (:owner, :subject, :object, :n_type, NOW())";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("owner", $owner);
            $stmt->bindParam("subject", $subject);
            $stmt->bindParam("object", $object);
            $stmt->bindParam("n_type", intval($type));
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getProvidersRequests($email) {
        $id = $this->getProviderByEmail($email)['provider_id'];
        $sql = 'SELECT requests.request_id,
                        requests.created_time AS `request_created_time`,
                         request_checks.request_id AS `checks_request_id`,
                         request_statuses.name AS `request_statuses_name`,
                          service_statuses.name AS `service_statuses_name`,
                         users.first_name, users.last_name
                FROM `requests`, `request_checks`, `users`, `request_statuses`, `service_statuses`
                WHERE requests.request_id = request_checks.request_id AND
                 request_checks.provider_id = :id AND requests.user_id = users.user_id AND
                  request_checks.request_status_id = request_statuses.request_status_id AND
                   requests.service_status_id = service_statuses.service_status_id AND
                    requests.active_status = 1';
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->execute();
            $requestsArr  = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //$postsArr = objectToArray($posts);
            $leng = count($requestsArr);
            $arr = array('count'=>$leng, 'requests'=>$requestsArr);
            return $arr;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getRequest($id,$email) {
        $provider = $this->getProviderByEmail($email)['provider_id'];
        $sql = 'SELECT requests.request_id, requests.user_id, requests.latitude, requests.longitude,
                request_checks.request_id AS `checks_request_id`,
                request_checks.request_status_id AS `statuses_request_status_id`, request_statuses.name AS `request_statuses_name`,
                 service_statuses.service_status_id AS `statuses_service_status_id`, service_statuses.name AS `service_statuses_name`,
                  users.first_name, users.last_name, users.phone_number
                FROM `requests`, `request_checks`, `users`, `request_statuses`, `service_statuses`
                WHERE requests.request_id = :id AND
                 request_checks.provider_id = :provider AND
                  requests.user_id = users.user_id AND
                  requests.request_id = request_checks.request_id AND
                   request_checks.request_status_id = request_statuses.request_status_id AND
                    requests.service_status_id = service_statuses.service_status_id AND
                     requests.active_status = 1';
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->bindParam("provider", $provider);
            $stmt->execute();
            $requestArr  = $stmt->fetch(PDO::FETCH_ASSOC);
            //$postsArr = objectToArray($posts);
            //$leng = count($requestsArr);
            //$arr = array('count'=>$leng, 'requests'=>$requestsArr);
            return $requestArr;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getKinsForProviders($userId) {
        $sql = 'SELECT `kin_id`, `first_name`, `last_name`, `phone_number`, `email_address`, `address` FROM `kins` WHERE `user_id` = :userId AND `active_status` = 1';
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("userId", $userId);
            $stmt->execute();
            $kinsArr  = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //$postsArr = objectToArray($posts);
            $leng = count($kinsArr);
            $arr = array('count'=>$leng, 'kins'=>$kinsArr);
            return $arr;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function approveRequest($request,$email) {
        $provider = $this->getProviderByEmail($email)['provider_id'];
        $sql = "UPDATE `request_checks` SET `request_status_id` = 2, `modified_time` = NOW() WHERE `request_id` = :request AND `provider_id` = :provider";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("request", intval($request));
            $stmt->bindParam("provider", intval($provider));
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function declineRequest($request,$email) {
        $provider = $this->getProviderByEmail($email)['provider_id'];
        $sql = "UPDATE `request_checks` SET `request_status_id` = 3, `modified_time` = NOW() WHERE `request_id` = :request AND `provider_id` = :provider";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("request", intval($request));
            $stmt->bindParam("provider", intval($provider));
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function completeRequest($request) {
        $sql = "UPDATE `requests` SET `service_status_id` = 1, `modified_time` = NOW() WHERE `request_id` = :request";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("request", intval($request));
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }
    public function abortRequest($request) {
        $sql = "UPDATE `requests` SET `service_status_id` = 3, `modified_time` = NOW() WHERE `request_id` = :request";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("request", intval($request));
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }



}

?>
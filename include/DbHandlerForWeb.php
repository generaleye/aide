<?php
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 * Author: Generaleye
 */
class DbHandlerForWeb {

    private $conn;
    private $sendgrid;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        require_once ('api/libs/sendgrid-php/sendgrid-php.php');
        require_once dirname(__FILE__) . '/SendGridEmail.php';
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
        $sql = "SELECT `email_address`, `api_key`, `created_time` FROM `providers` WHERE `email_address` = :email";
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

//    public function addKins($userId, $fname, $lname, $phone, $address, $email) {
//        $sql = "INSERT INTO kins (`user_id`, `first_name`,`last_name`, `phone_number`, `address`, `email_address`, `created_time`) VALUES (:userId, :fname, :lname, :phone, :address, :email, NOW())";
//        try {
//            $stmt = $this->conn->prepare($sql);
//            $stmt->bindParam("userId", $userId);
//            $stmt->bindParam("fname", $fname);
//            $stmt->bindParam("lname", $lname);
//            $stmt->bindParam("phone", $phone);
//            $stmt->bindParam("address", $address);
//            $stmt->bindParam("email", $email);
//            $stmt->execute();
//            return TRUE;
//        } catch(PDOException $e) {
//            echo '{"error":{"text":'. $e->getMessage() .'}}';
//        }
//    }
//
//    public function getKins($userId) {
//        $sql = 'SELECT `kin_id`, `first_name`, `last_name`, `phone_number`, `email_address`, `address` FROM `kins` WHERE `user_id` = :userId AND `active_status` = 1';
//        try {
//            $stmt = $this->conn->prepare($sql);
//            $stmt->bindParam("userId", $userId);
//            $stmt->execute();
//            $kinsArr  = $stmt->fetchAll(PDO::FETCH_ASSOC);
//            //$postsArr = objectToArray($posts);
//            $leng = count($kinsArr);
//            if($leng==0) {
//                $startId = 0;
//                $endId = 0;
//            } else {
//                $startId = $kinsArr[0]['kin_id'];
//                $endId = $kinsArr[$leng-1]['kin_id'];
//            }
//            $arr = array('start'=>$startId, 'end'=>$endId, 'count'=>$leng, 'kins'=>$kinsArr);
//            return $arr;
//        } catch(PDOException $e) {
//            echo '{"error":{"text":'. $e->getMessage() .'}}';
//        }
//    }
//
//    public function updateKinsById($userId, $kinId, $fname, $lname, $phone, $email, $address) {
//        $sql = "UPDATE `kins` SET `first_name` = :fname, `last_name` = :lname, `phone_number` = :phone, `email_address` = :email, `address` = :address, `modified_time` = NOW() WHERE `user_id` =:userId AND `kin_id` =:kinId";
//        try {
//            $stmt = $this->conn->prepare($sql);
//            $stmt->bindParam("userId", $userId);
//            $stmt->bindParam("kinId", $kinId);
//            $stmt->bindParam("fname", $fname);
//            $stmt->bindParam("lname", $lname);
//            $stmt->bindParam("phone", $phone);
//            $stmt->bindParam("email", $email);
//            $stmt->bindParam("address", $address);
//            $stmt->execute();
//            return TRUE;
//        } catch(PDOException $e) {
//            echo '{"error":{"text":'. $e->getMessage() .'}}';
//        }
//    }
//
//    public function deleteKinsById($id,$userId) {
//        $sql = "SELECT `user_id` FROM  `kins` WHERE `kin_id` =:id AND `user_id` = :userId";
//        try {
//            $stmt = $this->conn->prepare($sql);
//            $stmt->bindParam("id", $id);
//            $stmt->bindParam("userId", $userId);
//            $stmt->execute();
//            $kinUserId = $stmt->fetch(PDO::FETCH_ASSOC);
//
//            if ($kinUserId==$userId) {
//                $sql = "UPDATE `kins` SET `active_status` = 0 WHERE `kin_id` =:id AND `user_id` = :userId";
//                try {
//                    $stmt = $this->conn->prepare($sql);
//                    $stmt->bindParam("id", $id);
//                    $stmt->bindParam("userId", $userId);
//                    $stmt->execute();
//                    return TRUE;
//                } catch(PDOException $e) {
//                    echo '{"error":{"text":'. $e->getMessage() .'}}';
//                }
//            } else {
//                return FALSE;
//            }
//
//        } catch(PDOException $e) {
//            echo '{"error":{"text":'. $e->getMessage() .'}}';
//        }
//
//    }
//
//    public function sendTextToKins($userId,$device_id,$longitude,$latitude,$address,$type) {
//        if ($longitude!="" && $latitude!="") {
//            if ($address=="") {
//                $address = "UNKNOWN";
//            }
//        } else {
//            if ($address!="") {
//                $longitude = "UNKNOWN";
//                $latitude = "UNKNOWN";
//            } else {
//                $response['error'] = TRUE;
//                $response['message'] = "Please specify your address";
//                return $response;
//            }
//        }
//        //count no of kins
//        if ($this->getKins($userId)['count']!=0) {
//            $sql = "INSERT INTO `requests` (`user_id`, `device_id`, `longitude`, `latitude`, `address`, `service_type_id`, `created_time`) VALUES (:userId, :device_id, :longitude, :latitude, :address, :service_type, NOW())";
//            try {
//                $stmt = $this->conn->prepare($sql);
//                $stmt->bindParam("userId", $userId);
//                $stmt->bindParam("device_id", $device_id);
//                $stmt->bindParam("longitude", $longitude);
//                $stmt->bindParam("latitude", $latitude);
//                $stmt->bindParam("address", $address);
//                $stmt->bindParam("service_type", intval($type));
//                $stmt->execute();
//                //return TRUE;
//
//                //send the message to all the kins here
//
//                $id = $this->conn->lastInsertId();
//                $sql = "UPDATE `requests` SET `service_status_id` = 1, `modified_time` = NOW() WHERE `request_id` =:id AND `user_id` = :userId";
//                try {
//                    $this->conn->beginTransaction();
//                    $stmt = $this->conn->prepare($sql);
//                    $stmt->bindParam("id", $id);
//                    $stmt->bindParam("userId", $userId);
//                    $stmt->execute();
//                    $this->conn->commit();
//
//                    $response['error'] = FALSE;
//                    $response['message'] = "SOS Message has been sent";
//                } catch(PDOException $e) {
//                    $this->conn->rollBack();
//                    echo '{"error":{"text":'. $e->getMessage() .'}}';
//                }
//
//
//            } catch(PDOException $e) {
//                echo '{"error":{"text":'. $e->getMessage() .'}}';
//            }
//        } else {
//            //return NULL;
//            $response['error'] = TRUE;
//            $response['message'] = "You have not added anyone as your next of kin";
//        }
//        return $response;
//    }
//
//    public function getProviders($userId,$device_id,$longitude,$latitude,$address,$type) {
//        require_once ('LatLong.php');
//        $response = array();
//        if ($longitude!="" && $latitude!="") {
//            $latLon = new LatLong($latitude,$longitude);
//            $leng = 0;
//            $radius = 10;
//            while ($leng == 0) {
//                if ($radius >= 45) {
//                    $response['error'] = TRUE;
//                    $response['message'] = "No Service Provider found";
//                    return $response;
//                }
//                $lonLatArr = $latLon->getResult($radius);
//
//                $sql = 'SELECT `provider_id`, `name`, `email_address`, `longitude`, `latitude`, `address` FROM `providers` WHERE `service_type_id` = :type AND `latitude` BETWEEN :minLat AND :maxLat AND longitude BETWEEN :minLon AND :maxLon AND `active_status` = 1';
//                try {
//                    $stmt = $this->conn->prepare($sql);
//                    $stmt->bindParam("minLat", $lonLatArr['minLat']);
//                    $stmt->bindParam("maxLat", $lonLatArr['maxLat']);
//                    $stmt->bindParam("minLon", $lonLatArr['minLon']);
//                    $stmt->bindParam("maxLon", $lonLatArr['maxLon']);
//                    $stmt->bindParam("type", intval($type));
//                    $stmt->execute();
//                    $providersArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
//                    //$postsArr = objectToArray($posts);
//                    $leng = count($providersArr);
//                    if ($leng == 0) {
//                        $radius += 5;
//                    } else {
//                        $arr = array('error' => FALSE, 'count' => $leng, 'providers' => $providersArr);
//                        return $arr;
//                    }
//
//                } catch (PDOException $e) {
//                    echo '{"error":{"text":' . $e->getMessage() . '}}';
//                }
//            }
//
//        } else {
//            if ($address!="") {
//                $address = "%$address%";
//                $sql = 'SELECT `provider_id`, `name`, `email_address`, `longitude`, `latitude`, `address` FROM `providers` WHERE `service_type_id` = :type AND `address` LIKE :val OR `name` LIKE :val AND `active_status` = 1';
//                try {
//                    $stmt = $this->conn->prepare($sql);
//                    $stmt->bindParam("val", $address);
//                    $stmt->bindParam("type", intval($type));
//                    $stmt->execute();
//                    $providersArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
//                    //$postsArr = objectToArray($posts);
//                    $leng = count($providersArr);
//                    if ($leng == 0) {
//                        $response['error'] = TRUE;
//                        $response['message'] = "No Service Provider found";
//                        return $response;
//                    } else {
//                        $arr = array('error' => FALSE, 'count' => $leng, 'providers' => $providersArr);
//                        return $arr;
//                    }
//
//                } catch (PDOException $e) {
//                    echo '{"error":{"text":' . $e->getMessage() . '}}';
//                }
//            } else {
//                $response['error'] = TRUE;
//                $response['message'] = "Please specify your address";
//            }
//        }
//        return $response;
//    }
//
//    public function selectProvider($userId,$device_id,$providerId,$longitude,$latitude,$address,$type) {
//        require_once ('SendGridEmail.php');
//        $sql = "INSERT INTO `requests` (`user_id`, `device_id`, `longitude`, `latitude`, `address`, `service_type_id`, `created_time`) VALUES (:userId, :device_id, :longitude, :latitude, :address, :service_type, NOW())";
//        try {
//            $this->conn->beginTransaction();
//            $stmt = $this->conn->prepare($sql);
//            $stmt->bindParam("userId", $userId);
//            $stmt->bindParam("device_id", $device_id);
//            $stmt->bindParam("longitude", $longitude);
//            $stmt->bindParam("latitude", $latitude);
//            $stmt->bindParam("address", $address);
//            $stmt->bindParam("service_type", intval($type));
//            $stmt->execute();
//            //return TRUE;
//
//            //send the message to all the kins here
//
//            $requestId = $this->conn->lastInsertId();
//            $sql = "INSERT INTO `request_checks` (`request_id`, `provider_id`, `request_status_id`, `created_time`) VALUES (:request_id, :provider_id, 1, NOW())";
//            try {
//
//                $stmt = $this->conn->prepare($sql);
//                $stmt->bindParam("request_id", $requestId);
//                $stmt->bindParam("provider_id", $providerId);
//                //$stmt->bindParam("request_status_id", 1);
//                $stmt->execute();
//                $this->conn->commit();
//
//                $this->sendNotification($providerId,$userId, $requestId, 3);
//                $sendEmail = new SendGridEmail();
//                $sendEmail->sendEmergencyEmail($this->getProviderEmailById($providerId),$this->getUserEmailById($userId));
//                //$this->sendEmergencyEmail($this->getProviderEmailById($providerId),$this->getUserEmailById($userId));
//                $response['error'] = FALSE;
//                $response['message'] = "Your Request has been sent";
//                return $response;
//            } catch(PDOException $e) {
//
//                echo '{"error":{"text":'. $e->getMessage() .'}}';
//            }
//
//
//        } catch(PDOException $e) {
//            $this->conn->rollBack();
//            echo '{"error":{"text":'. $e->getMessage() .'}}';
//        }
//    }
//
//    public function addReview($userId, $providerId, $rating, $comment) {
//        if ($rating=="") {$rating = 0;}
//        $sql = "INSERT INTO `reviews` (`user_id`, `provider_id`,`rating`, `comment`, `created_time`) VALUES (:userId, :provider_id, :rating, :comment, NOW())";
//        try {
//            $stmt = $this->conn->prepare($sql);
//            $stmt->bindParam("userId", $userId);
//            $stmt->bindParam("provider_id", $providerId);
//            $stmt->bindParam("rating", intval($rating));
//            $stmt->bindParam("comment", $comment);
//            $stmt->execute();
//            return TRUE;
//        } catch(PDOException $e) {
//            echo '{"error":{"text":'. $e->getMessage() .'}}';
//        }
//    }


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

}

?>

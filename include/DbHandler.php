<?php
/**
 * Class to handle all db operations
 * This class will have CRUD methods for database tables
 * Author: Generaleye
 */
class DbHandler {

    private $conn;
    private $sendgrid;

    function __construct() {
        require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
        require_once ('../libs/sendgrid-php/sendgrid-php.php');
        $this->sendgrid = new SendGrid(SENDGRID_USERNAME, SENDGRID_PASSWORD);
    }

    /* ------------- `users` table method ------------------ */

    /**
     * Creating new user
     */
    public function createUser($email, $password) {
        require_once 'PassHash.php';
        //$response = array();

        // Check if user already exists in db
        if (!$this->isEmailExists($email)) {
            // Generating password hash
            $password_hash = PassHash::hash($password);

            // Generating API key
            $api_key = $this->generateApiKey();

            if (!$this->isApikeyExists($api_key)) {

                // insert query
                $sql = "INSERT INTO users (`email_address`, `password`, `api_key`, `created_time`) VALUES (:email, :password, :apikey, NOW())";
                try {
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam("email", $email);
                    $stmt->bindParam("password", $password_hash);
                    $stmt->bindParam("apikey", $api_key);
                    $result = $stmt->execute();
                } catch (PDOException $e) {
                    echo '{"error":{"text":' . $e->getMessage() . '}}';
                }

                // Check for successful insertion
                if ($result) {
                    // User successfully inserted
                    $this->sendRegistrationEmail($email);
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
    public function checkLogin($email, $password) {
        // fetching user by email
        $sql = "SELECT password FROM users WHERE email_address = :email";
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
        $sql = "SELECT user_id from `users` WHERE `email_address` = :email";
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
        $sql = "SELECT user_id from `users` WHERE `api_key` = :apikey";
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
    public function getUserByEmail($email) {
        $sql = "SELECT `email_address`, `api_key`, `created_time` FROM `users` WHERE `email_address` = :email";
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
        $sql = "SELECT api_key FROM users WHERE id = :id";
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
    public function getUserId($api_key) {
        $sql = "SELECT `user_id` FROM `users` WHERE `api_key` = :api_key";
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
        $sql = "SELECT `user_id` from `users` WHERE `api_key` = :api_key";
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

    public function getProfileById($id) {
        $sql = "SELECT `first_name`, `last_name`, `email_address`, `profile_picture`, `sex`, `phone_number`, `address` FROM `users` WHERE `user_id` =:id";
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

    public function updateProfileById($userId, $fname, $lname, $sex, $phone, $address) {
        $sql = "UPDATE `users` SET `first_name` = :fname, `last_name` = :lname, `sex` = :sex, `phone_number` = :phone, `address` = :address, `modified_time` = NOW() WHERE `user_id` =:userId";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("userId", $userId);
            $stmt->bindParam("fname", $fname);
            $stmt->bindParam("lname", $lname);
            $stmt->bindParam("sex", $sex);
            $stmt->bindParam("phone", $phone);
            $stmt->bindParam("address", $address);
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function addKins($userId, $fname, $lname, $phone, $address, $email) {
        $sql = "INSERT INTO kins (`user_id`, `first_name`,`last_name`, `phone_number`, `address`, `email_address`, `created_time`) VALUES (:userId, :fname, :lname, :phone, :address, :email, NOW())";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("userId", $userId);
            $stmt->bindParam("fname", $fname);
            $stmt->bindParam("lname", $lname);
            $stmt->bindParam("phone", $phone);
            $stmt->bindParam("address", $address);
            $stmt->bindParam("email", $email);
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function getKins($userId) {
        $sql = 'SELECT `kin_id`, `first_name`, `last_name`, `phone_number`, `email_address`, `address` FROM `kins` WHERE `user_id` = :userId AND `active_status` = 1';
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("userId", $userId);
            $stmt->execute();
            $kinsArr  = $stmt->fetchAll(PDO::FETCH_ASSOC);
            //$postsArr = objectToArray($posts);
            $leng = count($kinsArr);
            if($leng==0) {
                $startId = 0;
                $endId = 0;
            } else {
                $startId = $kinsArr[0]['kin_id'];
                $endId = $kinsArr[$leng-1]['kin_id'];
            }
            $arr = array('start'=>$startId, 'end'=>$endId, 'count'=>$leng, 'kins'=>$kinsArr);
            return $arr;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function updateKinsById($userId, $kinId, $fname, $lname, $phone, $email, $address) {
        $sql = "UPDATE `kins` SET `first_name` = :fname, `last_name` = :lname, `phone_number` = :phone, `email_address` = :email, `address` = :address, `modified_time` = NOW() WHERE `user_id` =:userId AND `kin_id` =:kinId";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("userId", $userId);
            $stmt->bindParam("kinId", $kinId);
            $stmt->bindParam("fname", $fname);
            $stmt->bindParam("lname", $lname);
            $stmt->bindParam("phone", $phone);
            $stmt->bindParam("email", $email);
            $stmt->bindParam("address", $address);
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function deleteKinsById($id,$userId) {
        $sql = "UPDATE `kins` SET `active_status` = 0 WHERE `kin_id` =:id AND `user_id` = :userId";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("id", $id);
            $stmt->bindParam("userId", $userId);
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function sendTextToKins($userId,$device_id,$longitude,$latitude,$address,$type) {
        if ($longitude!="" && $latitude!="") {
            if ($address=="") {
                $address = "UNKNOWN";
            }
        } else {
            if ($address!="") {
                $longitude = "UNKNOWN";
                $latitude = "UNKNOWN";
            } else {
                $response['error'] = TRUE;
                $response['message'] = "Please specify your address";
                return $response;
            }
        }
        //count no of kins
        if ($this->getKins($userId)['count']!=0) {
            $sql = "INSERT INTO `requests` (`user_id`, `device_id`, `longitude`, `latitude`, `address`, `service_type_id`, `created_time`) VALUES (:userId, :device_id, :longitude, :latitude, :address, :service_type, NOW())";
            try {
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam("userId", $userId);
                $stmt->bindParam("device_id", $device_id);
                $stmt->bindParam("longitude", $longitude);
                $stmt->bindParam("latitude", $latitude);
                $stmt->bindParam("address", $address);
                $stmt->bindParam("service_type", intval($type));
                $stmt->execute();
                //return TRUE;

                //send the message to all the kins here

                $id = $this->conn->lastInsertId();
                $sql = "UPDATE `requests` SET `service_status_id` = 1, `modified_time` = NOW() WHERE `request_id` =:id AND `user_id` = :userId";
                try {
                    $this->conn->beginTransaction();
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam("id", $id);
                    $stmt->bindParam("userId", $userId);
                    $stmt->execute();
                    $this->conn->commit();

                    $response['error'] = FALSE;
                    $response['message'] = "SOS Message has been sent";
                } catch(PDOException $e) {
                    $this->conn->rollBack();
                    echo '{"error":{"text":'. $e->getMessage() .'}}';
                }


            } catch(PDOException $e) {
                echo '{"error":{"text":'. $e->getMessage() .'}}';
            }
        } else {
            //return NULL;
            $response['error'] = TRUE;
            $response['message'] = "You have not added anyone as your next of kin";
        }
        return $response;
    }

    public function getProviders($userId,$device_id,$longitude,$latitude,$address,$type) {
        require_once 'LatLong.php';
        $response = array();
        if ($longitude!="" && $latitude!="") {
            $latLon = new LatLong($latitude,$longitude);
            $leng = 0;
            $radius = 10;
            while ($leng == 0) {
                if ($radius >= 45) {
                    $response['error'] = TRUE;
                    $response['message'] = "No Service Provider found";
                    return $response;
                }
                $lonLatArr = $latLon->getResult($radius);

                $sql = 'SELECT `provider_id`, `name`, `email_address`, `longitude`, `latitude`, `address` FROM `providers` WHERE `latitude` BETWEEN :minLat AND :maxLat AND longitude BETWEEN :minLon AND :maxLon AND `service_type_id` = :type';
                try {
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam("minLat", $lonLatArr['minLat']);
                    $stmt->bindParam("maxLat", $lonLatArr['maxLat']);
                    $stmt->bindParam("minLon", $lonLatArr['minLon']);
                    $stmt->bindParam("maxLon", $lonLatArr['maxLon']);
                    $stmt->bindParam("type", $type);
                    $stmt->execute();
                    $providersArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    //$postsArr = objectToArray($posts);
                    $leng = count($providersArr);
                    if ($leng == 0) {
                        $radius += 5;
                    } else {
                        $arr = array('error' => FALSE, 'count' => $leng, 'providers' => $providersArr);
                        return $arr;
                    }

                } catch (PDOException $e) {
                    echo '{"error":{"text":' . $e->getMessage() . '}}';
                }
            }

        } else {
            if ($address!="") {
                $address = "%$address%";
                $sql = 'SELECT `provider_id`, `name`, `email_address`, `longitude`, `latitude`, `address` FROM `providers` WHERE `address` LIKE :val OR `name` LIKE :val AND `service_type_id` = :type';
                try {
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bindParam("val", $address);
                    $stmt->bindParam("type", $type);
                    $stmt->execute();
                    $providersArr = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    //$postsArr = objectToArray($posts);
                    $leng = count($providersArr);
                    if ($leng == 0) {
                        $response['error'] = TRUE;
                        $response['message'] = "No Service Provider found";
                        return $response;
                    } else {
                        $arr = array('error' => FALSE, 'count' => $leng, 'providers' => $providersArr);
                        return $arr;
                    }

                } catch (PDOException $e) {
                    echo '{"error":{"text":' . $e->getMessage() . '}}';
                }
            } else {
                $response['error'] = TRUE;
                $response['message'] = "Please specify your address";
            }
        }
        return $response;
    }

    public function selectProvider($userId,$device_id,$providerId,$longitude,$latitude,$address,$type) {
        $sql = "INSERT INTO `requests` (`user_id`, `device_id`, `longitude`, `latitude`, `address`, `service_type_id`, `created_time`) VALUES (:userId, :device_id, :longitude, :latitude, :address, :service_type, NOW())";
        try {
            $this->conn->beginTransaction();
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("userId", $userId);
            $stmt->bindParam("device_id", $device_id);
            $stmt->bindParam("longitude", $longitude);
            $stmt->bindParam("latitude", $latitude);
            $stmt->bindParam("address", $address);
            $stmt->bindParam("service_type", intval($type));
            $stmt->execute();
            //return TRUE;

            //send the message to all the kins here

            $requestId = $this->conn->lastInsertId();
            $sql = "INSERT INTO `request_checks` (`request_id`, `provider_id`, `request_status_id`, `created_time`) VALUES (:request_id, :provider_id, 1, NOW())";
            try {

                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam("request_id", $requestId);
                $stmt->bindParam("provider_id", $providerId);
                //$stmt->bindParam("request_status_id", 1);
                $stmt->execute();
                $this->conn->commit();

                $this->sendEmergencyEmail($this->getProviderEmailById($providerId),$this->getUserEmailById($userId));
                $response['error'] = FALSE;
                $response['message'] = "Your Request has been sent";
                return $response;
            } catch(PDOException $e) {

                echo '{"error":{"text":'. $e->getMessage() .'}}';
            }


        } catch(PDOException $e) {
            $this->conn->rollBack();
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    public function addReview($userId, $providerId, $rating, $comment) {
        if ($rating=="") {$rating = 0;}
        $sql = "INSERT INTO `reviews` (`user_id`, `provider_id`,`rating`, `comment`, `created_time`) VALUES (:userId, :provider_id, :rating, :comment, NOW())";
        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("userId", $userId);
            $stmt->bindParam("provider_id", $providerId);
            $stmt->bindParam("rating", intval($rating));
            $stmt->bindParam("comment", $comment);
            $stmt->execute();
            return TRUE;
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }
    }

    /**
     * Send an email after Registration using SendGrid's api
     * @param $recipient
     */

    public function sendRegistrationEmail($recipient) {
        $emails = new SendGrid\Email();
        $emails
            ->addTo($recipient)
            ->setBcc(SENDGRID_CC_EMAIL)
            ->setFrom(SENDGRID_FROM_EMAIL)
            ->setFromName(SENDGRID_FROM_NAME)
            ->setSubject('Successful Registration')
            ->setHtml('<h1>Welcome to Aide</h1><br />
                <p>Thanks for registering for our service. Kindly update your profile as soon as possible</p>
                <p><strong>Thank You!</strong></p><br />')
        ;
        //$response = $this->sendgrid->send($email);
        $this->sendgrid->send($emails);
        //var_dump($response);
    }

    /**
     * Send emails to Service Providers in case of emergency
     * @param $provider
     * @param $user
     */

    public function sendEmergencyEmail($provider, $user) {
        $emails = new SendGrid\Email();
        $emails
            ->addTo($provider)
            ->setBcc(SENDGRID_CC_EMAIL)
            ->setFrom(SENDGRID_FROM_EMAIL)
            ->setFromName(SENDGRID_FROM_NAME)
            ->setSubject('Emergency Alert')
            ->setHtml('<h1>Emergency Request</h1><br />
                <p>Hello,</p><br /><p>A user registered with the email: "'.$user.'" has requested for your assistance.</p>
                <p>Please follow this <a href="aide-generaleye.rhcloud.com">link</a> to your profile to accept or decline the request.</p>
                <p><strong>Thank You!</strong></p><br />')
        ;
        //$response = $this->sendgrid->send($email);
        $this->sendgrid->send($emails);
    }

}

?>

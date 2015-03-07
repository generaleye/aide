<?php

class GoogleGCMApi {
    private $gcm_url = 'https://android.googleapis.com/gcm/send';
    private $device_id;
    private $message;

    function __construct($device_id,$message) {
        $this->device_id = $device_id;
        $this->message = $message;
    }

    public function send() {
        $fields = array(
            'registration_ids'  => array( $this->device_id ),
            'data'              => array( "message" => $this->message ),
        );

        $headers = array(
            'Authorization: key=' . GOOGLE_GCM_APIKEY,
            'Content-Type: application/json'
        );


        // Open connection
        $ch = curl_init();

        // Set the url, number of POST vars, POST data
        curl_setopt( $ch, CURLOPT_URL, $this->gcm_url );

        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode( $fields ) );

        // Execute post
        $result = curl_exec($ch);

        if ( curl_errno( $ch ) ){
            echo 'GCM error: ' . curl_error( $ch );
        }

        // Close connection
        curl_close($ch);

        echo $result;

    }


// Message to be sent
//  $message = "DEMO MESSAGE testing api";
//  $registrationIDs = "APA91bGh6hX9CIep5k1SmjeNlVu1GeSlxexr0oGLkchRmutB-XeFEvk7O7lEw4aHQb8gQRaKWHxKCLPAAvqSaGRSpBS7rqwCko7wJ6BRltQzlmj5QrNjQQVYY74zpOOybZ2F67gA4iUKNrjQNnQdLTemBuyJ3gzIlszdHYP-tgGfnxLGZ2Gavx0";
//  $apiKey = "AIzaSyBy6510rXlahAbX_SDejy-zH1vNmgOWS2M";

//$message = $_POST['message'];
//$registrationIDs = $_POST['registrationIDs'];
//$apiKey = $_POST['apiKey'];

//$url = 'https://android.googleapis.com/gcm/send';


}
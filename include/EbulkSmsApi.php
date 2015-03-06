<?php

class EbulkSmsApi {

    //private $ebulksms;
    private $json_url = "http://api.ebulksms.com/sendsms.json";
    private $flash = 0;
    public $result;

    private $recipients;
    private $message;

    function __construct() {

    }

    public function sendText($recipients,$message) {
        $this->recipients = $recipients;
        $this->message = $message;
        if (get_magic_quotes_gpc()) {
            $this->message = stripslashes($this->message);
        }
        $this->message = substr($this->message, 0, 160);
        $this->result = $this->useJSON($this->json_url, EBULK_USERNAME, EBULK_APIKEY, $this->flash, EBULK_FROM_NAME, $this->message, $this->recipients);
        return $this->result;
    }


    public function useJSON($url, $username, $apikey, $flash, $sendername, $messagetext, $recipients) {
        $gsm = array();
        $country_code = '234';
        $arr_recipient = explode(',', $recipients);
        foreach ($arr_recipient as $recipient) {
            $mobilenumber = trim($recipient);
            if (substr($mobilenumber, 0, 1) == '0'){
                $mobilenumber = $country_code . substr($mobilenumber, 1);
            }
            elseif (substr($mobilenumber, 0, 1) == '+'){
                $mobilenumber = substr($mobilenumber, 1);
            }
            $generated_id = uniqid('int_', false);
            $generated_id = substr($generated_id, 0, 30);
            $gsm['gsm'][] = array('msidn' => $mobilenumber, 'msgid' => $generated_id);
        }
        $message = array(
            'sender' => $sendername,
            'messagetext' => $messagetext,
            'flash' => "{$flash}",
        );

        $request = array('SMS' => array(
            'auth' => array(
                'username' => $username,
                'apikey' => $apikey
            ),
            'message' => $message,
            'recipients' => $gsm
        ));
        $json_data = json_encode($request);
        if ($json_data) {
            $response = $this->doPostRequest($url, $json_data, array('Content-Type: application/json'));
            $this->result = json_decode($response);
            return $this->result->response->status;
        } else {
            return false;
        }
    }


    //Function to connect to SMS sending server using HTTP POST
    public function doPostRequest($url, $data, $headers = array('Content-Type: application/x-www-form-urlencoded')) {
        $php_errormsg = '';
        if (is_array($data)) {
            $data = http_build_query($data, '', '&');
        }
        $params = array('http' => array(
            'method' => 'POST',
            'content' => $data)
        );
        if ($headers !== null) {
            $params['http']['header'] = $headers;
        }
        $ctx = stream_context_create($params);
        $fp = fopen($url, 'rb', false, $ctx);
        if (!$fp) {
            return "Error: gateway is inaccessible";
        }
        //stream_set_timeout($fp, 0, 250);
        try {
            $response = stream_get_contents($fp);
            if ($response === false) {
                throw new Exception("Problem reading data from $url, $php_errormsg");
            }
            return $response;
        } catch (Exception $e) {
            $response = $e->getMessage();
            return $response;
        }
    }

}
?>
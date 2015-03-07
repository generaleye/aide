<?php
require_once('include/Config.php');
require_once('include/EbulkSmsApi.php');
$eb = new EbulkSmsApi();

if (isset($_POST['button'])) {
    $recipients = $_POST['telephone'];
    $message = $_POST['message'];
    $result = $eb->sendText($recipients,$message);

#Use the next line for HTTP POST with JSON
 //   $result = useJSON($json_url, $username, $apikey, $flash, $sendername, $message, $recipients);
#Uncomment the next line and comment the one above if you want to use HTTP POST with XML
    //$result = useXML($xml_url, $username, $apikey, $flash, $sendername, $message, $recipients);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>eBulkSMS Send SMS JSON API</title>
</head>

<body>
<h2 style="text-align: center">eBulk SMS Integration Sample Code</h2>
<div style="border: 1px solid #333; padding: 5px 10px; width: 40%; margin: 0 auto">
    <form id="form1" name="form1" method="post" action="">

        <?php
        if (!empty($_POST)) {
            if ($result == 'SUCCESS') {?>
                <p style="border: 1px dotted #333; background: #33ff33; padding: 5px;">Message sent</p>
            <?php
            }
            else {?>
                <p style="border: 1px dotted #333; background: #FFDACC; padding: 5px;">Message not sent</p>
            <?php
            }
        }
        ?>
        <p>
            <label>Recipients
                <textarea name="telephone" id="telephone" cols="45" rows="2"></textarea>
            </label>
        </p>
        <p>
            <label>Message
                <textarea name="message" id="message" cols="45" rows="5"></textarea>
            </label>
        </p>
        <p>
            <label>
                <input type="submit" name="button" id="button" value="Submit" />
            </label>
            <label>
                <input type="reset" name="button2" id="button2" value="Reset" />
            </label>
        </p>
    </form>
</div>
</body>
</html>
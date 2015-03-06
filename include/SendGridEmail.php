<?php

class SendGridEmail {

    private $sendgrid;

    function __construct() {
        $this->sendgrid = new SendGrid(SENDGRID_USERNAME, SENDGRID_PASSWORD);
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
     * Send a Provider an email after Registration using SendGrid's api
     * @param $recipient
     */

    public function sendProviderRegistrationEmail($recipient) {
        $emails = new SendGrid\Email();
        $emails
            ->addTo($recipient)
            ->setBcc(SENDGRID_CC_EMAIL)
            ->setFrom(SENDGRID_FROM_EMAIL)
            ->setFromName(SENDGRID_FROM_NAME)
            ->setSubject('Successful Registration')
            ->setHtml('<h1>Welcome to Aide</h1><br />
                <p>Thanks for registering for our service as a Service Provider. Kindly update your profile as soon as possible</p>
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

    /**
     * Send emails to Service Providers in case of emergency
     * @param $provider
     * @param $user
     */

    public function sendSOSEmail($email, $first_name, $last_name, $url) {
        $emails = new SendGrid\Email();
        $emails
            ->addTo($email)
            ->setBcc(SENDGRID_CC_EMAIL)
            ->setFrom(SENDGRID_FROM_EMAIL)
            ->setFromName(SENDGRID_FROM_NAME)
            ->setSubject('SOS Alert')
            ->setHtml('<h1>Save Our Soul Request</h1><br />
                <p>Hello,</p><br /><p>"'.$first_name." ".$last_name.'" is in Trouble and requires your assistance. Follow this <a href="'.$url.'">LINK</a> to view more details.</p>
                <p><strong>Thank You!</strong></p><br />')
        ;
        //$response = $this->sendgrid->send($email);
        $this->sendgrid->send($emails);
    }

}

?>
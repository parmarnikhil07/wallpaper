<?php

App::uses('CakeEmail', 'Network/Email');

class AppError {

    public static function handleError($code, $description, $file = null, $line = null, $context = null) {
        $body = 'Response: ';
        $body .= "Code: " . $code . "<br/>";
        $body .= "Description: " . $description . "<br/>";
        $body .= "File: " . $file . "<br/>";
        $body .= "Line: " . $line . "<br/>";
        if (!empty($context) && is_array($context)) {
            $body .= "Context: " . json_encode($context) . "<br/>";
        } else {
            if (!empty($context)) {
                $body .= "Context: " . $context . "<br/>";
            }
        }
        $body .= "URL: " . "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . "<br/>";
        try {
            $Email = new CakeEmail('default');
            $Email->to("nikhil.parmar@multidots.in");
            $Email->subject("Error mail from Wallpaper");
            $Email->replyTo("nikhil.parmar@multidots.in", "nikhil");
            $Email->from("nikhil.parmar@multidots.in", "nikhil");
            $Email->template("mail_temp");
            $Email->emailFormat('html');
            $Email->send($body);
        } catch (Exception $ex) {
            
        }
    }

}

?>

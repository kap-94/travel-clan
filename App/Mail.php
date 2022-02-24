<?php

namespace App;

// use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Class Mail
 * 
 * PHP @version 7.4
 */
class Mail
{
    /**
     * Send an email to the correspondant user
     * 
     * @param string $to Recipient
     * @param string $subject Subject
     * @param string $text Text-only content of the message
     * @param string $html HTML content of the message
     * 
     * @return mixed
     */
    public static function send($to, $subject, $text, $html)
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                       //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = getenv('MAIL_ACCOUNT');                   //SMTP username
            $mail->Password   = getenv('MAIL_PASSWORD');                  //SMTP password
            $mail->SMTPSecure = 'tls';                                  //Enable implicit TLS encryption
            $mail->Port       = 587;


            //Recipients
            $mail->setFrom(getenv('MAIL_ACCOUNT'));
            $mail->addAddress($to);

            //Content
            $mail->isHTML(true);                                        //Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $html;
            $mail->AltBody = $text;

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
}

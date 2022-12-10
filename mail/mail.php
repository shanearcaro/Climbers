<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Load Composer's autoloader
require_once '../vendor/autoload.php';


class Post
{
    //Create an instance; passing `true` enables exceptions
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        try {
            //Server settings
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = "rockclimbingit490@gmail.com";
            $this->mail->Password = "qvcwclmfykpmwdvg";
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $this->mail->Port = 465;
            $this->mail->setFrom('rockclimbingit490@gmail.com', 'Rock Climbing');
            $this->mail->isHTML(true);
        } catch (Exception $e) {
            echo "Mail could not be generated: {$this->mail->ErrorInfo}";
        }
    }

    /**
     * Set a reset password email to the specified email address
     * @param string $emailAddress the emailAddress to send the email to
     * @param string $username the username corresponding to the account
     * @param string $hash a unique hash that will act as the temporary
     * @return bool
     * Returns **true** if email was sent, **false** otherwise
     */
    function sendResetPassword(string $emailAddress, string $username, string $hash):bool
    {
        //Content
        try {
            $this->mail->addAddress($emailAddress, $username);
            $this->mail->Subject = 'Password Reset';
            $this->mail->Body = $this->generateResetPasswordEmail($username, $hash);
            $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $this->mail->send();
        } catch (Exception $e) {
            // Email failed to send
            return false;
        }

        // Email was sent
        return true;
    }

    private function generateResetPasswordEmail(string $username, string $hash): string
    {
        $date = new DateTime('+15 minutes');
        $date = $date->format('H:i');

        return '<h2 style="fontFamily: courier new;">Hello ' . $username . ',</h2>' .
        '<h3>Your temporary password is: <span style="color: red;">' . $hash . '</span></h3>' .
        '<h4>Please use this temporary password to log into your account. From there you will be prompted to update your password.</h4>' .
        '<h4>The temporary password will expire at <span style="cholor: red;">' . $date . '</span></h4>';
    }
}


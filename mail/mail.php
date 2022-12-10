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
            $this->mail->Password = "***REMOVED***";
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
            $this->mail->Body = $this->generateResetPasswordEmail($hash);
            $this->mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $this->mail->send();
        } catch (Exception $e) {
            // Email failed to send
            return false;
        }

        // Email was sent
        return true;
    }

    private function generateResetPasswordEmail(string $hash): string
    {

        // For testing
        return '
            <h1 style="color=red">Your temporary password is </h1><p>' . $hash . '</p>' .
            '<p>Please use this password to log in where you will be prompted
            to reset your password.</p>'
            ;

        // return "
        //     <html>
        //     <head><title>Title</title></head>
        //     <body>
        //         <a href='localhost:8050/" . uniqid() . "'>Google</a>
        //     </body>
        //     </html>
        // ";
    }
}


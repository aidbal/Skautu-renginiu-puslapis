<?php
require 'mailer/PHPMailerAutoload.php';

class Mailer {
    
    
    /**
     * sendWelcome - Sends a welcome message to the newly
     * registered user, also supplying the username and
     * password.
     */
    function sendWelcome($user, $email) {
        //Set default values
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com;smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = EMAIL_FROM_ADRESS;                 // SMTP username
        $mail->Password = EMAIL_FROM_PASS;                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                  // TCP port to connect to
        $mail->WordWrap = 70;                               //maximum line length in email
        
        $mail->setFrom(EMAIL_FROM_ADRESS, 'Skautatinklis');
        $mail->addAddress($email);     // Add a recipient
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Sėkmingai užsiregistravote į skautatinklį!';
        $mail->Body = 'Sveiki, <b>' . $user . '</b>!<br>Sėkmingai užsiregistravote tinklalapyje <a href="aidbal.stud.if.ktu.lt">aidbal.stud.if.ktu.lt - skautatinklis</a>. ' . 
                'Savo duomenis galite redaguoti prisijungę ir paspaudę ant savo vardo.<br><br>' . 
                'Nepamirškite užsiregistruoti į stovyklas!<br><br>' . 
                'Pagarbiai<br>'
                . 'Skautatinklio administracija.';
        return $mail->send();
    }

    /**
     * sendNewPass - Sends the newly generated password
     * to the user's email address that was specified at
     * sign-up.
     */
    function sendNewPass($user, $email, $pass) {
        //Set default values
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com;smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = EMAIL_FROM_ADRESS;                 // SMTP username
        $mail->Password = EMAIL_FROM_PASS;                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                  // TCP port to connect to
        $mail->WordWrap = 70;                               //maximum line length in email
        
        $mail->setFrom(EMAIL_FROM_ADRESS, 'Skautatinklis');
        $mail->addAddress($email);     // Add a recipient
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Sugeneruotas naujas slaptažodis.';
        $mail->Body = 'Sveiki, <b>' . $user . '</b>!<br>Jums buvo sugeneruotas naujas slaptažodis tinklalapyje <a href="aidbal.stud.if.ktu.lt">aidbal.stud.if.ktu.lt - skautatinklis</a>. ' . 
                'Slaptažodį galite pasikeisti prisijungę ir paspaudę ant savo vardo.<br><br>' . 
                'Jūsų prisijungimo vardas: <b>' . $user . '</b><br>' .
                'Naujas slaptažodis: <b>' . $pass . '</b><br><br>' .
                'Pagarbiai<br>'
                . 'Skautatinklio administracija.';
        return $mail->send();
    }
    
    /**
     * sendNewPass - Sends the newly generated password
     * to the user's email address that was specified at
     * sign-up.
     */
    function sendMessage($usersdata, $message, $eventtitle) {
        //Set default values
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com;smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = EMAIL_FROM_ADRESS;                 // SMTP username
        $mail->Password = EMAIL_FROM_PASS;                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                  // TCP port to connect to
        $mail->WordWrap = 70;                               //maximum line length in email
        
        $mail->setFrom(EMAIL_FROM_ADRESS, 'Skautatinklis');
        while($result = mysqli_fetch_array($usersdata)){
            $mail->addAddress($result['email']);     // Add a recipients
        }
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Gautas naujas laiškas dėl stovyklos "' . $eventtitle['title'] . '"';
        $formatted_message = nl2br($message);
        $mail->Body = $formatted_message;
        return $mail->send();
    }
    
    /**
     * sendNewPass - Sends the newly generated password
     * to the user's email address that was specified at
     * sign-up.
     */
    function sendContactForm($email, $heading, $message) {
        //Set default values
        $mail = new PHPMailer;
        $mail->CharSet = 'UTF-8';
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com;smtp.gmail.com';  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = EMAIL_FROM_ADRESS;               // SMTP username
        $mail->Password = EMAIL_FROM_PASS;                           // SMTP password
        $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 587;                                  // TCP port to connect to
        $mail->WordWrap = 70;                               //maximum line length in email
        
        $mail->AddReplyTo($email);
        $mail->setFrom(EMAIL_FROM_ADRESS, 'Skautatinklis');
        $mail->addAddress(EMAIL_FROM_ADRESS);     // Add a recipients
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Gauta nauja užklausos forma';
        
        $formatted_message = nl2br($message);
        $concated_message = "Sveiki, gauta nauja užklausos forma.<br>Siuntėjas: <b>$email</b><br><br>";
        $concated_message .= "Tema: <br><b>$heading</b><br><br>";
        $concated_message .= "Žinutės tekstas: <br><b>$formatted_message</b><br><br>";
        $concated_message .= "Galyte atsakyti paspaudę <b>atsakyti</b>.<br>";
        $concated_message .= "Pašto adresas bus įterptas automatiškai.";
        
        $mail->Body = $concated_message;
        return $mail->send();
    }

}

/* Initialize mailer object */
$mailer = new Mailer;
?>

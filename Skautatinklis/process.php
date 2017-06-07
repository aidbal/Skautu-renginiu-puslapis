<?php

include("include/session.php");

class Process {
    /* Class constructor */

    function Process() {
        global $session;
        /* User submitted login form */
        if (isset($_POST['sublogin'])) {
            $this->procLogin();
        }
        /* Admin submitted new user */ else if (isset($_POST['subjoin'])) {
            $this->procRegister();
        }
        /* User submitted forgot password form */ else if (isset($_POST['subforgot'])) {
            $this->procForgotPass();
        }
        /* User edited question */ else if (isset($_POST['subedit'])) {
            $this->procEditUser();
        }
        /* User edited question */ else if (isset($_POST['subeditpass'])) {
            $this->procEditUserPass();
        }
        /* User added new event */ else if (isset($_POST['newevent'])) {
            $this->procAddEvent();
        }
        /* User edited event */ else if (isset($_POST['editevent'])) {
            $this->procEditEvent();
        }
        /* User registered to event */ else if (isset($_POST['registertoevent'])) {
            $this->procRegisterToEvent();
        }
        /* User unregistered from event */ else if (isset($_POST['unregisterfromevent'])) {
            $this->procUnregisterFromEvent();
        }
        /* User sent message */ else if (isset($_POST['sendmessage'])) {
            $this->sendMessage();
        }
        /* User submitted contact form */ else if (isset($_POST['contactform'])) {
            $this->sendContactForm();
        }
        /**
         * The only other reason user should be directed here
         * is if he wants to logout, which means user is
         * logged in currently.
         */ else if ($session->logged_in) {
            $this->procLogout();
        }
        /**
         * Should not get here, which means user is viewing this page
         * by mistake and therefore is redirected.
         */ else {
            header("Location: index.php");
        }
    }

    /**
     * procLogin - Processes the user submitted login form, if errors
     * are found, the user is redirected to correct the information,
     * if not, the user is effectively logged in to the system.
     */
    function procLogin() {
        global $session, $form;
        /* Filter user inputs */
        $user = filter_input(INPUT_POST, 'user'); 
        $pass = filter_input(INPUT_POST, 'pass');
        
        /* Login attempt */
        $retval = $session->login($user, $pass, isset($_POST['remember']));

        /* Login successful */
        if ($retval) {
            $session->logged_in = 1;
            header("Location: " . $session->referrer);
        }
        /* Login failed */ else {
            $session->logged_in = null;
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }

    /**
     * procLogout - Simply attempts to log the user out of the system
     * given that there is no logout form to process.
     */
    function procLogout() {
        global $session;
        $retval = $session->logout();
        header("Location: index.php");
    }
    
    /**
     * procRegister - Processes the Admin submitted registration form,
     * if errors are found, the admin is redirected to correct the
     * information, if not, the admin typed user is effectively registered with
     * the system.
     */
    function procRegister() {
        global $session, $form;
        /* Filter user input */
        //$POST = filter_input(INPUT_POST); 
        $user = filter_input(INPUT_POST, 'user'); 
        $pass = filter_input(INPUT_POST, 'pass'); 
        $email = filter_input(INPUT_POST, 'email'); 
        $name = filter_input(INPUT_POST, 'name'); 
        $surname = filter_input(INPUT_POST, 'surname'); 
        $borndate = filter_input(INPUT_POST, 'borndate'); 
        $leader = filter_input(INPUT_POST, 'leader'); 
        $recaptcha = $_POST["g-recaptcha-response"];
        
        /* Convert username to all lowercase (by option) */
        if (ALL_LOWERCASE) {
            $user = strtolower($user);
        }
        
        if(empty($leader)){
            $leader = NULL;
        }
        
        /* Registration attempt */
        $retval = $session->register($user, $pass, $email, $name, $surname, $borndate, $leader, $recaptcha);

        /* Registration Successful */
        if ($retval == 0) {
            $_SESSION['reguname'] = $user;
            $_SESSION['regsuccess'] = true;
            $_SESSION['regemail'] = $email;
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else if ($retval == 1) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
        /* Registration attempt failed */ else if ($retval == 2) {
            $_SESSION['reguname'] = $user;
            $_SESSION['regsuccess'] = false;
            header("Location: " . $session->referrer);
        }
    }
    
    /**
     * procForgotPass - Validates the given username then if
     * everything is fine, a new password is generated and
     * emailed to the address the user gave on sign up.
     */
    function procForgotPass() {
        global $database, $session, $mailer, $form;
        /* Username error checking */
        $subuser = $_POST['user'];
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Neįvestas vartotojo vardas<br>");
        } else {
            /* Make sure username is in database */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 5 || strlen($subuser) > 30 ||
                    !eregi("^([0-9a-z])+$", $subuser) ||
                    (!$database->usernameTaken($subuser))) {
                $form->setError($field, "* Vartotojas neegzistuoja<br>");
            }
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
        }
        /* Generate new password and email it to user */ else {
            /* Generate new password */
            $newpass = $session->generateRandStr(8);

            /* Get email of user */
            $usrinf = $database->getUserInfo($subuser);
            $email = $usrinf['email'];

            /* Attempt to send the email with new password */
            if ($mailer->sendNewPass($subuser, $email, $newpass)) {
                /* Email sent, update database */
                $database->updateUserField($subuser, "password", md5($newpass));
                $_SESSION['forgotpass'] = true;
            }
            /* Email failure, do not change password */ else {
                $_SESSION['forgotpass'] = false;
            }
        }

        header("Location: " . $session->referrer);
    }

    /**
     * procRegister - Processes the Admin submitted registration form,
     * if errors are found, the admin is redirected to correct the
     * information, if not, the admin typed user is effectively registered with
     * the system.
     */
    function procEditUser() {
        global $session, $form;
        /* Filter user input */
        //$POST = filter_input(INPUT_POST); 
        $user = filter_input(INPUT_POST, 'user');
        $email = filter_input(INPUT_POST, 'email'); 
        $name = filter_input(INPUT_POST, 'name'); 
        $surname = filter_input(INPUT_POST, 'surname'); 
        $borndate = filter_input(INPUT_POST, 'borndate'); 
        $leader = filter_input(INPUT_POST, 'leader'); 
        
        if(empty($leader)){
            $leader = NULL;
        }
        
        /* Registration attempt */
        $retval = $session->editUser($user, $email, $name, $surname, $borndate, $leader);

        /* Registration Successful */
        if ($retval == true) {
            $_SESSION['subedit'] = true;
            header("Location: " . $session->referrer . "?editaccount");
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer . "?editaccount");
        }
    }
    
    /**
     * procRegister - Processes the Admin submitted registration form,
     * if errors are found, the admin is redirected to correct the
     * information, if not, the admin typed user is effectively registered with
     * the system.
     */
    function procEditUserPass() {
        global $session, $form;
        /* Filter user input */
        //$POST = filter_input(INPUT_POST); 
        $currpass = filter_input(INPUT_POST, 'currpass');
        $newpass = filter_input(INPUT_POST, 'newpass'); 
        $newpass2 = filter_input(INPUT_POST, 'newpass2'); 
        
        /* Registration attempt */
        $retval = $session->editUserPass($currpass, $newpass, $newpass2, $session->username);
        
        /* Registration Successful */
        if ($retval == true) {
            $_SESSION['subeditpass'] = true;
            header("Location: manopaskyra.php?editpass");
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: manopaskyra.php?editpass");
        }
    }
    
    /**
     * Attempts to add new camp and its relative data.
     * First it makes sure it is in right format,
     * then adds it.
     */
    function procAddEvent() {
        global $session, $form;
        /* Filter user input */
        $title = filter_input(INPUT_POST, 'title'); 
        $starred = 0;
        if (filter_input(INPUT_POST, 'starred') == 1){
            $starred = 1;
        }
        $date = filter_input(INPUT_POST, 'date'); 
        $duration = filter_input(INPUT_POST, 'duration'); 
        $whereabout = filter_input(INPUT_POST, 'whereabout'); 
        $price = filter_input(INPUT_POST, 'price'); 
        $capacity = filter_input(INPUT_POST, 'capacity'); 
        $leader = filter_input(INPUT_POST, 'leader'); 
        $phone_number = filter_input(INPUT_POST, 'phone_number'); 
        $short_description = filter_input(INPUT_POST, 'short_description'); 
        $description = filter_input(INPUT_POST, 'description');
        
        $retval = $session->addEvent($title, $date, $duration, $whereabout, $price, $capacity, $leader, $phone_number, $short_description, $description, $starred);

        /* Question add successful */
        if ($retval) {
            $_SESSION['newevent'] = true;
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }

    /**
     * Attempts to add new camp and its relative data.
     * First it makes sure it is in right format,
     * then adds it.
     */
    function procEditEvent() {
        global $session, $form;
        /* Filter user input */
        $id = filter_input(INPUT_POST, 'id'); 
        $title = filter_input(INPUT_POST, 'title'); 
        $starred = 0;
        if (filter_input(INPUT_POST, 'starred') == 1){
            $starred = 1;
        }
        $date = filter_input(INPUT_POST, 'date'); 
        $duration = filter_input(INPUT_POST, 'duration'); 
        $whereabout = filter_input(INPUT_POST, 'whereabout'); 
        $price = filter_input(INPUT_POST, 'price'); 
        $capacity = filter_input(INPUT_POST, 'capacity'); 
        $leader = filter_input(INPUT_POST, 'leader'); 
        $phone_number = filter_input(INPUT_POST, 'phone_number'); 
        $short_description = filter_input(INPUT_POST, 'short_description'); 
        $description = filter_input(INPUT_POST, 'description');
        
        $retval = $session->editEvent($id, $title, $date, $duration, $whereabout, $price, $capacity, $leader, $phone_number, $short_description, $description, $starred);

        /* Question add successful */
        if ($retval) {
            $_SESSION['editevent'] = true;
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }
    
    /**
     * Writes all exam test user input data to database. Also if question
     * is closed type, it evaluates it automatically, if 
     * question is open type, it changes test condition
     * to unvalidated.
     */
    function procRegisterToEvent() {
         global $session, $form, $database;
         
        $eventid = filter_input(INPUT_POST, 'eventid');
        
        $retval = $database->registerToEvent($eventid, $session->username);
         
        if ($retval) {
            $_SESSION['registertoevent'] = true;
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }
    
    /**
     * Writes all exam test user input data to database. Also if question
     * is closed type, it evaluates it automatically, if 
     * question is open type, it changes test condition
     * to unvalidated.
     */
    function procUnregisterFromEvent() {
         global $session, $form, $database;
         
        $eventid = filter_input(INPUT_POST, 'eventid');
        
        $retval = $database->unregisterFromEvent($eventid, $session->username);
         
        if ($retval) {
            $_SESSION['unregisterfromevent'] = true;
            header("Location: " . $session->referrer);
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer);
        }
    }

    /**
     * Writes all exam test user input data to database. Also if question
     * is closed type, it evaluates it automatically, if 
     * question is open type, it changes test condition
     * to unvalidated.
     */
    function sendMessage() {
         global $session, $form;
         
        $eventid = filter_input(INPUT_POST, 'eventid');
        $message = filter_input(INPUT_POST, 'message');
        $retval = $session->sendMessage($eventid, $message);
        
        if ($retval) {
            $_SESSION['sendmessage'] = true;
            header("Location: " . $session->referrer . "?id=$eventid");
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer . "?id=$eventid");
        }
    }
    /**
     * Writes all exam test user input data to database. Also if question
     * is closed type, it evaluates it automatically, if 
     * question is open type, it changes test condition
     * to unvalidated.
     */
    function sendContactForm() {
         global $session, $form;
         
        $email = filter_input(INPUT_POST, 'email');
        $heading = filter_input(INPUT_POST, 'heading');
        $message = filter_input(INPUT_POST, 'message');
        $retval = $session->sendContactForm($email, $heading, $message);
        if ($retval) {
            $_SESSION['contactform'] = true;
            header("Location: " . $session->referrer . "#kontaktai");
        }
        /* Error found with form */ else {
            $_SESSION['value_array'] = $_POST;
            $_SESSION['error_array'] = $form->getErrorArray();
            header("Location: " . $session->referrer . "#kontaktai");
        }
    }
}

/* Initialize process */
$process = new Process;
?>

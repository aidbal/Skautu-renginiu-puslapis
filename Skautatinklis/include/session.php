<?php

include("database.php");
include("mailer.php");
include("form.php");
include("recaptchalib.php");

class Session {

    var $username;     //Username given on sign-up
    var $userid;       //Random value generated on current login
    var $userlevel;    //The level to which the user pertains
    var $time;         //Time user was last active (page loaded)
    var $logged_in;    //True if user is logged in, false otherwise
    var $userinfo = array();  //The array holding all user info
    var $url;          //The page url current being viewed
    var $referrer;     //Last recorded site page viewed

    /**
     * Note: referrer should really only be considered the actual
     * page referrer in process.php, any other time it may be
     * inaccurate.
     */
    /* Class constructor */

    function Session() {
        $this->time = time();
        $this->startSession();
    }

    /**
     * startSession - Performs all the actions necessary to 
     * initialize this session object. Tries to determine if the
     * the user has logged in already, and sets the variables 
     * accordingly. Also takes advantage of this page load to
     * update the active visitors tables.
     */
    function startSession() {
        global $database;  //The database connection
        session_start();   //Tell PHP to start the session

        /* Determine if user is logged in */
        $this->logged_in = $this->checkLogin();

        /**
         * Set guest value to users not logged in, and update
         * active guests table accordingly.
         */
        if (!$this->logged_in) {
            $this->username = $_SESSION['username'] = USER_NAME;
            $this->userlevel = USER_LEVEL;
            $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);
        }
        /* Update users last active timestamp */ else {
            $database->addActiveUser($this->username, $this->time);
        }

        /* Remove inactive visitors from database */
        $database->removeInactiveUsers();
        $database->removeInactiveGuests();

        /* Set referrer page */
        if (isset($_SESSION['url'])) {
            $this->referrer = $_SESSION['url'];
        } else {
            $this->referrer = "/";
        }

        /* Set current url */
        $this->url = $_SESSION['url'] = $_SERVER['PHP_SELF'];
    }

    /**
     * checkLogin - Checks if the user has already previously
     * logged in, and a session with the user has already been
     * established. Also checks to see if user has been remembered.
     * If so, the database is queried to make sure of the user's 
     * authenticity. Returns true if the user has logged in.
     */
    function checkLogin() {
        global $database;  //The database connection
        /* Check if user has been remembered */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            $this->username = $_SESSION['username'] = $_COOKIE['cookname'];
            $this->userid = $_SESSION['userid'] = $_COOKIE['cookid'];
        }

        /* Username and userid have been set and not guest */
        if (isset($_SESSION['username']) && isset($_SESSION['userid']) &&
                $_SESSION['username'] != USER_NAME) {
            /* Confirm that username and userid are valid */
            if ($database->confirmUserID($_SESSION['username'], $_SESSION['userid']) != 0) {
                /* Variables are incorrect, user not logged in */
                unset($_SESSION['username']);
                unset($_SESSION['userid']);
                return false;
            }

            /* User is logged in, set class variables */
            $this->userinfo = $database->getUserInfo($_SESSION['username']);
            $this->username = $this->userinfo['username'];
            $this->userid = $this->userinfo['userid'];
            $this->userlevel = $this->userinfo['userlevel'];
            return true;
        }
        /* User not logged in */ else {
            return false;
        }
    }

    /**
     * login - The user has submitted his username and password
     * through the login form, this function checks the authenticity
     * of that information in the database and creates the session.
     * Effectively logging in the user if all goes well.
     */
    function login($subuser, $subpass, $subremember) {
        global $database, $form;  //The database and form object

        /* Username error checking */
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Neivestas vartotojo vardas");
        } else {
            /* Check if username is not alphanumeric */
            if (!preg_match('/'."^([0-9a-z])*$".'/i', $subuser)) {
                $form->setError($field, "* Vartotojo vardas gali buti sudarytas
                    <br>&nbsp;&nbsp;tik iš raidžiu ir skaiciu");
            }
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "* Neivestas slaptažodis");
        }
        //set error identifier
        $form->setError("loginAttempt", 1);
        /* Return if form errors exist */
        if ($form->num_errors > 1) {
            return false;
        }

        /* Checks that username is in database and password is correct */
        $subuser = stripslashes($subuser);
        $result = $database->confirmUserPass($subuser, md5($subpass));

        /* Check error codes */
        if ($result == 1) {
            $field = "user";
            $form->setError($field, "* Tokio vartotojo nera");
        } else if ($result == 2) {
            $field = "pass";
            $form->setError($field, "* Neteisingas slaptažodis");
        }
        
        /* Return if form errors exist */
        if ($form->num_errors > 1) {
            return false;
        }

        /* Username and password correct, register session variables */
        $this->userinfo = $database->getUserInfo($subuser);
        $this->username = $_SESSION['username'] = $this->userinfo['username'];
        $this->userid = $_SESSION['userid'] = $this->generateRandID();
        $this->userlevel = $this->userinfo['userlevel'];

        /* Insert userid into database and update active users table */
        $database->updateUserField($this->username, "userid", $this->userid);
        $database->addActiveUser($this->username, $this->time);
        $database->removeActiveGuest($_SERVER['REMOTE_ADDR']);

        /**
         * This is the cool part: the user has requested that we remember that
         * he's logged in, so we set two cookies. One to hold his username,
         * and one to hold his random value userid. It expires by the time
         * specified in constants.php. Now, next time he comes to our site, we will
         * log him in automatically, but only if he didn't log out before he left.
         */
        if ($subremember) {
            setcookie("cookname", $this->username, time() + COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", $this->userid, time() + COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Login completed successfully */
        return true;
    }
    
    /**
     * register - Gets called when the user has just submitted the
     * registration form. Determines if there were any errors with
     * the entry fields, if so, it records the errors and returns
     * 1. If no errors were found, it registers the new user and
     * returns 0. Returns 2 if registration failed.
     */
    function register($subuser, $subpass, $subemail, $name, $surname, $borndate, $leader, $recaptcha) {
        global $database, $form, $mailer;  //The database, form and mailer object
        
        // empty response
        $response = null;

        // check secret key
        $reCaptcha = new ReCaptcha(SECRET);
        
        /* Username error checking */
        $field = "user";  //Use field name for username
        if (!$subuser || strlen($subuser = trim($subuser)) == 0) {
            $form->setError($field, "* Vartotojas neįvestas");
        } else {
            /* Spruce up username, check length */
            $subuser = stripslashes($subuser);
            if (strlen($subuser) < 4) {
                $form->setError($field, "* Vartotojo vardas turi mažiau kaip 4 simbolius");
            } else if (strlen($subuser) > 30) {
                $form->setError($field, "* Vartotojo vardas virš 30 simbolių");
            }
            /* Check if username is not alphanumeric */ else if (!preg_match('/'."^([0-9a-z])+$".'/i', $subuser)) {
                $form->setError($field, "* Vartotojo vardas gali būti sudarytas
                    <br>&nbsp;&nbsp;tik iš raidžių ir skaičių");
            }
            /* Check if username is reserved */ else if (strcasecmp($subuser, USER_NAME) == 0) {
                $form->setError($field, "* Rezervuotas vartotojo vardas");
            }
            /* Check if username is already in use */ else if ($database->usernameTaken($subuser)) {
                $form->setError($field, "* Toks vartotojo vardas jau yra");
            }
            /* Check if username is banned */ else if ($database->usernameBanned($subuser)) {
                $form->setError($field, "* Vartotojas užblokuotas");
            }
        }

        /* Password error checking */
        $field = "pass";  //Use field name for password
        if (!$subpass) {
            $form->setError($field, "* Neįvestas slaptažodis");
        } else {
            /* Spruce up password and check length */
            $subpass = stripslashes($subpass);
            if (strlen($subpass) < 4) {
                $form->setError($field, "* Ne mažiau kaip 4 simboliai");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }
        
        /* Email error checking */
        $field = "email";  //Use field name for email
        if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
            $form->setError($field, "* Neįvestas e-pašto adresas");
        } else {
            /* Check if valid email address */
            $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                    . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                    . "\.([a-z]{2,}){1}$";
            if (!preg_match('/'.$regex.'/i', $subemail)) {
                $form->setError($field, "* Klaidingas e-pašto adresas");
            }
        }
        
        /* date error checking */
        $field = "borndate";  //Use field name for email
        if (!$subemail || strlen($borndate = trim($borndate)) == 0) {
            $form->setError($field, "* Neįvesta gimimo data");
        } else {
            /* Check if valid email address */
            $regex = "^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$";
            if (!preg_match('/'.$regex.'/i', $borndate)) {
                $form->setError($field, "* Klaidingai įvesta data");
            }
        }
        
        $field = "recaptcha";
        // if submitted check response
        if ($recaptcha) {
            $response = $reCaptcha->verifyResponse(
                $_SERVER["REMOTE_ADDR"],
                $recaptcha
            );
            if ($response != null && $response->success) {
                //sekmingas recaptcha
            } else {
               $form->setError($field, "* Klaida įvedant recaptcha");
            }
        } else {
            $form->setError($field, "* Nepažymėtas recaptcha");
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return 1;  //Errors with form
        }
        /* No errors, add the new account to the */ else {
            if ($database->addNewUser($subuser, md5($subpass), $subemail, $name, $surname, $borndate, $leader)) {
                if (EMAIL_WELCOME) {
                    $mailer->sendWelcome($subuser, $subemail);
                    if(!$mailer) return 2;
                }
                return 0;  //New user added succesfully
            } else {
                return 2;  //Registration attempt failed
            }
        }
    }
    
    /**
     * editUser - Gets called when the user has just submitted the
     * user edit form. Determines if there were any errors with
     * the entry fields, if so, it records the errors and returns
     * false. If no errors were found, it edits new user details and
     * returns true. 
     */
    function editUser($subuser, $subemail, $name, $surname, $borndate, $leader) {
        global $database, $form;  //The database, form and mailer object
        
        /* Email error checking */
        $field = "email";  //Use field name for email
        if (!$subemail || strlen($subemail = trim($subemail)) == 0) {
            $form->setError($field, "* Neįvestas e-pašto adresas");
        } else {
            /* Check if valid email address */
            $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                    . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                    . "\.([a-z]{2,}){1}$";
            if (!preg_match('/'.$regex.'/i', $subemail)) {
                $form->setError($field, "* Klaidingas e-pašto adresas");
            }
        }
        
        /* date error checking */
        $field = "borndate";  //Use field name for email
        if (!$subemail || strlen($borndate = trim($borndate)) == 0) {
            $form->setError($field, "* Neįvesta gimimo data");
        } else {
            /* Check if valid email address */
            $regex = "^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$";
            if (!preg_match('/'.$regex.'/i', $borndate)) {
                $form->setError($field, "* Klaidingai įvesta data");
            }
        }

        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return false;  //Errors with form
        }
        /* Edit user since there were no errors */
        $success = $database->editUser($subuser, $subemail, $name, $surname, $borndate, $leader);
        if(!$success) {
            $field = "user";
            $form->setError($field, "* Išsaugoti duomenų nepavyko, bandykite kitą kartą.");
            /* Something went wrong! */
            return false;
        }
        
        /* Success! */
        return true;
    }

    /**
     * logout - Gets called when the user wants to be logged out of the
     * website. It deletes any cookies that were stored on the users
     * computer as a result of him wanting to be remembered, and also
     * unsets session variables and demotes his user level to guest.
     */
    function logout() {
        global $database;  //The database connection
        /**
         * Delete cookies - the time must be in the past,
         * so just negate what you added when creating the
         * cookie.
         */
        if (isset($_COOKIE['cookname']) && isset($_COOKIE['cookid'])) {
            setcookie("cookname", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
            setcookie("cookid", "", time() - COOKIE_EXPIRE, COOKIE_PATH);
        }

        /* Unset PHP session variables */
        unset($_SESSION['username']);
        unset($_SESSION['userid']);

        /* Reflect fact that user has logged out */
        $this->logged_in = false;

        /**
         * Remove from active users table and add to
         * active guests tables.
         */
        $database->removeActiveUser($this->username);
        $database->addActiveGuest($_SERVER['REMOTE_ADDR'], $this->time);

        /* Set user level to guest */
        $this->username = GUEST_NAME;
        $this->userlevel = GUEST_LEVEL;
    }
    
    
    /**
     * addEvent - Attempts to add new camp event and its relative data.
     * Returns any input errors if found.
     */
    function addEvent($title, $date, $duration, $whereabout, $price, $capacity, $leader, $phone_number, $short_description, $description, $starred) {
        global $database, $form;
        
        /* Username error checking */
        $field = "title";  //Use field name for username
        if (strlen($title) > 70) {
            $form->setError($field, "* Pavadinimas per ilgas (" . strlen($title) . "/70)");
        }
        
        /* Date error checking */
        $field = "date";  //Use field name for email
        /* Check if valid email address */
        $regex = "^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$";
        if (!preg_match('/'.$regex.'/i', $date)) {
            $form->setError($field, "* Klaidingai įvesta data. Datos formatas(YYYY-MM-DD).");
        }
        
        /* Duration error checking */
        $field = "duration";  //Use field name for username
        $regex = "^([1-9]|[1-9][0-9])$";
        if (strlen($duration) > 2) {
            $form->setError($field, "* Trukmė per ilga. (" . strlen($duration) . "/2)");
        } else if (!preg_match('/'.$regex.'/i', $duration)) {
            $form->setError($field, "* Klaidingai įvesta trukmė. Gali būti tik skaičiai.");
        }
        
        /* Price error checking */
        $field = "price";  //Use field name for username
        if (strlen($price) > 4) {
            $form->setError($field, "* Kaina per ilga. (" . strlen($price) . "/4)");
        }
        
        /* Capacity error checking */
        $field = "capacity";  //Use field name for username
        $regex = "^([1-9]|[0-9]{2}|[0-9]{3}|[0-9]{4}|[0-9]{5}|[0-9]{6})$";
        if (strlen($capacity) > 6) {
            $form->setError($field, "* Žmonių kiekis per ilgas. (" . strlen($capacity) . "/6)");
        } else if (!preg_match('/'.$regex.'/i', $capacity)) {
            $form->setError($field, "* Klaidingai įvestas vietų kiekis. Gali būti tik skaičiai.");
        }
        
        /* Leader error checking */
        $field = "leader";  //Use field name for username
        if (strlen($leader) > 60) {
            $form->setError($field, "* Atsakingas asmuo viršyja ilgį. (" . strlen($leader) . "/60)");
        }
        
        /* Phone number error checking */
        $field = "phone_number";  //Use field name for username
        if (strlen($phone_number) > 15) {
            $form->setError($field, "* Telefono numeris viršyja ilgį. (" . strlen($phone_number) . "/15)");
        }
        
        /* Short description error checking */
        $field = "short_description";  //Use field name for username
        if (strlen($short_description) > 140) {
            $form->setError($field, "* Trumpas aprašymas viršyja ilgį. (" . strlen($short_description) . "/140)");
        }
        
        /* Description error checking */
        $field = "description";  //Use field name for username
        if (strlen($description) > 2000) {
            $form->setError($field, "* Aprašymas viršyja ilgį. (" . strlen($description) . "/2000)");
        }
        
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return false;  //Errors with form
        }
        /* Add camp event since there were no errors */
        $database->addCampEvent($title, $date, $duration, $whereabout, $price, $capacity, $leader, $phone_number, $short_description, $description, $starred);
        /* Success! */
        return true;
    }
    
    /**
     * editEvent - Attempts to edit camp event and its relative data.
     * Returns any input errors if found.
     */
    function editEvent($id, $title, $date, $duration, $whereabout, $price, $capacity, $leader, $phone_number, $short_description, $description, $starred) {
        global $database, $form;
        
        /* Username error checking */
        $field = "title";  //Use field name for username
        if (strlen($title) > 70) {
            $form->setError($field, "* Pavadinimas per ilgas (" . strlen($title) . "/70)");
        }
        
        /* Date error checking */
        $field = "date";  //Use field name for email
        /* Check if valid email address */
        $regex = "^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$";
        if (!preg_match('/'.$regex.'/i', $date)) {
            $form->setError($field, "* Klaidingai įvesta data. Datos formatas(YYYY-MM-DD).");
        }
        
        /* Duration error checking */
        $field = "duration";  //Use field name for username
        $regex = "^([1-9]|[1-9][0-9])$";
        if (strlen($duration) > 2) {
            $form->setError($field, "* Trukmė per ilga. (" . strlen($duration) . "/2)");
        } else if (!preg_match('/'.$regex.'/i', $duration)) {
            $form->setError($field, "* Klaidingai įvesta trukmė. Gali būti tik skaičiai.");
        }
        
        /* Price error checking */
        $field = "price";  //Use field name for username
        if (strlen($price) > 4) {
            $form->setError($field, "* Kaina per ilga. (" . strlen($price) . "/4)");
        }
        
        /* Capacity error checking */
        $field = "capacity";  //Use field name for username
        $regex = "^([1-9]|[0-9]{2}|[0-9]{3}|[0-9]{4}|[0-9]{5}|[0-9]{6})$";
        if (strlen($capacity) > 6) {
            $form->setError($field, "* Žmonių kiekis per ilgas. (" . strlen($capacity) . "/6)");
        } else if (!preg_match('/'.$regex.'/i', $capacity)) {
            $form->setError($field, "* Klaidingai įvestas vietų kiekis. Gali būti tik skaičiai.");
        }
        
        /* Leader error checking */
        $field = "leader";  //Use field name for username
        if (strlen($leader) > 60) {
            $form->setError($field, "* Atsakingas asmuo viršyja ilgį. (" . strlen($leader) . "/60)");
        }
        
        /* Phone number error checking */
        $field = "phone_number";  //Use field name for username
        if (strlen($phone_number) > 15) {
            $form->setError($field, "* Telefono numeris viršyja ilgį. (" . strlen($phone_number) . "/15)");
        }
        
        /* Short description error checking */
        $field = "short_description";  //Use field name for username
        if (strlen($short_description) > 140) {
            $form->setError($field, "* Trumpas aprašymas viršyja ilgį. (" . strlen($short_description) . "/140)");
        }
        
        /* Description error checking */
        $field = "description";  //Use field name for username
        if (strlen($description) > 2000) {
            $form->setError($field, "* Aprašymas viršyja ilgį. (" . strlen($description) . "/2000)");
        }
        
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return false;  //Errors with form
        }
        /* Edit camp event since there were no errors */
        $database->editCampEvent($id, $title, $date, $duration, $whereabout, $price, $capacity, $leader, $phone_number, $short_description, $description, $starred);
        /* Success! */
        return true;
    }
    
    
    /**
     * sendMessege - Attempts to send messege to registered users
     * to camp. Returns any input errors if found.
     */
    function sendMessage($eventid, $message) {
        global $database, $form, $mailer;
        
        /* Username error checking */
        $field = "message";  //Use field name for username
        if (strlen($message) < 50) {
            $form->setError($field, "* Žinutės laukas per trumpas (" . strlen($message) . "<50)");
        }
        if (strlen($message) > 3000) {
            $form->setError($field, "* Žinutės laukas per ilgas (" . strlen($message) . "/3000)");
        }
        
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return false;  //Errors with form
        }
        /* Get users and their emails since there were no errors */
        $usersdata = $database->getRegisteredUsersToEvent($eventid);
        /* Gets event title*/
        $eventtitle = $database->getEventTitle($eventid);
        $mailer->sendMessage($usersdata, $message, $eventtitle);
        //$mailer->sendWelcome('aidas', 'balcaitis.aidas@gmail.com');
        
        if(!$mailer) {
            $form->setError($field, "* Klaida siunčiant laišką. Bandykite dar kartą.");
            return false;  //Errors with form
        }
        
        /* Success! */
        return true;
    }
    /**
     * sendContactForm - Attempts to send entered contact form to website's
     * info email. Returns any input errors if found.
     */
    function sendContactForm($email, $heading, $message) {
        global $database, $form, $mailer;
        
        /* Message error checking */
        $field = "message";  //Use field name for username
        if (strlen($message) < 20) {
            $form->setError($field, "* Žinutės laukas per trumpas (" . strlen($message) . "<50)");
        }
        if (strlen($message) > 3000) {
            $form->setError($field, "* Žinutės laukas per ilgas (" . strlen($message) . "/3000)");
        }
        
        /* Heading error checking */
        $field = "heading";  //Use field name for username
        if (strlen($heading) < 4) {
            $form->setError($field, "* Tema per trumpa (" . strlen($heading) . "<4)");
        }
        if (strlen($heading) > 100) {
            $form->setError($field, "* Tema per ilga (" . strlen($heading) . "/100)");
        }
        
        /* Email error checking */
        $field = "email";  //Use field name for email
        if (!$email || strlen($email = trim($email)) == 0) {
            $form->setError($field, "* Neįvestas e-pašto adresas");
        } else {
            /* Check if valid email address */
            $regex = "^[_+a-z0-9-]+(\.[_+a-z0-9-]+)*"
                    . "@[a-z0-9-]+(\.[a-z0-9-]{1,})*"
                    . "\.([a-z]{2,}){1}$";
            if (!preg_match('/'.$regex.'/i', $email)) {
                $form->setError($field, "* Klaidingas e-pašto adresas");
            }
        }
        
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return false;  //Errors with form
        }
        
        /* Sends form */
        $mailer->sendContactForm($email, $heading, $message);
        
        if(!$mailer) {
            $form->setError($field, "* Klaida siunčiant laišką. Bandykite dar kartą.");
            return false;  //Errors with form
        }
        
        /* Success! */
        return true;
    }
    
    /**
     * editUserPass - Attempts to edit user password. Also checks if new 
     * password matches with repeated new password.
     * Returns any input errors if found.
     */
    function editUserPass($currpass, $newpass, $newpass2, $userid) {
        global $database, $form;
        
        /* Current password error checking */
        $field = "currpass";  //Use field name for password
        if (!$currpass) {
            $form->setError($field, "* Neįvestas slaptažodis");
        } else {
            /* Spruce up password and check length */
            $currpass = stripslashes($currpass);
            if (strlen($currpass) < 4) {
                $form->setError($field, "* Ne mažiau kaip 4 simboliai");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }
        
        /* New password error checking */
        $field = "newpass";  //Use field name for password
        if (!$newpass) {
            $form->setError($field, "* Neįvestas slaptažodis");
        } else {
            /* Spruce up password and check length */
            $newpass = stripslashes($newpass);
            if (strlen($newpass) < 4) {
                $form->setError($field, "* Ne mažiau kaip 4 simboliai");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }
        
        /* New password error checking */
        $field = "newpass2";  //Use field name for password
        if (!$newpass2) {
            $form->setError($field, "* Neįvestas slaptažodis");
        } else {
            /* Spruce up password and check length */
            $newpass2 = stripslashes($newpass2);
            if (strlen($newpass2) < 4) {
                $form->setError($field, "* Ne mažiau kaip 4 simboliai");
            }
            /**
             * Note: I trimmed the password only after I checked the length
             * because if you fill the password field up with spaces
             * it looks like a lot more characters than 4, so it looks
             * kind of stupid to report "password too short".
             */
        }
        
        /* Errors exist, have user correct them */
        if ($form->num_errors > 0) {
            return false;  //Errors with form
        }
        
        // If passwords don't match, return false
        if(strcmp($newpass, $newpass2) != 0){
            $field = "newpass";  //Use field name for password
            $form->setError($field, "* Slaptažodžiai nesutampa.");
            $field = "newpass2";  //Use field name for password
            $form->setError($field, "* Slaptažodžiai nesutampa.");
            return false;
        }
        
        $currpassmd5 = md5($currpass); // Entered current user pass
        $currUserpass = $database->getUserCurrPass($userid); // Current user pass from databse
        
        // If password don't match, return false
        if(strcmp($currpassmd5, $currUserpass['password']) != 0){
            $field = "currpass";  //Use field name for password
            $form->setError($field, "* Slaptažodis nesutampa su dabartiniu slaptažodžiu.");
            return false;
        }
        
        $newUserPass = md5($newpass);
        $database->updateUserField($userid, 'password', $newUserPass);
        
        /* Success! */
        return true;
    }
    
    /**
     * isAdmin - Returns true if currently logged in user is
     * an administrator, false otherwise.
     */
    function isAdmin() {
        return ($this->userlevel == ADMIN_LEVEL ||
                $this->username == ADMIN_NAME);
    }
    
    /**
     * isTeacher - Returns true if currently logged in user is
     * a teacher, false otherwise.
     */
    function isModerator() {
        return ($this->userlevel == MODERATOR_LEVEL);
    }
    
    /**
     * isStudent - Returns true if currently logged in user is
     * a student, false otherwise.
     */
    function isUser() {
        return ($this->userlevel == USER_LEVEL);
    }

    /**
     * generateRandID - Generates a string made up of randomized
     * letters (lower and upper case) and digits and returns
     * the md5 hash of it to be used as a userid.
     */
    function generateRandID() {
        return md5($this->generateRandStr(16));
    }

    /**
     * generateRandStr - Generates a string made up of randomized
     * letters (lower and upper case) and digits, the length
     * is a specified parameter.
     */
    function generateRandStr($length) {
        $randstr = "";
        for ($i = 0; $i < $length; $i++) {
            $randnum = mt_rand(0, 61);
            if ($randnum < 10) {
                $randstr .= chr($randnum + 48);
            } else if ($randnum < 36) {
                $randstr .= chr($randnum + 55);
            } else {
                $randstr .= chr($randnum + 61);
            }
        }
        return $randstr;
    }

}

/**
 * Initialize session object - This must be initialized before
 * the form object because the form uses session variables,
 * which cannot be accessed unless the session has started.
 */
$session = new Session;

/* Initialize form object */
$form = new Form;
?>
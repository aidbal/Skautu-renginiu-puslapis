<?php

include("constants.php");

class MySQLDB {

    var $connection;         //The MySQL database connection
    var $num_active_users;   //Number of active users viewing site
    var $num_active_guests;  //Number of active guests viewing site
    var $num_members;        //Number of signed-up users

    /* Note: call getNumMembers() to access $num_members! */

    /* Class constructor */

    function MySQLDB() {
        /* Make connection to database */
        $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME)
                or die(mysql_error() . '<br><h1>Faile include/constants.php suveskite savo MySQLDB duomenis.</h1>');
        mysqli_set_charset($this->connection,"utf8");
        /**
         * Only query database to find out number of members
         * when getNumMembers() is called for the first time,
         * until then, default value set.
         */
        $this->num_members = -1;

        if (TRACK_VISITORS) {
            /* Calculate number of users at site */
            $this->calcNumActiveUsers();

            /* Calculate number of guests at site */
            $this->calcNumActiveGuests();
        }
    }

    /**
     * confirmUserPass - Checks whether or not the given
     * username is in the database, if so it checks if the
     * given password is the same password in the database
     * for that user. If the user doesn't exist or if the
     * passwords don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    function confirmUserPass($username, $password) {
        /* Add slashes if necessary (for query) */
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }

        /* Verify that user is in database */
        $q = "SELECT password FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return 1; //Indicates username failure
        }

        /* Retrieve password from result, strip slashes */
        $dbarray = mysqli_fetch_array($result);
        $dbarray['password'] = stripslashes($dbarray['password']);
        $password = stripslashes($password);

        /* Validate that password is correct */
        if ($password === $dbarray['password']) {
            return 0; //Success! Username and password confirmed
        } else {
            return 2; //Indicates password failure
        }
    }

    /**
     * confirmUserID - Checks whether or not the given
     * username is in the database, if so it checks if the
     * given userid is the same userid in the database
     * for that user. If the user doesn't exist or if the
     * userids don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    function confirmUserID($username, $userid) {
        /* Add slashes if necessary (for query) */
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }

        /* Verify that user is in database */
        $q = "SELECT userid FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return 1; //Indicates username failure
        }

        /* Retrieve userid from result, strip slashes */
        $dbarray = mysqli_fetch_array($result);
        $dbarray['userid'] = stripslashes($dbarray['userid']);
        $userid = stripslashes($userid);

        /* Validate that userid is correct */
        if ($userid == $dbarray['userid']) {
            return 0; //Success! Username and userid confirmed
        } else {
            return 2; //Indicates userid invalid
        }
    }
    
        /**
     * usernameTaken - Returns true if the username has
     * been taken by another user, false otherwise.
     */
    function usernameTaken($username) {
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }
        $q = "SELECT username FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        return (mysqli_num_rows($result) > 0);
    }
    
    /**
     * addNewUser - Inserts the given (username, password, email)
     * info into the database. Appropriate user level is set.
     * Returns true on success, false otherwise.
     */
    function addNewUser($username, $password, $email, $name, $surname, $borndate, $leader) {
        $time = time();
        /* If admin sign up, give admin user level */
        if (strcasecmp($username, ADMIN_NAME) == 0) {
            $ulevel = ADMIN_LEVEL;
        }else {
            $ulevel = USER_LEVEL;
        }
        $q = "INSERT INTO " . TBL_USERS . "(`username`, `password`, `userlevel`, `userid`, `timestamp`, `email`, `name`, `surname`, `born_date`, `leader`) " . 
                "VALUES ('$username', '$password', $ulevel, '0', $time, '$email', '$name', '$surname', '$borndate', '$leader')";
        return mysqli_query($this->connection, $q);
    }
    
     /**
     * updateUserField - Updates a field, specified by the field
     * parameter, in the user's row of the database.
     */
    function updateUserField($username, $field, $value) {
        $q = "UPDATE " . TBL_USERS . " SET " . $field . " = '$value' WHERE username = '$username'";
        return mysqli_query($this->connection, $q);
    }
    
    /**
     * usernameBanned - Returns true if the username has
     * been banned by the administrator.
     */
    function usernameBanned($username) {
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }
        $q = "SELECT username FROM " . TBL_BANNED_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        return (mysqli_num_rows($result) > 0);
    }
    
        /**
     * getUserInfo - Returns the result array from a mysql
     * query asking for all information stored regarding
     * the given username. If query fails, NULL is returned.
     */
    function getUserInfo($username) {
        $q = "SELECT * FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }

    /**
     * getNumMembers - Returns the number of signed-up users
     * of the website, banned members not included. The first
     * time the function is called on page load, the database
     * is queried, on subsequent calls, the stored result
     * is returned. This is to improve efficiency, effectively
     * not querying the database when no call is made.
     */
    function getNumMembers() {
        if ($this->num_members < 0) {
            $q = "SELECT * FROM " . TBL_USERS;
            $result = mysqli_query($this->connection, $q);
            $this->num_members = mysqli_num_rows($result);
        }
        return $this->num_members;
    }

    /**
     * calcNumActiveUsers - Finds out how many active users
     * are viewing site and sets class variable accordingly.
     */
    function calcNumActiveUsers() {
        /* Calculate number of users at site */
        $q = "SELECT * FROM " . TBL_ACTIVE_USERS;
        $result = mysqli_query($this->connection, $q);
        $this->num_active_users = mysqli_num_rows($result);
    }

    /**
     * calcNumActiveGuests - Finds out how many active guests
     * are viewing site and sets class variable accordingly.
     */
    function calcNumActiveGuests() {
        /* Calculate number of guests at site */
        $q = "SELECT * FROM " . TBL_ACTIVE_GUESTS;
        $result = mysqli_query($this->connection, $q);
        $this->num_active_guests = mysqli_num_rows($result);
    }

    /**
     * addActiveUser - Updates username's last active timestamp
     * in the database, and also adds him to the table of
     * active users, or updates timestamp if already there.
     */
    function addActiveUser($username, $time) {
        $q = "UPDATE " . TBL_USERS . " SET timestamp = '$time' WHERE username = '$username'";
        mysqli_query($this->connection, $q);

        if (!TRACK_VISITORS)
            return;
        $q = "REPLACE INTO " . TBL_ACTIVE_USERS . " VALUES ('$username', '$time')";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveUsers();
    }

    /* addActiveGuest - Adds guest to active guests table */

    function addActiveGuest($ip, $time) {
        if (!TRACK_VISITORS)
            return;
        $q = "REPLACE INTO " . TBL_ACTIVE_GUESTS . " VALUES ('$ip', '$time')";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveGuests();
    }

    /* These functions are self explanatory, no need for comments */

    /* removeActiveUser */

    function removeActiveUser($username) {
        if (!TRACK_VISITORS)
            return;
        $q = "DELETE FROM " . TBL_ACTIVE_USERS . " WHERE username = '$username'";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveUsers();
    }

    /* removeActiveGuest */

    function removeActiveGuest($ip) {
        if (!TRACK_VISITORS)
            return;
        $q = "DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE ip = '$ip'";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveGuests();
    }

    /* removeInactiveUsers */

    function removeInactiveUsers() {
        if (!TRACK_VISITORS)
            return;
        $timeout = time() - USER_TIMEOUT * 60;
        $q = "DELETE FROM " . TBL_ACTIVE_USERS . " WHERE timestamp < $timeout";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveUsers();
    }

    /* removeInactiveGuests */

    function removeInactiveGuests() {
        if (!TRACK_VISITORS)
            return;
        $timeout = time() - GUEST_TIMEOUT * 60;
        $q = "DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE timestamp < $timeout";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveGuests();
    }

    //Mano funkcijos:
    //----------------------------------------------------------------------------------------------------------------------------------------------
    
    /**
     * addCampEvent - Adds new camp event with values, specified by the input
     * parameters.
     */
    function addCampEvent($title, $date, $duration, $whereabout, $price, $capacity, $leader, $phone_number, $short_description, $description, $starred) {
        $formatted_short_description = nl2br($short_description);
        $formatted_description = nl2br($description);
        $q = "INSERT INTO " . TBL_EVENTS . " VALUES (NULL, '$title', '$date', $duration, '$whereabout', '$price', $capacity, '$leader', '$phone_number', '$formatted_short_description', '$formatted_description', $starred)";
        return mysqli_query($this->connection, $q);
    }
    
    /**
     * editCampEvent - Edits specified by id camp event with values, specified by the input
     * parameters.
     */
    function editCampEvent($id, $title, $date, $duration, $whereabout, $price, $capacity, $leader, $phone_number, $short_description, $description, $starred) {
        $formatted_short_description = nl2br($short_description);
        $formatted_description = nl2br($description);
        $q = "UPDATE " . TBL_EVENTS . " SET title = '$title', date = '$date', duration = $duration," . 
                " whereabout = '$whereabout', price = '$price', capacity = $capacity, leader = '$leader'," . 
                " phone_number = '$phone_number', short_description = '$formatted_short_description'," . 
                " description = '$formatted_description', starred = $starred WHERE id = $id";
        return mysqli_query($this->connection, $q);
    }
    
    /**
     * addCampEvent - Adds new camp event with values, specified by the input
     * parameters.
     */
    function getCampEventsCount() {
        $q = "SELECT COUNT(*) as count FROM " . TBL_EVENTS;
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }
    
    /*
     * Gets all events data
     */
    function getAllEvents() {
        $q = "SELECT * FROM " . TBL_EVENTS . " ORDER BY date DESC";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return $result;
    }
    
    /*
     * Gets specified event data
     */
    function getSingleEventData($id) {
        $q = "SELECT * FROM " . TBL_EVENTS . " WHERE id = $id";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return $result;
    }
    
    /*
     * Gets specified event data
     */
    function getStarredEventsData() {
        $q = "SELECT * FROM " . TBL_EVENTS . " WHERE starred = 1 ORDER BY date DESC";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return $result;
    }
    
    /*
     * Gets how many starred events are
     */
    function countStarredlEvents() {
        $q = "SELECT COUNT(*) as count FROM " . TBL_EVENTS . " WHERE starred = 1";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }
    
    /*
     * Gets how many users are registered to specified event
     */
    function countRegisteredUsers($id) {
        $q = "SELECT COUNT(*) as count FROM " . TBL_EVENT_USER . " WHERE fk_EVENTid = $id";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }
    
    /*
     * Gets how many users are registered to specified event
     */
    function getUserName($username) {
        $q = "SELECT name FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }
    
    /*
     * Registers user to event
     */
    function registerToEvent($eventid, $username) {
        $q = "INSERT INTO " . TBL_EVENT_USER . " VALUES ($eventid, '$username')";
        $result = mysqli_query($this->connection, $q);
        
        /* Error occurred, return given name by default */
        if (!$result) {
            return NULL;
        }
        /* Return success result */
        return TRUE;
    }
    
    /*
     * Gets all user registered events
     */
    function getUserEventsData($username) {
        $q = "SELECT * FROM " . TBL_EVENTS . " INNER JOIN " . TBL_EVENT_USER . 
                " ON " . TBL_EVENT_USER . ".fk_EVENTid = " . TBL_EVENTS . 
                ".id WHERE " . TBL_EVENT_USER . 
                ".fk_USERusername = '$username' ORDER BY date DESC";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return $result;
    }
    
    /*
     * Returns 1 if user is registered to specified event, else returns zero
     */
    function checkUserEvent($eventid, $username) {
        $q = "SELECT COUNT(*) as count FROM " . TBL_EVENT_USER . " WHERE fk_EVENTid = $eventid AND fk_USERusername = '$username'";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }
    
    /*
     * Unregisters user from event
     */
    function unregisterFromEvent($eventid, $username) {
        $q = "DELETE FROM " . TBL_EVENT_USER . " WHERE fk_EVENTid = $eventid AND fk_USERusername = '$username'";
        $result = mysqli_query($this->connection, $q);
        
        /* Error occurred, return given name by default */
        if (!$result) {
            return NULL;
        }
        /* Return success result */
        return TRUE;
    }
    
    
    /*
     * Updates user data
     */
    function editUser($username, $subemail, $name, $surname, $borndate, $leader) {
        $q = "UPDATE " . TBL_USERS . " SET email = '$subemail', name = '$name', " 
           . "surname = '$surname', born_date = '$borndate', leader = '$leader' WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        
        /* Error occurred, return given name by default */
        if (!$result) {
            return NULL;
        }
        /* Return success result */
        return TRUE;
    }
    
    function getUserCurrPass($username) {
        $q = "SELECT password FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }
    
    /*
     * Gets all registered users and their emails to specified event
     */
    function getRegisteredUsersToEvent($eventid) {
        //SELECT EVENT_USER.fk_USERusername, USER.email FROM EVENT_USER INNER JOIN USER ON EVENT_USER.fk_USERusername = USER.username  WHERE fk_EVENTid = 7
        $q = "SELECT " . TBL_USERS . ".email FROM " . TBL_EVENT_USER 
                . " INNER JOIN " . TBL_USERS . " ON " . TBL_EVENT_USER . 
                ".fk_USERusername = " . TBL_USERS . ".username WHERE fk_EVENTid = $eventid";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return $result;
    }
    
    /*
     * Gets specified event title
     */
    function getEventTitle($eventid) {
        $q = "SELECT title FROM " . TBL_EVENTS . " WHERE id = '$eventid'";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }
    
    /*
     * Gets all registered users and their emails to specified event
     */
    function getRegisteredUsersToCamp($eventid) {
        $q = "SELECT " . TBL_USERS . ".name, " . TBL_USERS . ".surname, " . 
                TBL_USERS . ".leader FROM " . TBL_USERS 
                . " INNER JOIN " . TBL_EVENT_USER . " ON " . TBL_EVENT_USER . 
                ".fk_USERusername = " . TBL_USERS . ".username WHERE fk_EVENTid = $eventid";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return $result;
    }

    /**
     * query - Performs the given query on the database and
     * returns the result, which may be false, true or a
     * resource identifier.
     */
    function query($query) {
        return mysqli_query($this->connection, $query);
    }

}

/* Create database connection */
$database = new MySQLDB;
?>
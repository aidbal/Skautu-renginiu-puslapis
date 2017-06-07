<?php
/**
 * Constants.php
 *
 * This file is intended to group all constants to
 * make it easier for the site administrator to tweak
 * the login script.
 *
 * Database Constants - these constants are required
 * in order for there to be a successful connection
 * to the MySQL database. Make sure the information is
 * correct.
 */
define("DB_SERVER", "db.if.ktu.lt");
define("DB_USER", "aidbal");
define("DB_PASS", "");
define("DB_NAME", "aidbal");


//Defines how many events there should be on main screen
define("MAX_STARRED_EVENTS", 3);

// your secret key
define("SECRET", "6LdP3A8UAAAAAFH75tWU2aBdcrEGeoMo0DmYhVo1");


define("LEARN_QUESTIONS", 5);
define("EXAM_QUESTIONS", 5);

/**
 * Database Table Constants - these constants
 * hold the names of all the database tables used
 * in the script.
 */
define("TBL_USERS", "USER");
define("TBL_EVENTS", "EVENT");
define("TBL_EVENT_USER", "EVENT_USER");

define("TBL_ACTIVE_USERS", "ACTIVE_USERS");
define("TBL_ACTIVE_GUESTS", "ACTIVE_GUESTS");
define("TBL_BANNED_USERS", "BANNED_USERS");
define("TBL_QUESTION", "QUESTION");
define("TBL_TEST_DETAILS", "TEST_DETAILS");
define("TBL_QUESTION_TEST_DETAILS", "QUESTION_TEST_DETAILS");

/**
 * Special Names and Level Constants - the admin
 * page will only be accessible to the user with
 * the admin name and also to those users at the
 * admin user level. Feel free to change the names
 * and level constants as you see fit, you may
 * also add additional level specifications.
 * Levels must be digits between 0-9.
 */
define("ADMIN_NAME", "Administratorius");
define("MODERATOR_NAME", "Moderatorius");
define("USER_NAME", "Vartotojas");
define("ADMIN_LEVEL", 9);
define("MODERATOR_LEVEL", 5);
define("USER_LEVEL", 1);


/**
 * This boolean constant controls whether or
 * not the script keeps track of active users
 * and active guests who are visiting the site.
 */
define("TRACK_VISITORS", true);

/**
 * Timeout Constants - these constants refer to
 * the maximum amount of time (in minutes) after
 * their last page fresh that a user and guest
 * are still considered active visitors.
 */
define("USER_TIMEOUT", 10);
define("GUEST_TIMEOUT", 5);

/**
 * Cookie Constants - these are the parameters
 * to the setcookie function call, change them
 * if necessary to fit your website. If you need
 * help, visit www.php.net for more info.
 * <http://www.php.net/manual/en/function.setcookie.php>
 */
define("COOKIE_EXPIRE", 60 * 60 * 24 * 100);  //100 days by default
define("COOKIE_PATH", "/");  //Avaible in whole domain

/**
 * Email Constants - these specify what goes in
 * the from field in the emails that the script
 * sends to users, and whether to send a
 * welcome email to newly registered users.
 */
define("EMAIL_FROM_ADRESS", "infoskautatinklis@gmail.com");
define("EMAIL_FROM_PASS", "labas123");
define("EMAIL_WELCOME", true);

/**
 * This constant forces all users to have
 * lowercase usernames, capital letters are
 * converted automatically.
 */
define("ALL_LOWERCASE", true);
?>

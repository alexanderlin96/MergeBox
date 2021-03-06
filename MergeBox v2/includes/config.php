<?php

    /**
     * config.php
     *
     * Computer Science 50
     * Problem Set 7
     *
     * Configures pages.
     */

    // display errors, warnings, and notices
    ini_set("display_errors", true);
    error_reporting(E_ALL);

    // requirements
    require("constants.php");
    require("functions.php");

    // enable sessions
    session_start();

    // require authentication for all pages except /login.php, /logout.php, and /register.php
    if (!in_array($_SERVER["PHP_SELF"], ["/ajax_handler.php","/logout.php", "/register.php", "/login.php","/checklogin.php","/home.php"]))
    {
        if (empty($_SESSION["id"]))
        {
            redirect("home.php");
        }
    }
define("KILO", 1024);
define("MEGA", KILO * 1024);
define("GIGA", MEGA * 1024);
define("TERA", GIGA * 1024);

/*function format_bytes($bytes) {
    if ($bytes < KILO) {
        return $bytes . ' B';
    }
    if ($bytes < MEGA) {
        return round($bytes / KILO, 2) . ' KB';
    }
    if ($bytes < GIGA) {
        return round($bytes / MEGA, 2) . ' MB';
    }
    if ($bytes < TERA) {
        return round($bytes / GIGA, 2) . ' GB';
    }
    return round($bytes / TERA, 2) . ' TB';
}*/
?>

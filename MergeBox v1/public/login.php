<?php

    // configuration
    require("../includes/config.php"); 

    // GET request response
    if ($_SERVER["REQUEST_METHOD"] == "GET")
    {
        render("login_form.php", ["title" => "Log In"]);
    }

    // POST response
    else if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        // validate submission
        if (empty($_POST["username"]))
        {
            apologize("You must provide your username.");
        }
        else if (empty($_POST["password"]))
        {
            apologize("You must provide your password.");
        }

        // query database for user
        $rows = query("SELECT * FROM users WHERE username = ?", $_POST["username"]);

        // if we found user, check password
        if (count($rows) == 1)
        {
            $row = $rows[0];

            // compare hashes to see if the same
            if (crypt($_POST["password"], $row["password"]) == $row["password"])
            {
                // store session
                $_SESSION["id"] = $row["id"];
		$_SESSION["username"] = $row["username"];
                // redirect to portfolio
                redirect("/");
            }
        }

        // else apologize
        apologize("Invalid username and/or password.");
    }

?>


<?php
    // configuration
    require("../includes/config.php"); 
    // if form was submitted
    use Dropbox as dbx;
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        require_once "../lib/Dropbox/autoload.php";
	#function will get a WebAuth object that will generate a url which the client authenticates the app.
	function getWebAuth()
	{
	    $appInfo = dbx\AppInfo::loadFromJsonFile("../lib/Dropbox/app-info.json");
	    $clientIdentifier = "my-app/1.0";
            $redirectUri = "http://localhost/dump.php";
            $csrfTokenStore = new dbx\ArrayEntryStore($_SESSION, 'dropbox-auth-csrf-token');
            return new dbx\WebAuth($appInfo, $clientIdentifier, $redirectUri, $csrfTokenStore, "en");
	}
	$authorizeUrl = getWebAuth()->start();
	#redirects to dropbox authentication
	header("Location: $authorizeUrl");
    }
    else
    {
	// else render form
	render("linkaccount_form.php", ["title" => "Link Account"]);
    }
?>

<!DOCTYPE html>
<html>
<!--index is not called by the render function so header had to be put in manually-->
    <head>   
	<link rel="icon" type="image/png" href="/img/mylogo.png">
        <link href="/css/bootstrap.min.css" rel="stylesheet"/>
        <link href="/css/bootstrap-theme.min.css" rel="stylesheet"/>
    <link href='http://fonts.googleapis.com/css?family=Muli' rel='stylesheet' type='text/css'>
<link href="css/styles.css" rel="stylesheet"/>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<script type="text/javascript" src="/js/plupload.full.min.js"></script>
        <?php
if (isset($title)):
?>
           <title>MergeBox: <?= htmlspecialchars($title) ?></title>
        <?php
else:
?>
           <title>MergeBox</title>
        <?php
endif;
?>
<script src="/js/jquery-1.11.1.min.js"></script>
<!--javascript for when you click on a row in the table. You go to the row's link (either downloading a file or going into a directory)-->
<script>
$(document).ready(function() {
    $('#displayfiles tr').click(function() {
    var href = $(this).attr("href");
    if(href)
    {
    window.location = href;
    }
    else
    alert("no link");
    });
});
</script>
    </head>
<div class="container">
            <div id="top">
                <a href="/"><img id="logo" alt="MERGEBOX" src="/img/logo3.png"/></a>
            </div>
            <div id="middle">
<table id="displayfiles" class="table table-hover">
    <tbody>
    <?php
require("../includes/config.php");
#load Dropbox api
use \Dropbox as dbx;
require_once "../lib/Dropbox/autoload.php";
#if just at localhost/ print all account directories and files on root
if ($_SERVER['QUERY_STRING'] == '') {
    #get client information using Session ID
    $accounts = query("SELECT * FROM dropbox_accounts WHERE id = ?", $_SESSION["id"]);
    foreach ($accounts as $account) {
	#using each of OAuth tokens, get root folder data
        $client = new dbx\Client($account["dropbox_accessToken"], "PHP");
        $email  = $account["dropbox_email"];
        $user   = $account["dropbox_id"];
        $data   = $client->getMetadataWithChildren("/");
	#parse the information
        foreach ($data['contents'] as $file) {
            $path     = $file['path'];
	    $namefile = explode('/',$path);
	    $num = count($namefile) - 1;
	    $actualname = $namefile[$num];
            $size     = $file['size'];
            $icon     = $file['icon'];
	    #link each row with it's current path with the file name
            $ref_link = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $email . '&' . $path;
                print "<tr class=\"filelist\" href=\"$ref_link\" style=\"text-align: center\"><td class=\"file\" style=\"text-align: center\">$email</td><td class=\"file\" style=\"text-align: center\">$icon</td><td class=\"file\" style=\"text-align: center\">$user</td><td class=\"file\" style=\"text-align: center\">$actualname</td><td class=\"file\" style=\"text-align: center\">$size</td><td class=\"file\" style=\"text-align: center\"><a class =\"deletefile\" href=\"$ref_link&delete\">Delete</a></td></tr>";
        }       
    }
}
#query denotes filepath in Dropbox accounts 
else {
    #parse the query string
    $peices  = explode('&', $_SERVER['QUERY_STRING']);
    $path    = $peices[1];
    $path    = urldecode($path);
    $account = query("SELECT * FROM dropbox_accounts WHERE (id = ? AND dropbox_email = ?)", $_SESSION["id"], $peices[0]);
    $client  = new dbx\Client($account[0]["dropbox_accessToken"], "PHP");
    if(array_key_exists('2', $peices))
    {
    if($peices[2]=='delete')
    {$client->delete($path);
     $linkback = $_SERVER['HTTP_REFERER'];
     header("Location: $linkback");}
    }
    else{
    $email   = $account[0]["dropbox_email"];
    $user    = $account[0]["dropbox_id"];
    
    #convert html to plaintext
    
    #get file information from current path;
    $data    = $client->getMetadataWithChildren("$path");
    #if the file has 'content' (directory) print out the information like normal
    if (array_key_exists('contents', $data)) {
        foreach ($data['contents'] as $file) {
            $path     = $file['path'];
	    //parsing out the filename from the path
	    $namefile = explode('/',$path);
	    $num = count($namefile) - 1;
	    $actualname = $namefile[$num];
            $size     = $file['size'];
            $icon     = $file['icon'];
            $ref_link = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $email . '&' . $path;
                print "<tr href=\"$ref_link\" style=\"text-align: center\"><td class=\"file\" style=\"text-align: center\">$email</td><td class=\"file\" style=\"text-align: center\">$icon</td><td class=\"file\" style=\"text-align: center\">$user</td><td class=\"file\" style=\"text-align: center\">$actualname</td><td class=\"file\" style=\"text-align: center\">$size</td><td class=\"file\" style=\"text-align: center\"><a class =\"deletefile\" href=\"$ref_link&delete\">Delete</a></td></tr>";
        }
    } 
    else {
	#if the file clicked is not a directory (actual file) generate a temp link and download it
        $link = $client->createTemporaryDirectLink($path);
        header("Location: $link[0]");
    }
}
}
?>
   </tbody>
</table>
    <body>
<!--side bar navigation-->
<div id="page-sidebar">
<div>
<a id="linkaccount" href = "linkaccount.php">Link Account</a>
<a id="logout" href = "logout.php">Log Out</a>
</div>
<div id="accountInfo">
<?php
#grabs quota from each account and shows you how much space you have used so far
$accounts = query("SELECT * FROM dropbox_accounts WHERE id = ?", $_SESSION["id"]);
foreach ($accounts as $account) {
    $client      = new dbx\Client($account["dropbox_accessToken"], "PHP");
    $userData    = $client->getAccountInfo();
    $email       = $account["dropbox_email"];
    $normalbytes = $userData['quota_info']['normal'];
    $quotabytes  = $userData['quota_info']['quota'];
#format_bytes is in config.php converts bytes to more readable format.
    print "<br><a class=\"emaillink\" href=\"index.php?$email&/\">" . $email . '</a>:' . '</br>' . format_bytes($normalbytes) . ' out of ' . format_bytes($quotabytes) . '</br>';
}
?>
</div>
<!--example from plupload, filelist will show if you dont have flash, or HTML5-->
<div id="filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
<br />
<!--clicking on of these will trigger actions in the script below-->
<div id="container">
    <a id="pickfiles" href="javascript:;">[Select files]</a> 
    <a id="uploadfiles" href="javascript:;">[Upload files]</a>
</div>

<br />
<!--where the files you have uploaded will show up-->
<pre id="console"></pre>
</div>
</body>
<script type="text/javascript">
// Custom example logic
// example from plupload, changed a little to suit situation. This will upload large files in 'chunks' and reassemble them on the server

//counts number of files user wants to upload
var count = 0;
//counts number of files successfully uploaded
var complete = 0;
var uploader = new plupload.Uploader({
    runtimes : 'html5,flash,silverlight,html4',
    browse_button : 'pickfiles', // you can pass in id...
    container: document.getElementById('container'), // ... or DOM Element itself
    url : 'http://localhost/upload.php',
    //each chunk is 2mb, which is the limit specified in php.ini
    chunk_size: '2000kb',
    max_retries: 3,
    flash_swf_url : 'http://localhost/js/Moxie.swf',
    silverlight_xap_url : 'http://localhost/js/Moxie.xap',

    init: {
        PostInit: function() {
            document.getElementById('filelist').innerHTML = '';
	    //if on localhost/index.php error will show asking you to do to a directory or click on an account
            document.getElementById('uploadfiles').onclick = function() {
                if(location.search === '')
                {alert("Please Upload to a Directory or Account root");}
                else{                
                uploader.start();
                return false;}
            };
        },

        FilesAdded: function(up, files) {
            plupload.each(files, function(file) {
                document.getElementById('console').innerHTML += '<div id="' + file.id + '">' + file.name + ' (' + plupload.formatSize(file.size) + ') <b></b></div>'; count ++;
            });
        },

        UploadProgress: function(up, file) {
            document.getElementById(file.id).getElementsByTagName('b')[0].innerHTML = '<span>' + file.percent + "%</span>";
        },    

        Error: function(up, err) {
            document.getElementById('console').innerHTML += "\nError #" + err.code + ": " + err.message;
        }
    }
});
uploader.init();
//upond upload success, reload the page so that the files you've just uploaded will show.
uploader.bind('FileUploaded', function(up, file, info) {
complete++;
//if uploads successful, refresh page so the files appear.
if(complete===count){location.reload();}
});
</script>
<body>
            </div>

            

        </div>

    </body>

<div id="bottom">
                Copyright &#169; Alexander Lin
            </div>
</html>

<!DOCTYPE html>
<html>
    <head>
        <title>
            STL Dashboard Settings
        </title>
        <script src="dashboard.js"></script>
        <link type="text/css" rel="stylesheet" href="stl.css">
    </head>
    <body onload="startPage()">
        <div class="banner">
        <h1>Settings</h1>
        </div>
        <div class="menubar">
        <div class="menu" onclick="window.location='index.php'">HOME</div>
        </div>
        <div id="content"> </div>
<?php
    require_once 'common.php';
    $mode = "test";
        session_start();
    
    $config = setConfig();
    $dbConnection = dbConnect($config);
    
    $mysqli = dbConnect($config);
    $id = $_GET['id'];
    $sql = "SELECT * FROM customers WHERE id = $id";
    $result = $mysqli->query($sql);
    $customer = mysqli_fetch_array($result, MYSQLI_ASSOC);

?>
    <div style="margin-left: 40px;">
        <form method='post' action='postSettings.php' onsubmit="settingsChange(event)">
<?php
    echo "<br><br>";
    echo '<input type="hidden" name="id" value="' . $customer['id'] . '">';
    showLine("Customer Account", $customer, "Account", 32);
    showLine("User", $customer, "User", 20);
    showLine("Password", $customer, "Password", 20);
/*    showLine("Ewon Name", $customer, "Ewonname", 20);
    showLine("Ewon User", $customer, "Ewonuser", 20);
    showLine("Ewon password", $customer, "Ewonpw", 20); */
    showLine("Poll interval (seconds)", $customer, "Pollinterval", 6, false);
    echo "<span class='postprompt'>Set interval to zero to poll manually</span>";

// -------------------------------------------
    function showLine($prompt, $dta, $key, $size, $newline=true ) {
            $value = '"' . $dta[$key] . '"';
        echo "\n<span class='prompt'>$prompt</span>";
        echo "<span class='input'>";
        echo "<input type='text' name='$key' id='$key' onChange='fldChange()' size='$size' value=$value>";
        echo "</span>";
        if ($newline) {
            echo "<br><br>";
        }
    }
	
	
?>    
            <br><br><button type='submit'>Post</button>
        </form>
    </div>
    </body>
</html>


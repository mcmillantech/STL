<!DOCTYPE html>
<html>S
    <head>
        <title>
            STL Post Settings
        </title>
        <script src="dashboard.js"></script>
        <link type="text/css" rel="stylesheet" href="stl.css">
    </head>
    <body>
        <div class="banner">
        <h1>Posting Settings</h1>
        </div>
        <div class="menubar">
        <div class="menu" onclick="window.location='index.php'">HOME</div>
        </div>
<?php

echo "<br><br>";
//    require_once 'header.php';
S
$sql = "UPDATE customers SET "
        . "User=" . postField('User')
        . ", Password=" . postField('Password')
//        . ", Ewonname=" . postField('Ewonname')
//        . ", Ewonuser=" . postField('Ewonuser')
//        . ", Ewonpw=" . postField('Ewonpw')
        . ", Pollinterval=" . $_POST['Pollinterval']
        . ", Account=" . postField('Account')
        . " WHERE id=" . $_POST['id'];
    $mysqli->query($sql)
        or die ("Error updating item " . mysqli_error($mysqli));
    echo "Settings updated";

// ----------------------------------------------
//	Make a quoted, safe string from a POST field
//
//	Parameter	key to $_POST
// ----------------------------------------------
function postField($key) {
    $str = '"'
        . addslashes($_POST[$key])
        . '"';
    return $str;
}


?>
    </body>
</html>



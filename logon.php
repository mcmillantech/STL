<?php
// ------------------------------------------------------
//  Project	STL Dashboard
//  File	logon.php
//		Customer log on form
//
//  Author	John McMillan, McMillan Technology
//              mcmillantech@uwclub.net
// ------------------------------------------------------
?>
<!DOCTYPE html>
<html>
    <head>
        <title>
            Dashboard log on
        </title>
        <script src="dashboard.js"></script>
        <link type="text/css" rel="stylesheet" href="stl.css">
    </head>
    <body>
        <div id="content" style="margin-left: 40px;">
          <div id="form">
            <h1>STL Dashboard</h1>
            <h2>Log on</h2>
            <form id="form" action="javascript:logCheck()">
                <span class='prompt'>Account</span>
                <span class="input">
                    <input type="text" name="account" id="account">
                </span><br><br>
                <span class='prompt'>password</span>
                <span class="input">
                    <input type="text" name="password" id="password">
                </span><br><br>
                <input type="submit" value="submit">
            </form>
          </div>

            <br><div id="message"> </div><br>
            <div id="test"> </div>
        </div>
    </body>
</html>

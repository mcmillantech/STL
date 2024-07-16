<?php
// ------------------------------------------------------
//  Project	STL Dashboard
//  File	Meader & menus
//
//  Author	John McMillan, McMillan Technolo0gy
// ------------------------------------------------------

function showHeader($title) {
    echo '<div class="banner">';
    echo "<h1 style='margin-left:40px'> $title</h1>";
    ?>
        </div>
        <div class="menubar">
        <div class="menu" onclick="doInstantValues()">INSTANT VALUES</div>
        <div class="menu" onclick="doTagHeadings()">TAG HEADINGS</div>
        <div class="menu" onclick="window.location='history.php'">HISTORY</div>
        <div class="menu" onclick="settings()">SETTINGS</div>
        <div class="menu" onclick="logOff(0)">LOG OFF</div>
        <div style = "clear: both"> </div>
        </div>
<?php
}
        

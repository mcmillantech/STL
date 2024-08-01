// ------------------------------------------------------
//  Project	STL Dashboard
//  File	dashboard.js
//
//  Author	John McMillan, McMillan Technology
//              mcmillantech@uwclub.net
// ------------------------------------------------------

var spareTags = ["TagName",
    "Q14",
    "Q15",
    "Q16",
    "Inlet_Drive_Hz",
    "Extract_Drive_Hz",
    "Gas_Valve_%",
    "TDE_Amber_Light",
    "TDE_Red_Light"];


/* ---------  onLoad function -------------------
 * Called by every page to check user's logged on
 * @returns {undefined}
 */
function startPage() {
                    // Check user is logged on. If not, route to logon.php.
    let logon = sessionStorage.getItem("logon");
    if (logon === null) {
        window.location.href = "logon.php"; }
                // logon.php posts to logCheck() which calls startApp().
}

/* ------- Called from success in logCheck() ----------
*   Hence when the app first starts
*   Starting needs a chain of async calls
*/
function startApp() {
    startupMakeBoothUrls();
                // First call booth login, drop to fetch tag list
    boothApi('login', startupLoginCallback);
}

/* ----------  receiver from form in logon.php ---------
 * Passes up to logcheck.php 
 * 
 * This replaces AJAX calls
 * 
 */
async function logCheck() {

    const obj = {};
    let el1 = document.getElementById("account");
    let account = el1.value;
    let el2 = document.getElementById("password");
    let password =  el2.value;

    obj.account = account;
    obj.password = password;
    const dta = JSON.stringify(obj);
    const url = "logcheck.php";
    
    let foo = await fetch(url, { 
        method: 'POST', 
        headers: {
             "Content-Type": "application/json"
        },
        body: dta
        }   // End of 2nd parameter of fetch
    )       // End of fetch parameters

   .then(function (response) {    // Methid of foo. Seems to pull out response
        return response.text();
    })
   .then(function (body) {        // Returned message
       onLogCheck (body);
    });
}

/* ----- Callback from logCheck --------
 * 
 * If invalid, display the message and stay in logon.php.
 * Else proceed
 * Sets logon and customer session data
 * @param {type} text - JSON data from logcheck
 *              Error and message or customer
 * @returns {undefined}
 */
function onLogCheck(text) {
    const elm = document.getElementById('message');
    
    const obj = JSON.parse(text);
    console.log(obj);
    if ('message' in obj) {                 // Invalid user or password
        elm.innerHTML = obj.message;
    }
    if (obj.state === "OK") {               // Fetch and store customer data
        elm.innerHTML = "Logged onto app. Connecting to booth";
        let cusObj = obj.customer;
        customerStore(cusObj);
        sessionStorage.setItem("logon", "OK");
        console.log("olc " + sessionStorage);

        startApp();
    }
}

/* -------- Store customer ----------
 * Customer is stored in the session as a JSON string
 * Also store poll interval
 * 
 * @param {object} customer object
 * @returns {undefined}
 */
function customerStore(customer) {
    let cusJs = JSON.stringify(customer);
    let pollInterval = customer.Pollinterval;
    sessionStorage.setItem("Pollinterval", pollInterval);
    sessionStorage.setItem("customer", cusJs);
    
//    console.log(cusJs);
    if (pollInterval > 0) {
        let interval = setInterval(doInstantValues, pollInterval);
    }
}

function logOff() {
    sessionStorage.clear();
    window.location = "loggedoff.php";
}

/*-----  Build the URLs to send to the booth -----
 * 
 * @returns {undefined}
 * Stores URLS for logon, instant values & tags
 */
function startupMakeBoothUrls() {
    const root = "https://m2web.talk2m.com/t2mapi/";
    const devId = "e0f02ddb-a13e-41ed-b5e2-166458d1cdb5"; 
                                            // Credentials
    const credAccount = "STLelectrical";
    const credUser = "Steve Woollard";
    const credPassword = "STLelec1";
    
    let cust = JSON.parse(sessionStorage.getItem('customer'));
//    let account = cust.Account;
//    let user = cust.User;
//    let password = cust.Password;
    let boothName = cust.Boothname;
    let boothUser = cust.Boothuser;
    let boothPw = cust.Boothpw;
    
    let login = root + "login?t2maccount=" + credAccount        // URL to log into booth
        + "&t2musername=" + credUser
        + "&t2mpassword=" + credPassword
        + "&t2mdeveloperid=" + devId;
    sessionStorage.setItem('loginUrl', login);
                                                        // Base for queries
    let apiGet = root + "get/" + boothName + "/rcgi.bin/ParamForm?AST_Param=";
    sessionStorage.setItem('apiGet', apiGet);
    let credentials = "&t2maccount=" + credAccount
        + "&t2mdeveloperid=" + devId
        + "&t2musername=" + credUser
        + "&t2mpassword=" + credPassword
        + "&t2mdeviceusername=" + boothUser
        + "&t2mdevicepassword=" + boothPw;
    sessionStorage.setItem('apiCredentials', credentials);  // Needed for history

    let ivUrl = apiGet + "$dtIV$ftT" + credentials;         // Instant values
    sessionStorage.setItem('ivUrl', ivUrl);
    
//    let tagURL = apiGet + "$dtTL$fnTags.txt" + credentials;
    let tagUrl = apiGet + "$dtTL" + credentials;          // Tag list
    sessionStorage.setItem('tagUrl', tagUrl);
}

/* ---------  Now log into the booth and fetch tags
 * 
 * @param {JSON string} dta
 * @returns {undefined}
 */
function startupLoginCallback (dta) {
    console.log(dta);
    let obj = JSON.parse(dta);                  // Check login to HMS
    if (!obj.success){
        errorShow('log in ', obj.message,
            'Error connecting to booth. Please contact STL');
        logOff();
    }
    boothApi('tags', startupTagsCallback);
}

function errorShow(func, message, help) {
    let str = "Error in " + func + '\n'
        + "Message is " + message + '\n'
        + help;
    alert (str);
}

function startupTagsCallback(tagRows) {
    if (tagRows[0] !== '0') {                  // EBD error is array, [0] = error
        tagsMakeList(tagRows);
        window.location = "index.php";
        return;
    }
//    let obj = JSON.parse(tagRows);
//    if (!obj.success){
        alert (tagRows[1]);
        window.location = "index.php";
}

// ----------  API call to booth ----

async function boothApi(mode, callback) {
   
    switch (mode) {
        case 'iv':
            url = sessionStorage.getItem('ivUrl');
            break;
        case 'login':
            url = sessionStorage.getItem('loginUrl');
            break;
        case 'tags':
            url = sessionStorage.getItem('tagUrl');
            break;
        case 'history':
            url = sessionStorage.getItem('hisUrl');
            break;
    }

    const info = 'https://m2web.talk2m.com/t2mapi/getaccountinfo' ;
    const test = "Test.php";

    const promise = await fetch(url, { 
        method: 'GET', 
        headers: {
             "Content-Type": "multipart/form-data"
//             "Content-Type": "application/json"       For $_POST
        }
//        body: JSON.stringify(params) 
        }
    );

    const res = await promise.text();
    callback(res);
} 

/* ---- Take a block of CSV, split into an array of lines ----
 * 
 * @param {string} line
 * @returns {array} columns for the line
 */
function utilSplitCsvLine (line) {
    let stripRow = line.replace(/"/g, '');  // Strip double quotes
    let columns = stripRow.split(';');      // Create an array for the row

    return columns;
}

function htmlClear() {
    return "<div style = 'clear: both'> </div>";
}

/*  -- Make an array of rows of the CSV data from the booth
 * 
 * @param {string} tagRows
 * @returns {JSON of a 2 dim array of }
 * Called from startupTagsCallback
 */
//  : id, name, units
function tagsMakeList(tagRowsCSV) {
    const tagRows = tagRowsCSV.split("\n");         // Array of rows
    const tagList = [];
    tagRows.forEach(tagRow);               // Now just store index, Id and name
        function tagRow(item, index) {
            let columns = utilSplitCsvLine(item);
            let entry = [columns[0], columns[1], columns[56]];
            tagList.push(entry);
    }
    console.log(tagList);
    const json = JSON.stringify(tagList);
    sessionStorage.setItem("tags", json);
//    window.location.href = "index.php";
}

/*  ----- Call point from Tag Headings menu -------
 * 
 * @returns {undefined}
 */
function doTagHeadings() {
    tagRows = tagsGetFromStore();
    console.log(tagRows);

    var headings = (tagRows[0]);
/*    if (headings[0] === '0') {                // Seems to indicate an error
        let response = headings[1][0];
        let obj = JSON.parse(response);
        if (!obj.success){
            errorShow('tags', obj.message, 'Check your booth name and password');
            return;
        }
    } */
    tagsListHtml(tagRows);
}

/* --- Make HTML for Tags Headings  ----
 * 
 * @param {string array} headings
 * @returns {string} HTML of heading
 */
function tagsListHtml(tagRows) {
    let str = " <h2>Tag headings</h2>";
    tagRows.forEach(tagLine);          // Headings should be tagRows
        function tagLine(item, index) {
            str += item + "<br>";
        }
    let div = document.getElementById("content");
    div.innerHTML = str;
}

function tagsGetFromStore() {
    const json = sessionStorage.getItem("tags");  // CSV data held as JSON
    const obj = JSON.parse(json);
    const tagRows = Object.entries(obj);            // Array of CSV lines
    
    return tagRows;
}

/* ---- Fetch the unit value for Tag(n) ----------
 * Used in instant values list
 * @param {type} inx
 * @returns unit as string
 */
function tagUnit(inx) {
    const json = sessionStorage.getItem("tags");
    const obj = JSON.parse(json);
    const tagRows = Object.entries(obj);     // Now an array of CSV rows */
    const line = tagRows[inx];              // Think tagRows may be a 3 dim array
    const value = line[1][2];

    return value;
}

// ---------  Instant values --------------

/*  function instantValuesStart
 *    Set interval below causes this to poll every few seconds
 *    Poll interval is taken from customer settings, unless the setting is zero.
 *    This avoids continuous polling when the operator is only checking every few minutes.
*/
function doInstantValues() {
    const pollInterval = sessionStorage.getItem('Pollinterval');
    if (pollInterval > 0) {
        let interval = setInterval(pollInstantValues, pollInterval * 1000);
    }
    else pollInstantValues();
}

function pollInstantValues() {
    boothApi('iv', instantValues);
}

/* --------  Callback from async fetches ----------
 * 
 * @param {text} rows - a CSV file
 * @returns {undefined}
 */
function instantValues(result) {
    console.log("IV " + result);

    let d = new Date();
    let strTime = d.toTimeString();
    const rows = result.split('\n');
    
    let str = "<h2>Current Values</h2>";            // Set headings
    str += "Time " + strTime + "<br><br>";
    str += "<b><span class='ivName'>Name</span>";
    str += "<span class='ivValue'>Value</span>";
    str += "<span class='ivUnit'>Units</span>";
    str += "<span class='ivAl'>Alarm</span></b>";
    str += htmlClear();

    rows.forEach(ivRow);                        // Now each row
      function ivRow(item, index) {
        let stripRow = item.replace(/"/g, '');  // Strip double quotes
        let columns = stripRow.split(';');      // Create an array for the row
        let ivName = columns[1];
//        if (index > 0) {                        // Skip the heading row
        if (!spareTags.includes(ivName)) {       // Filter unused tags 
            if (columns.length > 2) {
            str += ivRowHtml(columns);
            str += htmlClear();
            }
        }
    }
    let div = document.getElementById("content");   // Put into the display
    div.innerHTML = str;
}


/* -----   Make HTML for one row of Instant values ---
 * 
 */
function ivRowHtml(ivRow) {
    let style = "";
    if (ivRow[3] > 0) {         // Alarm process
        style = " style='color:red; font-weight:bold;'";
    }
    
    let inx = ivRow[0];
    
    let str = "<span class='ivName'" + style + ">" + ivRow[1] + "</span>";
    str += "<span class='ivValue'" + style + ">" + ivRow[2] + "</span>";
    str += "<span class='ivUnit'" + style + ">" + tagUnit(inx)+ "</span>";
    if (ivRow[3] > 0) {                 // Alarm is on
        str += "<span class='ivAlOn'>On</span>";
    } else {
        str += "<span class='ivAl'>Off</span>";
    }
    return str;
}

/* ------ First post from history.php ------------
 * 
 *  @Post The start and end times
 *  calls start and end functions to format EBD 
 *  parameters, stores for 2nd post
 */
function hisPost() {
    reply = hisStart();
    let start = reply[0];
    let reverseDate = reply[1];
    let end = hisEnd();
    
    let elhisnext = document.getElementById('hisnext');
    elhisnext.style.visibility = "hidden";
    
    let hisfname = document.getElementById("hisfname");
    hisfname.value = reverseDate + "history.txt";
    let elhisfile = document.getElementById('hisfile');
    elhisfile.style.visibility = "visible";
    
    let params = "$dtHT$ftT$st" + start + "$et" + end;
    sessionStorage.setItem('hisFile', hisfname);
    sessionStorage.setItem('boothHisParams', params);
    console.log(params);
}

/* ------ History start and end strings ----
 * 
 * @returns {String}
 */
function hisStart() {
    let el = document.getElementById("stday");
    let rawNo = el.value;
    let sday = rawNo.padStart(2,'0');
    el = document.getElementById("stmon");
    rawNo = el.value;
    let smonth = rawNo.padStart(2,'0');
    el = document.getElementById("styear");
    rawNo = el.value;
    let syear = rawNo.padStart(2,'0');
    el = document.getElementById("sthh");
    rawNo = el.value;
    let sthh = rawNo.padStart(2,'0');
    el = document.getElementById("stmins");
    rawNo = el.value;
    let stmins = rawNo.padStart(2,'0');
    
    let start = sday + smonth + syear + '_' + sthh + stmins + '00';
    let reverseDate = syear + smonth + sday;
    let reply = [start, reverseDate];
    return reply;
}

function hisEnd() {
    let el = document.getElementById("ndday");
    let rawNo = el.value;
    let nday = rawNo.padStart(2,'0');
    el = document.getElementById("ndmon");
    rawNo = el.value;
    let nmonth = rawNo.padStart(2,'0');
    el = document.getElementById("ndyear");
    rawNo = el.value;
    let nyear = rawNo.padStart(2,'0');
    el = document.getElementById("ndhh");
    rawNo = el.value;
    let nhh = rawNo.padStart(2,'0');
    el = document.getElementById("ndmins");
    rawNo = el.value;
    let nmins = rawNo.padStart(2,'0');

    let end = nday + nmonth + nyear + '_' + nhh + nmins +  '00';
    
    return end;
}

/* --------- Post from history.php after file name ----------
 * 
 *  Build the history URL and store in session
 * ----------------------------------------------------
 */
function hisGo() {
    let el = document.getElementById("hisfname");
    let fileName = el.value;        // Value from form
    
    let apiGet = sessionStorage.getItem('apiGet');
    let apiCredentials = sessionStorage.getItem('apiCredentials');
    let params = sessionStorage.getItem('boothHisParams');
    params += '$fn' + fileName;
    
    let hisUrl = apiGet + params + apiCredentials;
    sessionStorage.setItem('hisUrl', hisUrl);
    sessionStorage.setItem('hisFile', fileName);
    console.log(hisUrl);
    
    let els = document.getElementById("status");
    var hisStatus = "Fetching data";
    els.innerHTML = hisStatus;

    boothApi('history', historyCallback);
    //
}

/* ------ History callback -----------
 * 
 * @param  History CSV stream
 */
function historyCallback(res) {
    const fileName = sessionStorage.getItem('hisFile');
    historySave(res, fileName);
    let el = document.getElementById("status");
    var hisStatus = "Saving data";
    el.innerHTML = hisStatus;
}

function historySave(txt, fileName) {
         var blob = new Blob([txt], {
            type: "text/plain;charset=utf-8"
         });
         saveAs(blob, fileName);
}

/* ------  Settings --------------
 * 
 * Called from the menu, fetches stored customer
 * and sends to settings.php
 */
function settings() {
    const cusJson = sessionStorage.getItem("customer");
    const customer = JSON.parse(cusJson);
    const id = customer.id;
    window.location = "settings.php?id=" +  id;
}

/* --------  Handler from setttings.php onSubmit ---------
 * 
 * @param {type} HTML event 
 * @returns {undefined}
 */
function settingsChange(event){
    const target = event.target;
    let customer = JSON.parse(sessionStorage.getItem("customer"));
//    customer.Account = target.Account.value;
    customer.BoothName = target.BoothName.value;
    customer.BoothPw = target.BoothPw.value;
    customer.BoothUser = target.BoothUser.value;
    customer.Password = target.Password.value;
    customer.Pollinterval = target.Pollinterval.value;
    customer.User = target.User.value;
    let pollInterval = target
            .Pollinterval.value;
//    alert ("Settings change " + customer);
    sessionStorage.removeItem("customer");
    customerStore(customer);
/*    let cus = JSON.stringify(customer);

    sessionStorage.setItem("customer", cus);  */
}

// ---  Open a downloaded file, return its content as an array of rows --

 async function testBoothSet(file, callback) {
    const promise = await fetch(file);
    const text = await promise.text();
 //   const rows = text.split('\n');
    callback (text);
}

// ------ Settings ----------
function fldChange() {
    
} 

/* ----   John's debugging helper ------
 * 
 * @param {type} mode
 * @param {type} showSession
 * @returns {undefined}
 */
function test(mode, showSession) {
    if (showSession) {
        console.log(sessionStorage);
    }
    switch (mode) {
        case 1:
            console.log(sessionStorage);
            break;
        case 2:
            boothApi('iv', instantValues);
            break;
        case 3:
            doInstantValues(1);
            break;
        case 4:
            startApp();
            break;
        case 5:
            startupMakeBoothUrls();
            break;
    }
}


<!DOCTYPE html>
<html>
    <head>
        <title>
            STL Setup job
        </title>
        <link type="text/css" rel="stylesheet" href="stl.css">
        <script src="dashboard.js"></script>
    <script>
        function postJob() {
            alert("Post");
            let el1 = document.getElementById('jobNo');
            let jobNo = el1.value;
            let el2 = document.getElementById('cusId');
            let cusId = el2.value;
            updateJobTable(jobNo, cusId);
//        window.location = "index.php";
        }
        
        async function updateJobTable(jobNo, cusId) {
            
            const obj = {};
            obj.jobNo = jobNo;
            obj.cusId = cusId;
            const dta = JSON.stringify(obj);
            let foo = await fetch("postJob.php", { 
                method: 'POST', 
                headers: {
                     "Content-Type": "application/json"
                },
                body: dta
           })
   .then(function (response) {
       console.log("Response " + response);
       alert (response.text);
    }) 
   .then(function (body) {              // Returned message
       alert(body);
       console.log("Response 2" + body);
    });
        }
        function setSession() {
            let el1 = document.getElementById('jobNo');
            let jobNo = el1.value;
            let el2 = document.getElementById('cusId');
            let cusId = el2.value;
            const obj = {};
            obj.jobNo = jobNo;
            obj.cusId = cusId;
        sessionStorage.setItem("job", JSON.stringify(obj));
        console.log("job " + sessionStorage.getItem("job"));
        }
    </script>
    </head>
    <body>

<?php
    require 'header.php';
    showHeader(" STL Dashboard");
    
    $customer = $_GET['cus'];
    $cusVal = "'$customer'";
?>
    <h1>Create job</h1>
    <div style="margin-left: 40px; margin-top: 40px;">
<?php        
        echo "<form method='post' action='postjob.php?cust=$customer' "
                . "onSubmit='setSession()'>";
    ?>
            <span class='prompt'>Job number</span>
            <span class='input'>
                <input name='jobNo' id='jobNo'><br>
            <span class='input'>
<?php
        echo "<input name='cusId' id='cusId' value='$customer'>";
        ?>
            <br><br><input type='submit' value = 'Post'>
        </form>
    </div>

    </body>
</html>

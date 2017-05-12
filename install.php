<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2/25/2016
 * Time: 10:43 PM
 */
?>
<html>
<head>
<style>
    label{
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 20px;
    }
    input{
        width: 100%;
        height: 40px;
        margin-bottom: 10px;
    }
    .lab-div{
        margin-bottom: 20px;
    }
    input[type=submit]{
        width: 70%;
        height: 45px;

    }
</style>
</head>
<body>
<div style="margin: auto; width: 35%">
<h1>Setup Tollr</h1>
    <form method="post" >
        <div style="margin-bottom: 20px">
            <div class="lab-div"><label>Local database details</label></div>
            <input type="text" name="host" placeholder="DB Hostname"/>
            <input type="text" name="user" placeholder="DB username"/>
            <input type="text" name="password" placeholder="DB Password"/>
        </div>

        <div style="margin-bottom: 20px">
            <div class="lab-div"><label>Black Box details</label></div>
            <input type="text" name="toll_unique_id" placeholder="Toll Key"/>
            <input type="text" name="username" placeholder="Black Box username"/>
            <input type="text" name="spassword" placeholder="Black Box Password"/>
        </div>
        <div style="margin-bottom: 20px">
        <input type="submit" value="Submit" style="float: right"/>
        </div>
    </form>
</div>
</body>
</html>

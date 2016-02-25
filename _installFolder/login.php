<?php
session_start();
?>
<!DOCTYPE html>
<head>
  <title>DB IMAC V2 - LOGIN</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style/bootstrap.min.css">
  <link rel="stylesheet" href="style/defaultStyle.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/defaultScript.js"></script>
</head>
<body>
    <div class="container defaultWidth border1px" id="loginForm">
        <h2 class="text-center">DB IMAC V2 - LOGIN</h2>
        <form role="form">
            <div class="form-group">
                <label for="userName">USERNAME</label>
                <input type="text" class="form-control" id="userName" size="10">
            </div>
            <div class="form-group">
                <label for="userPassword">PASSWORD</label>
                <input type="password" class="form-control" id="userPassword" size="50">
            </div>
            <input type="submit" id="send" class="btn btn-warning btn-sm" name='submit' value="Submit">
            </form>
    </div>
  <div class='modal'></div>
</body>
</html>
<?php
session_start();
$_SESSION['root']=dirname(__FILE__);
?>
<!DOCTYPE html>
<head>
  <title>DB IMAC V2</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="style/bootstrap.min.css">
  <link rel="stylesheet" href="setup/setupStyle.css">
  <script src="js/jquery.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="setup/setupScript.js"></script>

</head>
<body>
    <div class="container" id="setupDBform">
        <h2 class="text-center">Setup DB IMAC V2</h2>
        <h3>Setup DB</h3>
        <form role="form">
            <div class="form-group">
                <label for="dbName">Nome DB</label>
                <input type="text" class="form-control" id="dbName" size="10" placeholder="DBIMACV2..o quello a tua scelta">
            </div>
            <div class="form-group">
                <label for="host">Inserisci l'host</label>
                <input type="text" class="form-control" id="host" size="50" placeholder="Normalmente e' localhost">
            </div>
            <div class="form-group">
                <label for="user">Inserisci l'user name per il DB</label>
                <input type="text" class="form-control" id="user" size="15" placeholder="Normalmente è root">
            </div>
             <div class="form-group">
                <label for="psw">Inserisci la password per il DB</label>
                <input type="text" class="form-control" id="psw" size="30" placeholder="Normalmente è vuoto">
            </div>
            <div class="form-group">
                <label for="adminName">Inserisci la user name del primo admin</label>
                <input type="text" class="form-control" id="adminName" size="30" placeholder="Il tuo user name">
            </div>
            <div class="form-group">
                <label for="adminPass">Inserisci la password del primo admin</label>
                <input type="text" class="form-control" id="adminPass" size="30" placeholder="La tua password, potrai cambiarla dopo">
            </div>
            <div class="form-group">
                <label for="adminRealName">Inserisci il tuo nome</label>
                <input type="text" class="form-control" id="adminRealName" size="30" placeholder="Il tuo nome">
            </div>
            <div class="form-group">
                <label for="adminRealSurname">Inserisci il tuo cognome</label>
                <input type="text" class="form-control" id="adminRealSurname" size="30" placeholder="Il tuo cognome">
            </div>
            <div class="form-group">
                <label for="salt">Inserisci un salt</label>
                <input type="text" class="form-control" id="salt" size="30" placeholder="Sii creativo :)">
            </div>
            <input type="submit" id="send1" class="btn btn-warning btn-sm" name='submit' value="Submit">
        </form>
    </div>
    <div class="container-fluid divHidden text-center" id="setupComplete">
      <table>
        <thead>
          <tr><th>Ci siamo quasi!</th></tr>
        </thead>
        <tbody>
          <tr><td><label for="startImac">Inserisci il progressivo IMAC da cui partire</label></td></tr>
          <tr><td><input type="number" id="startImac" value='1' size="10"></td></tr>
          <tr><td><p>Click per continuare!</p></td></tr>
          <tr><td><button class="btn btn-info btn-sm" id="done">Finish!</button></td></tr>
        </tbody>
      </table>
      
    </div>
<div class="modal"></div>
</body>
</html>
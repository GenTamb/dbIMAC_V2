<?php
session_start();
if($_SESSION['loggato']==1)
{
    require_once "core/CLASS_imac.php";
    require_once "core/CLASS_user.php";
    $utente=new UTENTE("","","","","");
    $utente->passa_da_SESSION();
    echo "
    <!DOCTYPE html>
            <head>
              <title>DB IMAC V2 - AMMINISTRAZIONE</title>
              <meta charset='utf-8'>
              <meta name='viewport' content='width=device-width, initial-scale=1'>
              <link rel='stylesheet' href='style/bootstrap.min.css'>
                <link rel='stylesheet' href='style/defaultStyle.css'>
                <link rel='stylesheet' href='style/jquery-ui.structure.min.css'>
                <link rel='stylesheet' href='style/jquery-ui.theme.min.css'>
                <script src='js/jquery.min.js'></script>
                <script src='js/bootstrap.min.js'></script>
                <script src='js/defaultScript.js'></script>
                <script src='js/jquery-ui.min.js'></script>
            </head>";
    if($utente->admin==1)
    {
        echo "
            
            <nav class='navbar navbar-default' role='navigation'>
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class='navbar-header'>
                  <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='.navbar-ex1-collapse'>
                    <span class='sr-only'>Toggle navigation</span>
                    <span class='icon-bar'></span>
                    <span class='icon-bar'></span>
                    <span class='icon-bar'></span>
                  </button>
                  <a class='navbar-brand' href='#'>Amministrazione</a>
                </div>
                <div class='collapse navbar-collapse navbar-ex1-collapse'>
                <ul class='nav navbar-nav'>
                  <li class='dropdown'>
                    <a href='#' class='dropdown-toggle' data-toggle='dropdown'>IMAC<b class='caret'></b></a>
                    <ul class='dropdown-menu'>
                        <li><a href='#' id='newIMAC'>New</a></li> 
                        <li><a href='#' id='editIMAC'>Edit</a></li>
                    </ul>
                  </li>
                  <li class='dropdown'>
                    <a href='#' class='dropdown-toggle' data-toggle='dropdown'>DIPENDENTI<b class='caret'></b></a>
                    <ul class='dropdown-menu'>
                        <li><a href='#' id='newDIP'>New</a></li> 
                        <li><a href='#' id='editDIP'>Edit</a></li>
                    </ul>
                  </li>
                  <li class='dropdown'>
                    <a href='#' class='dropdown-toggle' data-toggle='dropdown'>TIPOLOGIE<b class='caret'></b></a>
                    <ul class='dropdown-menu'>
                        <li><a href='#' id='newTYPE'>New</a></li> 
                        <li><a href='#' id='editTYPE'>Edit</a></li>
                    </ul>
                  </li>
                  <li class='dropdown'>
                    <a href='#' class='dropdown-toggle' data-toggle='dropdown'>UTENTI DB<b class='caret'></b></a>
                    <ul class='dropdown-menu'>
                        <li><a href='#' id='newUSER'>New</a></li> 
                        <li><a href='#' id='editUSER'>Edit</a></li>
                    </ul>
                  </li>
                 </ul>
                </div>
                </nav>
            <body>
            <div class='container-fluid' id='adminContent'></div>   
            
            ";
    }
    else
    {
        echo "<body><div class='alert alert-danger'>NON HAI IL PERMESSO DI ENTRARE QUI!</div>";
    }
    echo "</body></html>";
    
}
else header("location:login.php");
?>
    
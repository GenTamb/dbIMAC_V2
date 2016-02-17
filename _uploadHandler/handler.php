<?php
require_once("../core/db.php");
require_once("extractP7M.php");
session_start();
echo "<!DOCTYPE html>
      <head>
      <title>DB IMAC V2 - UPLOAD Automatico</title>
      <meta charset='utf-8'>
      <meta name='viewport' content='width=device-width, initial-scale=1'>
      <link rel='stylesheet' href='../style/bootstrap.min.css'>
      <link rel='stylesheet' href='../style/defaultStyle.css'>
      <link rel='stylesheet' href='../style/jquery-ui.structure.min.css'>
      <link rel='stylesheet' href='../style/jquery-ui.theme.min.css'>
      <script src='../js/jquery.min.js'></script>
      <script src='../js/bootstrap.min.js'></script>
      <script src='../js/defaultScript.js'></script>
      <script src='../js/jquery-ui.min.js'></script>
    </head>
    <body>";
if(($_GET['upload'])=='new')
{
    echo "
     <form id='uploader'  method='post' action='handler.php' enctype='multipart/form-data'>
        <input id='file' name='uppati[]' type='file' multiple='multiple'>
        <input type='submit' id='sendAUTO' name='send' value='carica'>
     </form>";
}

if(isset($_POST['send']))
{
    $counter=0;   
    $uploadDirTemp = ROOT."\\_uploadHandler\\temp_".$_SESSION['username']."\\";
    $uploadDirDest = ROOT."\\_uploadHandler\\job_".$_SESSION['username']."\\";
    mkdir($uploadDirTemp);
    mkdir($uploadDirDest);
    foreach($_FILES['uppati']['error'] as $file=> $error)
    {
        {
            $uploadfile = $uploadDirTemp . ($_FILES['uppati']['name'][$file]);
            //$uploadfile=str_replace(" ","",$uploadfile);
            if (move_uploaded_file($_FILES['uppati']['tmp_name'][$file], $uploadfile))
            {   
             echo "<div class='alert alert-success'>Uppato il file: ".$_FILES['uppati']['name'][$file]."</div>";
             
             if($_FILES['uppati']['type'][$file]=='application/pkcs7-mime') //se è p7m
             {
               if(!extractFromP7M($_FILES['uppati']['name'][$file],$uploadDirTemp,$uploadDirDest)) //estrai e cancella p7m
                      echo "<div class='alert alert-danger'>errore estrazione file: ".$_FILES['uppati']['name'][$file]."</div>"; //altrimenti messaggio di errore
               else $counter++;       
             }
             else if($_FILES['uppati']['type'][$file]=='application/excel' ||
                     $_FILES['uppati']['type'][$file]=='application/vnd.ms-excel' ||
                     $_FILES['uppati']['type'][$file]=='application/x-excel' ||
                     $_FILES['uppati']['type'][$file]=='application/x-msexcel') //se è xls
             {
                if(!moveWhatEverExt($_FILES['uppati']['name'][$file],$uploadDirTemp,$uploadDirDest))  //sposto in job
                     echo "<div class='alert alert-danger'>errore spostamento file ".$_FILES['uppati']['name'][$file]."</div>";  //altrimenti messaggio di errore
                else $counter++;     
             }
             else  //altrimenti, se è altro file, avviso e cancello
             {
                echo "<div class='alert alert-warning'>File ".$_FILES['uppati']['name'][$file]." non supportato per l'upload automatico!</div>";
                unlink($uploadDirTemp.$_FILES['uppati']['name'][$file]);
             }
            }
            else
            {
             echo "<div class='alert alert-danger'>Errore in upload di ".$_FILES['uppati']['name'][$file]."</div>";
            }
        }
    }
    echo "<span>File correttamenti uppati : <span id='numeroFilesOK' class='badge'>".$counter."</span></span><br>";
    echo "<button class='btn btn-lg btn-info' id='closeHandler'>Chiudi</button>";
    
}
echo "</body>
      </html>";



?>
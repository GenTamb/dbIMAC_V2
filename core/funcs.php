<?php
session_start();
include_once "db.php";

/*************************************************************************
 *                               funzioni gen                            *
 *************************************************************************/
function sanitizeInput($string)
{
    $string= strip_tags($string);
    $string= htmlentities($string);
    $string= stripslashes($string);
    return $string;
}

function echoResponse($es,$msg)
{
    $response[]=$es;
    $response[]=$msg;
    echo json_encode($response);
}


/**************************************************************************************************************************************************
 *                                                          script generali                                                                       *
 **************************************************************************************************************************************************/

/*************************************************************************
 *                               setup script                            *
 *************************************************************************/
if(isset($_POST['setup']))
{
    $dbName=sanitizeInput($_POST['dbName']);
    $dbHost=sanitizeInput($_POST['dbHost']);
    $dbUser=sanitizeInput($_POST['dbUser']);
    $dbPassword=sanitizeInput($_POST['dbPassword']);
    $adminName=sanitizeInput($_POST['adminName']);
    $adminPass=sanitizeInput($_POST['adminPass']);
    $adminRealName=sanitizeInput($_POST['adminRealName']);
    $adminRealSurname=sanitizeInput($_POST['adminRealSurname']);
    $salt=sanitizeInput($_POST['salt']);
    
    //salvo admin name e password in session, per poterle recuperare dopo
    $_SESSION['adminName']=$adminName;
    $_SESSION['adminPass']=$adminPass;
    $_SESSION['adminRealName']=$adminRealName;
    $_SESSION['adminRealSurname']=$adminRealSurname;
    
    
    //acquisisco e conservo in un file le specifiche del db
    if($file=fopen("db.php","w"))
    {
        $dirname=$_SESSION['root'];
        $txt="
        <?php
        /*
         *---------------------------------------------------*
         *-          here are set your DB specs             -*
         *-  this file is generated automatically by the    -*
         *-         db creation with form submit            -*
         *---------------------------------------------------*/
        
        define('DB','$dbName');
        define('HOST','$dbHost');
        define('USER','$dbUser');
        define('PSW','$dbPassword');
        define('SALT','$salt');
        define('ROOT','$dirname');
        ?>
        ";
        fwrite($file,$txt);
        fclose($file);
        //creo DB
        $conn=new mysqli($dbHost,$dbUser,$dbPassword);
        $query="CREATE DATABASE IF NOT EXISTS ".$dbName." CHARACTER SET utf8 COLLATE utf8_general_ci";
        $conn->query($query);
        echoResponse('yes','Setup quasi completato');
        
    }
    else echoResponse('no','Errore..contatta il supporto');
}

if(isset($_POST['completeSetup']))
{
    require_once("CLASS_db.php");
    require_once("CLASS_user.php");
    $db=new DBconn();
    //creazione tabella utenti (utilizzatori)
    $query="CREATE TABLE IF NOT EXISTS utenti
            (username VARCHAR(20),
             password VARCHAR(100) NOT NULL,
             nome VARCHAR(20) NOT NULL,
             cognome VARCHAR(30) NOT NULL,
             admin INT(1) NOT NULL DEFAULT 0,
             PRIMARY KEY(username)) DEFAULT CHARSET=utf8 ENGINE InnoDB";
    if(!$db->query($query)) echo $db->error;
    
    //creazione tabella dipendenti
    $query="CREATE TABLE IF NOT EXISTS dipendenti
            (matricola VARCHAR(10),
             nome VARCHAR(30) NOT NULL,
             cognome VARCHAR(40) NOT NULL,
             PRIMARY KEY(matricola),
             INDEX(cognome(30))) DEFAULT CHARSET=utf8 ENGINE InnoDB";
    if(!$db->query($query)) echo $db->error;
    
    //creazione tabella tipo richiesta
    $query="CREATE TABLE IF NOT EXISTS tipo_richiesta
            (id INT(3) AUTO_INCREMENT,
             nome VARCHAR(30) NOT NULL,
             PRIMARY KEY(id)) DEFAULT CHARSET=utf8 ENGINE InnoDB";
    if(!$db->query($query)) echo $db->error;
    
    //creazione tabella richieste imac
    $query="CREATE TABLE IF NOT EXISTS imac
            (nProtocollo INT(10) AUTO_INCREMENT,
             ticket INT(10) UNSIGNED NOT NULL,
             matUtente VARCHAR(10) NOT NULL,
             stato ENUM('APERTO','CHIUSO'),
             tipoRichiesta INT(3) NOT NULL,
             pathFile VARCHAR(500),
             note VARCHAR(500),
             dataApertura DATE,
             PRIMARY KEY(nProtocollo),
             FOREIGN KEY(matUtente) REFERENCES dipendenti(matricola)
             ON DELETE NO ACTION
             ON UPDATE CASCADE,
             FOREIGN KEY(tipoRichiesta) REFERENCES tipo_richiesta(id)
             ON DELETE NO ACTION
             ON UPDATE CASCADE)DEFAULT CHARSET=utf8 ENGINE InnoDB";
    if(!$db->query($query)) echo $db->error;
    
    //creo primo admin
    $utente=new UTENTE($_SESSION['adminName'],$_SESSION['adminPass'],$_SESSION['adminRealName'],$_SESSION['adminRealSurname'],1);
    if(!$utente->inserisciUtente()) echo $db->error;
    else if(!rename(ROOT."/setup.php",ROOT."/_installFolder/setup.php")) echo "errore nello spostamento files";
    else if(!rename(ROOT."/setup",ROOT."/_installFolder/setup")) echo "errore nello spostamento files";
    else echoResponse('yes',"Setup completato");
}

/*************************************************************************
 *                               login script                            *
 *************************************************************************/

if(isset($_POST['loginAttempt']))
{
    require_once("CLASS_user.php");
    $username=sanitizeInput($_POST['userName']);
    $userpass=sanitizeInput($_POST['userPass']);
    
    $user=new UTENTE($username,$userpass,"","","");
    if($user->checkLogin())
    {
        $user->passa_a_SESSION();
        $_SESSION['loggato']=1;
        echoResponse("yes","Benvenuto ".$user->nome." ".$user->cognome);
    }
    else echoResponse("no","Non ti conosco");
}

/*************************************************************************
 *                               index script                            *
 *************************************************************************/

 //mostra ultime 100 imac
if(isset($_POST['showLastImac']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacFinoA($_POST['howMany']);
}

//recupera note per pop-up
if(isset($_POST['recuperaNote']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    $imac->istanziaImacByProt($_POST['nProtocollo']);
    echo $imac->note;
}

//recupera link file per download
if(isset($_POST['recuperaFile']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    $imac->istanziaImacByProt($_POST['nProtocollo']);
    echo $imac->pathFile;
}

//logout
if(isset($_POST['logout']))
{
    session_destroy();
}

/*************************************************************************
 *                               filter script                           *
 *************************************************************************/
if(isset($_POST['cercaXnprocollo']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacDaParametro($_POST['nprotocollo'],"protocollo");
}

if(isset($_POST['cercaXticket']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacDaParametro($_POST['ticket'],"ticket");
}

if(isset($_POST['cercaXmatricola']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacDaParametro($_POST['matricola'],"matricola");
}

if(isset($_POST['cercaXdata']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacDaParametro($_POST['data'],"data");
}

if(isset($_POST['cercaXrange']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacDaParametro($_POST['from'],"range",$_POST['until']);
}

/*************************************************************************
 *                               admin script                            *
 *************************************************************************/

if(isset($_POST['cercaUtenteByMat']))
{
    require_once("CLASS_dip.php");
    $dip=new DIPENDENTE();
    if($dip->getDipByMat($_POST['matUtente']))
    {
        $data[]=$dip->cognome;
        $data[]=$dip->nome;
        echo json_encode($data);
    }
    else
    {
        $data[]="aggiungere dipendente";
        $data[]="dal pannello";
        echo json_encode($data);
    }
}

if(isset($_POST['hintMatricola']))
{
    require_once("CLASS_dip.php");
    $mat=$_POST['mat'];
    $listaDip=new DIPENDENTE();
    echo $listaDip->getHintList($mat);
}

if(isset($_POST['deleteTempFiles']))
{
    require_once "db.php";
    
    $jobDir=ROOT."\\_uploadHandler\\temp_".$_SESSION['username']."\\";
    $tempDir=ROOT."\\_uploadHandler\\job_".$_SESSION['username']."\\";
    $fileList_jobDir=array_slice(scandir($jobDir),2);
    $fileList_tempDir=array_slice(scandir($tempDir),2);
    foreach($fileList_jobDir as $file_jobDir) unlink($jobDir.$file_jobDir);
    foreach($fileList_tempDir as $file_tempDir) unlink($tempDir.$file_tempDir);
    rmdir($jobDir);
    rmdir($tempDir);
    $res[]="deleted";
    echo json_encode($res);
    
    
}








?>
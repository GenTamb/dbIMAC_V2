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

function moveAndRename($fileName,$newFileName,$origin,$destination)
{
    if(rename($origin.$fileName,$destination.$newFileName)) return true;
    else return false;   
}

function getFileExt($fileName)
{
    return ".".pathinfo($fileName,PATHINFO_EXTENSION);
    //return substr($fileName,-4);
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
    $prog=$_POST['progressivoImac'];
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
             ON UPDATE CASCADE) AUTO_INCREMENT = ".$prog." DEFAULT CHARSET=utf8 ENGINE InnoDB";
    if(!$db->query($query)) echo $db->error;
    
    //creo primo admin
    $utente=new UTENTE($_SESSION['adminName'],$_SESSION['adminPass'],$_SESSION['adminRealName'],$_SESSION['adminRealSurname'],1);
    if(!$utente->inserisciUtente()) echoResponse('no',$db->error);
    else if(!rename(ROOT."/setup.php",ROOT."/_installFolder/setup.php")) echoResponse('no',"Errore nello spostamento file setup.php");
    else if(!rename(ROOT."/setup",ROOT."/_installFolder/setup")) echoResponse('no',"Errore nello spostamento folder setup");
    else if(!rename(ROOT."/_installFolder/admin.php",ROOT."/admin.php")) echoResponse('no',"Errore nello spostamento file admin.php");
    else if(!rename(ROOT."/_installFolder/login.php",ROOT."/login.php")) echoResponse('no',"Errore nello spostamento file login.php");
    else if(!rename(ROOT."/_installFolder/index.php",ROOT."/index.php")) echoResponse('no',"Errore nello spostamento file index.php");
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

//cambio password form
if(isset($_POST['changePSW']))
{
    require_once("CLASS_user.php");
    $oldpsw=sanitizeInput($_POST['oldpsw']);
    $newpsw=sanitizeInput($_POST['newpsw']);
        
    $user=new UTENTE("","","","","");
    $user->passa_da_SESSION();
    
    if($user->userAggiornaPassword($oldpsw,$newpsw)) echoResponse('yes','Aggiornata password');
    else echoResponse('no','Errore, forse password originale sbagliata');
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

 
/**********************************
 *       IMAC management NEW      *
 **********************************/

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
    
    $tempDir=ROOT."\\_uploadHandler\\temp_".$_SESSION['usernameDBIMACV2']."\\";
    $jobDir=ROOT."\\_uploadHandler\\job_".$_SESSION['usernameDBIMACV2']."\\";
    $fileList_jobDir=array_slice(scandir($jobDir),2);
    $fileList_tempDir=array_slice(scandir($tempDir),2);
    foreach($fileList_jobDir as $file_jobDir) unlink($jobDir.$file_jobDir);
    foreach($fileList_tempDir as $file_tempDir) unlink($tempDir.$file_tempDir);
    rmdir($jobDir);
    rmdir($tempDir);
    $res[]="deleted";
    echo json_encode($res);
        
}

if(isset($_POST['deleteSingleTempFile']))
{
    require_once "db.php";
    $jobDir=ROOT."\\_uploadHandler\\job_".$_SESSION['usernameDBIMACV2']."\\";
    $fileName=$_POST['fileName'];
    if($fileName!='Upload')
    {
        if(unlink($jobDir.$fileName)) echoResponse('yes','Cancellato');
        else echoResponse('no','Errore durante cancellazione, contatta il developer');
    }
    else echoResponse('ni','Colonna cancellata');
}

if(isset($_POST['addRecord']))
{
    require_once "db.php";
    require_once "CLASS_imac.php";
    $jobDir=ROOT."\\_uploadHandler\\job_".$_SESSION['usernameDBIMACV2']."\\";
    $recDir=ROOT."\\recordFile\\";
    
    $ticket=sanitizeInput($_POST['ticket']);
    $matUtente=sanitizeInput($_POST['matUtente']);
    $tipoRichiesta=$_POST['tipoRichiesta'];
    if($_POST['fileName']!='Upload') $fileName=sanitizeInput($_POST['fileName']);
    else $fileName='';
    $note=sanitizeInput($_POST['note']);
    $dataApertura=sanitizeInput($_POST['dataApertura']);
    
    if($dataApertura=='') $dataApertura=Date('Y-m-d');
    
    $splitData=explode("-",$dataApertura);
    
    $error=false;
    
    $recDir=$recDir.$splitData[0]."\\".$splitData[1]."\\".$splitData[2]."\\";
    
    if(!is_dir($recDir))
    {
        if(!(mkdir($recDir, 0777, true)))
        {
            $error=true;
            $msg='Errore creazione cartella';
        }
    }
    $relativePath='recordFile/'.$splitData[0].'/'.$splitData[1].'/'.$splitData[2].'/';
    
    if(!$error)
    {
        $imac=new IMAC();
        $imac->ticket=$ticket;
        $imac->matUtente=$matUtente;
        $imac->stato='APERTO';
        $imac->tipoRichiesta=$tipoRichiesta;
        if($fileName!='') $imac->pathFile='notDefinedYet';
        else $imac->pathFile='';
        $imac->note=$note;
        $imac->data=$dataApertura;
    }
    if(!$error)
    {
        if($imac->inserisciImac()) $msg=$imac->nProtocollo;
        else
        {
            $error=true;
            $msg=$imac->DBconn->error;
        }
    }
    $ext=getFileExt($fileName);
    $newFileName="N".$msg."-Mat_".$matUtente."-Data_".$dataApertura.$ext;
    if(!$error)
    {
        if($fileName!='') $imac->pathFile=$relativePath.$newFileName;
        if(!$imac->aggiornaImac()) $error=true;
    }
    if(!$error)
    {
        if($fileName!='')
        {
            if(!(moveAndRename($fileName,$newFileName,$jobDir,$recDir)))
            {
                $error=true;
                $msg='Errore spostamento file';
            }
        }
    }
    
    (!$error) ? $status="yes" : $status="no";
    echoResponse($status,$msg);  
}

/**********************************
 *      IMAC management EDIT      *
 **********************************/

if(isset($_POST['cercaXnprocolloEDIT']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacDaParametroXedit($_POST['nprotocollo'],"protocollo");
}

if(isset($_POST['cercaXticketEDIT']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacDaParametroXedit($_POST['ticket'],"ticket");
}

if(isset($_POST['cercaXmatricolaEDIT']))
{
    require_once("CLASS_imac.php");
    $imac=new IMAC();
    echo $imac->stampaImacDaParametroXedit($_POST['matricola'],"matricola");
}

if(isset($_POST['editRecord']))
{
    require_once "db.php";
    require_once "CLASS_imac.php";
    $jobDir=ROOT."\\_uploadHandler\\job_".$_SESSION['usernameDBIMACV2']."\\";
    $recDir=ROOT."\\recordFile\\";
    
    $imac=new IMAC();
    $nProtocollo=sanitizeInput($_POST['nProtocollo']);
    $ticket=sanitizeInput($_POST['ticket']);
    $matUtente=sanitizeInput($_POST['matUtente']);
    $stato=$_POST['stato'];
    $tipoRichiesta=$_POST['tipoRichiesta'];
    $newPathFile=$_POST['fileName'];
    $note=sanitizeInput($_POST['note']);
    $dataApertura=$_POST['dataApertura'];
    
    //$nProtocollo=str_replace("N","",$nProtocollo);
    $error=false;
    //istanzio imac
    if(!$imac->istanziaImacByProt($nProtocollo))
    {
        $error=true;
        $msg='Errore: imac non trovata';
    }
    if(!$error)
    {
         //modifico nuovi valori
        $imac->ticket=$ticket;
        $imac->matUtente=$matUtente;
        $imac->stato=$stato;
        $imac->tipoRichiesta=$tipoRichiesta;
        $imac->note=$note;
        $imac->data=$dataApertura;
    }
   
    //se nuovo file, sostituisci al vecchio
    if(!$error && $newPathFile!='')
    {
         if($imac->pathFile!='')
         {
            $oldFilePATHandName=$imac->pathFile;
                   
            $oldFileNameChunk=explode("/",$oldFilePATHandName);
            $absolutePath_original=$recDir.$oldFileNameChunk[1]."\\".$oldFileNameChunk[2]."\\".$oldFileNameChunk[3]."\\";
            $originalFileName=$oldFileNameChunk[4];
            $originalExt=getFileExt($originalFileName);
            $originalFileNameWOExt=str_replace($originalExt,"",$originalFileName);
            
            $newFileExt=getFileExt($newPathFile);
            
            if(!unlink($absolutePath_original.$originalFileName))
            {
                $error=true;
                $msg="Errore cancellazione file ".$absolutePath_original.$originalFileName;
            }
            if(!(moveAndRename($newPathFile,$originalFileNameWOExt.$newFileExt,$jobDir,$absolutePath_original)))
            {
                $error=true;
                $msg='Errore spostamento nuovo file';
            }
            if(!$error)
            {
                $newFilePATHandName=str_replace($originalExt,$newFileExt,$oldFilePATHandName);
                $imac->pathFile=$newFilePATHandName;
            }
         }
        else
        {
            $splitData=explode("-",$dataApertura);
            $recDir=$recDir.$splitData[0]."\\".$splitData[1]."\\".$splitData[2]."\\";
     
            if(!is_dir($recDir))
            {
                if(!(mkdir($recDir, 0777, true)))
                {
                    $error=true;
                    $msg='Errore creazione cartella';
                }
            }
            $relativePath='recordFile/'.$splitData[0].'/'.$splitData[1].'/'.$splitData[2].'/';
            $newFileExt=getFileExt($newPathFile);
            $newFileName="N".$imac->nProtocollo."-Mat_".$imac->matUtente."-Data_".$imac->data.$newFileExt;
            if(!$error)
            {
                $imac->pathFile=$relativePath.$newFileName;
                
            }
            if(!(moveAndRename($newPathFile,$newFileName,$jobDir,$recDir)))
            {
                $error=true;
                $msg='Errore spostamento nuovo file';
            }
            
        } 
    }
       
    if(!$error)
    {
        $imac->aggiornaImac();
        $msg="Aggiornata imac N".$imac->nProtocollo;
    }
    (!$error) ? $status="yes" : $status="no";
    echoResponse($status,$msg);  
    
}

if(isset($_POST['deleteIMAC']))
{
    require_once "db.php";
    require_once "CLASS_imac.php";
    $recDir=ROOT."\\recordFile\\";
    $nProtocollo=$_POST['nProtocollo'];
    //$nProtocollo=str_replace("N","",$nProtocollo);
    
    $imac=new IMAC();
    $imac->istanziaImacByProt($nProtocollo);
    $error=false;
    if($imac->pathFile!='')
    {
        $oldFilePATHandName=$imac->pathFile;     
        $oldFileNameChunk=explode("/",$oldFilePATHandName);
        $absolutePath_original=$recDir.$oldFileNameChunk[1]."\\".$oldFileNameChunk[2]."\\".$oldFileNameChunk[3]."\\";
        $originalFileName=$oldFileNameChunk[4];

        if(!unlink($absolutePath_original.$originalFileName))
        {
            $error=true;
            $msg="Errore cancellazione file ".$absolutePath_original.$originalFileName;
        }
    }
    if(!$imac->cancellaImac())
    {
        $error=true;
        $msg='Errore cancellazione da DB';
    }
    
    (!$error) ? $status="yes" : $status="no";
    echoResponse($status,$msg); 
}

/**********************************
 *   DIPENDENTI management ADD    *
 **********************************/

if(isset($_POST['addDip']))
{
    require_once("CLASS_dip.php");
    $matricola=sanitizeInput($_POST['matricola']);
    $cognome=sanitizeInput($_POST['cognome']);
    $nome=sanitizeInput($_POST['nome']);
    
    $dipendente=NEW DIPENDENTE();
    $dipendente->matricola=$matricola;
    $dipendente->cognome=$cognome;
    $dipendente->nome=$nome;
    
    if(!$dipendente->insertDip()) echoResponse('no',$dipendente->DBconn->error);
    else echoResponse('yes','Aggiunto dipendente con matricola '.$dipendente->matricola);
}

/**********************************
 *   DIPENDENTI management EDIT   *
 **********************************/

if(isset($_POST['cercaDIPxMatricola']))
{
    require_once("CLASS_dip.php");
    $matricola=sanitizeInput($_POST['matricola']);
    
    $dipendente=new DIPENDENTE();
    echo $dipendente->stampaTabellaDIPxEdit($matricola,'matricola');
}

if(isset($_POST['cercaDIPxCognome']))
{
    require_once("CLASS_dip.php");
    $cognome=sanitizeInput($_POST['cognome']);
    
    $dipendente=new DIPENDENTE();
    echo $dipendente->stampaTabellaDIPxEdit($cognome,'cognome');
}

if(isset($_POST['editDipendente']))
{
    require_once("CLASS_dip.php");
    $matricola=sanitizeInput($_POST['matricola']);
    $cognome=sanitizeInput($_POST['cognome']);
    $nome=sanitizeInput($_POST['nome']);
    $nuovaMat=sanitizeInput($_POST['nuovaMat']);
    
    $dipendente=new DIPENDENTE();
    $dipendente->getDipByMat($matricola);
    $dipendente->cognome=$cognome;
    $dipendente->nome=$nome;
    
    if($nuovaMat!='NO')
    {
        $nuovaMat=strtoupper($nuovaMat);
        if(!$dipendente->aggiornaDip($nuovaMat)) echoResponse('no',$dipendente->DBconn->error);
        else echoResponse('yes',"Aggiornato dipendente con nuova matricola ".$nuovaMat);
    }
    else
    {
        if(!$dipendente->aggiornaDip()) echoResponse('no',$dipendente->DBconn->error);
        else echoResponse('yes',"Aggiornato utente con matricola ".$dipendente->matricola);    
    }
    
}

if(isset($_POST['deleteDIP']))
{
    require_once("CLASS_dip.php");
    require_once("CLASS_imac.php");
    $dipendente=new DIPENDENTE();
    $imac=new IMAC();
    
    $dipendente->matricola=$_POST['matricola'];
    $imac->matUtente=$_POST['matricola'];
    if($imac->getAmountImacByToken('matricola')==0)
    {
        $dipendente->cancellaDip();
        echoResponse('yes',"Cancellato dipendente con matricola ".$dipendente->matricola);
    }
    else echoResponse('no',"Il dipendente non può essere cancellato perchè sono presenti IMAC associate a lui");
}

/**********************************
 *    TIPOLOGIE management ADD    *
 **********************************/

if(isset($_POST['addType']))
{
    require_once("CLASS_type.php");
    $nome=sanitizeInput($_POST['nome']);
    
    $tipo=new TIPO();
    $tipo->nome=$nome;
    if(!$tipo->insertType()) echoResponse('no',"Errore inserimento con DB o tipologia già esistente");
    else echoResponse('yes',"Inserita tipologia: ".$tipo->nome);
    
}

/**********************************
 *    TIPOLOGIE management EDIT   *
 **********************************/
//mostra tutte le tipologie
if(isset($_POST['editType']))
{
    require_once("CLASS_type.php");
    $tipo=new TIPO();
    $tipo->stampaTypeListXedit();
}

//script edit tipologia
if(isset($_POST['editTipologia']))
{
    require_once("CLASS_type.php");
    $tipo=new TIPO();
    $newNome=sanitizeInput($_POST['nome']);
    $id=$_POST['id'];
    $tipo->id=$id;
    $tipo->nome=$newNome;
    if(!$tipo->aggiornaType()) echoResponse('no',"Errore aggiornamento record, contatta un admin");
    else echoResponse('yes',"Record Aggiornato");
}

//script del tipologia
if(isset($_POST['deleteType']))
{
    require_once("CLASS_type.php");
    require_once("CLASS_imac.php");
    $tipologia=new TIPO();
    $imac=new IMAC();
    
    $tipologia->getTypeById($_POST['id']);
    $imac->tipoRichiesta=$_POST['id'];
    if($imac->getAmountImacByToken('tipo')==0)
    {
        $tipologia->cancellaType();
        echoResponse('yes',"Cancellata tipologia ".$tipologia->nome);
    }
    else echoResponse('no',"La tipologia non può essere cancellato perchè sono presenti IMAC associate a lei");
}

/**********************************
 *        USER  management ADD    *
 **********************************/

if(isset($_POST['addUser']))
{
    require_once("CLASS_user.php");
    $userName=sanitizeInput($_POST['userName']);
    $userPassword=sanitizeInput($_POST['userPassword']);
    $userCognome=sanitizeInput($_POST['userCognome']);
    $userNome=sanitizeInput($_POST['userNome']);
    $userAdmin=sanitizeInput($_POST['userAdmin']);
    
    $user=new UTENTE($userName,$userPassword,$userNome,$userCognome,$userAdmin);
    
    if(!$user->inserisciUtente()) echoResponse('no',"Errore inserimento! Contatta un Admin");
    else echoResponse('yes',"Inserito utente: ".$user->username);
    
}

/**********************************
 *       USER  management EDIT    *
 **********************************/
//mostra tutti gli utenti per edit
if(isset($_POST['editUser']))
{
    require_once("CLASS_user.php");
    $user=new UTENTE("","","","","");
    $user->stampaUserListXedit();
}

//script edit user
if(isset($_POST['editSingleUser']))
{
    require_once("CLASS_user.php");
    
    $userName=sanitizeInput($_POST['userName']);
    $userCognome=sanitizeInput($_POST['userCognome']);
    $userNome=sanitizeInput($_POST['userNome']);
    $userAdmin=sanitizeInput($_POST['userAdmin']);
    
    $NEWuserName=sanitizeInput($_POST['NEWuserName']);
    $NEWuserPassword=sanitizeInput($_POST['NEWuserPassword']);
    $NEWuserCognome=sanitizeInput($_POST['NEWuserCognome']);
    $NEWuserNome=sanitizeInput($_POST['NEWuserNome']);
    
    $user=new UTENTE($userName,"","","","");
    $error=false;
    $msg="Info aggiornate:";
    if($NEWuserPassword!='NO')
    {
        $NEWuserPassword=$user->salt($NEWuserPassword);
        if(!$user->adminAggiornaUtente($NEWuserPassword,"password"))
        {
            $error=true;
            $msg="Errore aggiornamento password";
        }
        else $msg=$msg."-Password";
    }
    if($NEWuserCognome!='NO')
    {
        if(!$user->adminAggiornaUtente($NEWuserCognome,"cognome"))
        {
            $error=true;
            $msg="Errore aggiornamento cognome";
        }
        else $msg=$msg."-Cognome";
    }
    if($NEWuserNome!='NO')
    {
        if(!$user->adminAggiornaUtente($NEWuserNome,"nome"))
        {
            $error=true;
            $msg="Errore aggiornamento nome";
        }
        else $msg=$msg."-Nome";
    }
    if($user->getUsersCount('admin')==1 && $userAdmin==0)
    {
        $error=true;
        $msg="Sei l'ultimo admin, non puoi declassarti da solo!";
    }
    else
    {
        if(!$user->adminAggiornaUtente($userAdmin,"admin"))
        {
            $error=true;
            $msg="Errore aggiornamento permission";
        }
        else $msg=$msg."-Permission";
    }
    if($NEWuserName!='NO')
    {
        if(!$user->adminAggiornaUtente($NEWuserName,"username"))
        {
            $error=true;
            $msg="Errore aggiornamento username";
        }
        else $msg=$msg."-Username";
    }
    if($error) echoResponse('no',$msg);
    else echoResponse('yes',$msg);
}

//script del user
if(isset($_POST['deleteUser']))
{
    session_start();
    require_once("CLASS_user.php");
    $username=$_POST['username'];
    $user=new UTENTE($username,"","","","");
    
    if($_SESSION['usernameDBIMACV2']==$username)
    {
        $res="no";
        $msg="Non puoi cancellare te stesso!!";
    }
    else if($user->getUsersCount()==1)
    {
        $res="no";
        $msg="Sei l'unico utente rimasto!!";
    }
    else
    {
        if(!$user->cancellaUtente())
        {
            $res='no';
            $msg="Errore, contatta un admin!";
        }
        else
        {
            $res='yes';
            $msg="Utente cancellato";
        }
    }
    echoResponse($res,$msg);
    
}


?>
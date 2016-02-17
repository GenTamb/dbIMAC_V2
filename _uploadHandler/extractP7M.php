<?php
require_once("../core/db.php");
session_start();

function extractFromP7M($fileName,$tempDir,$outputDir)
{
    //normalizzo i nomi dei percorsi e files
    //$outputDir=ROOT."/_uploadHandler/job_".$_SESSION['username']."/";
    $inputFile="\"".$tempDir.$fileName."\"";
    //$inputFile=str_replace("/","\\",$inputFile);
    $outputFile=str_replace(".p7m","","\"".$outputDir.$fileName."\"");
    //$outputFile="\"".$outputDir.$fileName."\"";
    //$outputFile=str_replace("/","\\",$outputFile);
    
    //scrivo il comando del batch
    $cmd="openssl.exe smime -verify -inform DER -in ".$inputFile." -noverify -out ".$outputFile;
    
    //identifico il nome del batch
    $path="bat_".$_SESSION['username'].".bat";
    
    //scrivo nel file batch
    $batch=fopen($path,"w");
    fwrite($batch,$cmd);
    fclose($batch);
    
    //eseguo il batch
    if(exec($path))
    {
    //cancello i p7m temporanei
    unlink($tempDir.$fileName);
    unlink($path);
    return true;
    }
    else return false;
}

function moveWhatEverExt($fileName,$origin,$destination)
{
    //$origin=ROOT."\\_uploadHandler\\temp_".$_SESSION['username']."\\";
    //$destination=ROOT."\\_uploadHandler\\job_".$_SESSION['username']."\\";
    if(rename($origin.$fileName,$destination.$fileName)) return true;
    else return false;
    
}
?>
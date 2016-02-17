<?php
function getMat($fileName)
{
error_reporting(E_ALL ^ E_NOTICE);
require_once "excel_reader2.php";
try
{
  $data = new Spreadsheet_Excel_Reader($fileName,false);
  $mat=$data->val(5,"D",1);
  return $mat;    
}
catch(Exception $e)
{
    return "Errore in lettura";
}

}



?>
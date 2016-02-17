<?php

/*************************************************************************
 *                               admin  script                           *
 *************************************************************************/
session_start();

if(isset($_POST['newIMAC']))
{
    echo "
    <div class='container' id='sceltaProcedura'>
        <label class='radio-inline' class='suggerimento' data-toggle='tooltip' title='Verifica presenza di files nella cartella temporanea personale'>
        <input type='radio' name='procedura' value='continue'>Continua lavoro in sospeso</label>
        <label class='radio-inline' class='suggerimento' data-toggle='tooltip' title='Prevede upload del file p7m-xls ed inserimento automatico del record'>
        <input type='radio' name='procedura' value='auto'>Automatica</label>
        <label class='radio-inline' class='suggerimento' data-toggle='tooltip' title='Prevede upload del file generico ed inserimento manuale'>
        <input type='radio' name='procedura' value='manual'>Manuale</label>
    </div>
    <div class='container nascosto' id='pAuto'>
        <button id='openUploadForm' class='btn btn-sm btn-info'>Upload Form</button>
     </div>
    <div class='container nascosto' id='pAuto_fetch'>
       <button id='startFetchOps' class='btn btn-sm btn-success'>Avvia Fetch Automatica</button>
    </div>  
   ";  
}

if(isset($_POST['fetchIMAC']))
{
    require_once "db.php";
    require_once "excelFunctions.php";
    require_once "CLASS_type.php";
    $option=new TIPO();
    if(is_dir($fetchPath=ROOT."\\_uploadHandler\\job_".$_SESSION['username']."\\"))
  {
    $relativePath="_uploadHandler/job_".$_SESSION['username']."/";
    $fileList=array_slice(scandir($fetchPath),2);
    echo "<div id='imacFetchList' class='table-responsive'>
                          <table class='table'>
                             <thead>
                                 <tr>
                                 <th>FILE</th><th>MATRICOLA</th><th>COGNOME,NOME</th><th>TICKET</th><th>TIPO</th><th>NOTE</th><th>DATA</th><th>PROT.IMAC</th><th>+</th><th>-</th>
                                 </tr>
                             </thead>
                             <tbody>";
    foreach($fileList as $record)
    {
        $completePath=$fetchPath.$record;
        $matUtente=getMat($completePath);
        echo "<tr id='".$matUtente."'>";
        echo "<td class='defwidth'><a href='".$relativePath.$record."'><span id='FETCHfileName'>".$record."</span></a></td>";
        echo "<td><input type='text' id='FETCHmatUtente' class='defwidth' value='".$matUtente."'></td>";
        echo "<td><input type='text' id='FETCHcognomeNome' value=''></td>";
        echo "<td><input type='text' id='FETCHticket' value='' class='defwidth numero'></td>";
        echo "<td>";
        echo $option->stampaComboType();
        echo "</td>";
        echo "<td><input type='text' id='FETCHnote' value=''></td>";
        echo "<td><input type='text' id='FETCHdata' class='defwidth' value=''></td>";
        echo "<td><span id='FETCHprotImac' class='defwidth'></span></td>";
        echo "<td><button id='FETCHadd' class='btn btn-sm btn-success'>ADD</button></td>";
        echo "<td><button id='FETCHdel' class='btn btn-sm btn-warning'>DEL</button></td>";
        echo "</tr>";
    }
    echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><button class='btn btn-sm btn-danger' id='clearFiles'>Clear All</button></td></tr>";
    echo "</tbody></table></div>";
    
    echo "<hr>";
    echo "<div id='hintContent' class='nascosto border1px'></div>";
  }
  else
  {
    echo "Nessun file presente nella directory temporanea";
  }
}





?>
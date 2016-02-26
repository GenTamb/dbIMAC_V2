<?php
session_start();

function stampaHintDiv()
{
    echo "<hr>";
    echo "<div id='hintContent' class='nascosto border1px'></div>";
}

/************************************************************************
 *                              user script                             *
 ************************************************************************/

if(isset($_POST['changePSW']))
{
   echo "
    <div class='container' id='setupDBform'>
    <h2>Cambio password</h2>
    <form role='form'>
        <div class='form-group'>
            <label for='oldPSW'>Vecchia password</label>
            <input type='password' class='form-control pswdefwidth' id='oldPSW'>
        </div>
        <div class='form-group'>
            <label for='newPSW'>Nuova password</label>
            <input type='password' class='form-control pswdefwidth' id='newPSW'>
        </div>
        <div class='form-group'>
            <label for='confirmPSW'>Conferma password</label>
            <input type='password' class='form-control pswdefwidth' id='confirmPSW'>
        </div>
        <input type='submit' id='changePSWbutton' class='btn btn-warning btn-sm' name='submit' value='Submit'>
        </form>
    </div>";
   
}
/*************************************************************************
 *                               admin  script                           *
 *************************************************************************/


/***************************************************
 *                     IMAC                        *
 ***************************************************/

if(isset($_POST['newIMAC']))
{
    echo "
    <div class='container' id='sceltaProcedura'>
        <label class='radio-inline' class='suggerimento' data-toggle='tooltip' title='Verifica presenza di files nella cartella temporanea personale'>
        <input type='radio' name='procedura' value='continue'>Continua lavoro in sospeso</label>
        <label class='radio-inline' class='suggerimento' data-toggle='tooltip' title='Prevede upload del file p7m-xls ed inserimento automatico del record'>
        <input type='radio' name='procedura' value='auto'>Nuovo upload</label>
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
    if(is_dir($fetchPath=ROOT."\\_uploadHandler\\job_".$_SESSION['usernameDBIMACV2']."\\"))
  {
    $relativePath="_uploadHandler/job_".$_SESSION['usernameDBIMACV2']."/";
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
        echo "<td><button id='FETCHadd' class='btn btn-sm btn-success IMAC-CD'>ADD</button></td>";
        echo "<td><button id='FETCHdel' class='btn btn-sm btn-warning IMAC-CD'>DEL</button></td>";
        echo "</tr>";
    }
    echo "<tr><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td><button class='btn btn-sm btn-danger' id='clearFiles'>Clear All</button></td></tr>";
    echo "</tbody></table></div>";
    stampaHintDiv();
  }
  else
  {
    echo "Nessun file presente nella directory temporanea";
  }
}

if(isset($_POST['manualInsert']))
{
    require_once "db.php";
    require_once "excelFunctions.php";
    require_once "CLASS_type.php";
    $option=new TIPO();
    echo "<div id='imacManualList' class='table-responsive'>
                          <table class='table'>
                             <thead>
                                 <tr>
                                 <th>FILE</th><th>MATRICOLA</th><th>COGNOME,NOME</th><th>TICKET</th><th>TIPO</th><th>NOTE</th><th>DATA</th><th>PROT.IMAC</th><th>+</th><th>-</th>
                                 </tr>
                             </thead>
                             <tbody>";
    echo "<tr id='blankManualRow' class='nascosto'>";
    echo "<td id='MANUALupFile' class='MANUALupFileClass'><button class='btn btn-sm btn-info'>Upload</button></td>";
    echo "<td><input type='text' id='FETCHmatUtente' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='FETCHcognomeNome' value=''></td>";
    echo "<td><input type='text' id='FETCHticket' value='' class='defwidth numero'></td>";
    echo "<td>";
    echo $option->stampaComboType();
    echo "</td>";
    echo "<td><input type='text' id='FETCHnote' value=''></td>";
    echo "<td><input type='text' id='FETCHdata_Manual' class='defwidth' value=''></td>";
    echo "<td><span id='FETCHprotImac' class='defwidth DataManual'></span></td>";
    echo "<td><button id='MANUALadd' class='btn btn-sm btn-success IMAC-CD'>ADD</button></td>";
    echo "<td><button id='MANUALdel' class='btn btn-sm btn-warning IMAC-CD'>DEL</button></td>";
    echo "</tr>";
    
    echo "<tr id='manualRow' class='newManualRecord'>";
    echo "<td id='MANUALupFile' class='MANUALupFileClass'><button class='btn btn-sm btn-info'>Upload</button></td>";
    echo "<td><input type='text' id='FETCHmatUtente' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='FETCHcognomeNome' value=''></td>";
    echo "<td><input type='text' id='FETCHticket' value='' class='defwidth numero'></td>";
    echo "<td>";
    echo $option->stampaComboType();
    echo "</td>";
    echo "<td><input type='text' id='FETCHnote' value=''></td>";
    echo "<td><input type='text' id='FETCHdata' class='defwidth DataManual' value=''></td>";
    echo "<td><span id='FETCHprotImac' class='defwidth'></span></td>";
    echo "<td><button id='MANUALadd' class='btn btn-sm btn-success IMAC-CD'>ADD</button></td>";
    echo "<td><button id='MANUALdel' class='btn btn-sm btn-warning IMAC-CD'>DEL</button></td>";
    echo "</tr>";
    
    
    echo "</tbody>
          </table>
          <button id='newRow' class='btn btn-md btn-success'>Add Row</button>
          </div>";
    stampaHintDiv();
    
}

if(isset($_POST['editIMAC']))
{
    echo "
    <ul>
    <li id='filter'><button class='btn btn-info btn-sm' data-toggle='collapse' data-target='#filters'>CERCA IMAC PER:</button></li>
               <li>
                  <ul id='filters' class='collapse'>
                    <li>
                        <div class='form-group'>
                        <label for='cercaXnprotocollo'>Numero Protocollo</label><br>
                        <input id='cercaXnprotocollo' class='filtro numero' type='text' value='' data-toggle='tooltip' data-placement='bottom' title='Inserisci il numero di protocollo senza N'>
                        <button id='cercaXnprotocolloButton' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>   
                    <li>
                        <div class='form-group'>
                        <label for='cercaXticket'>Ticket</label><br>
                        <input id='cercaXticket' type='text'class='filtro numero' value='' data-toggle='tooltip' data-placement='bottom' title='Inserisci il numero di ticket'>
                        <button id='cercaXticketButton' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>
                    <li>
                        <div class='form-group'>
                        <label for='cercaXmatricola'>Matricola</label><br>
                        <input id='cercaXmatricola' type='text' class='filtro' value='' data-toggle='tooltip' data-placement='bottom' title='Inserisci il numero di matricola'>
                        <button id='cercaIMACXmatricolaButton' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>
                  </ul>
                 </li>
    </ul>
   ";  
}

if(isset($_POST['newDIP']))
{
    echo "
        <div id='newDipList' class='table-responsive'>
                          <table class='table'>
                             <thead>
                                 <tr>
                                 <th>MATRICOLA</th><th>COGNOME</th><th>NOME</th><th>+</th><th>-</th>
                                 </tr>
                             </thead>
                             <tbody>";
    echo "<tr id='blankDipRow' class='nascosto'>";
    echo "<td><input type='text' id='FETCHmatUtente' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='DIPcognomeUtente' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='DIPnomeUtente' class='defwidth' value=''></td>";
    echo "<td><button id='ADDdip' class='btn btn-sm btn-success DIP-CD'>ADD</button></td>";
    echo "<td><button id='DELdip' class='btn btn-sm btn-warning DIP-CD'>DEL</button></td>";
    echo "</tr>";
    echo "<tr id='DipRow'>";
    echo "<td><input type='text' id='FETCHmatUtente' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='DIPcognomeUtente' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='DIPnomeUtente' class='defwidth' value=''></td>";
    echo "<td><button id='ADDdip' class='btn btn-sm btn-success DIP-CD'>ADD</button></td>";
    echo "<td><button id='DELdip' class='btn btn-sm btn-warning DIP-CD'>DEL</button></td>";
    echo "</tr>";
    echo "</tbody>
          </table>
          <button id='newRow' class='btn btn-md btn-success'>Add Row</button>
          </div>";
    stampaHintDiv();
         
}

if(isset($_POST['editDIP']))
{
    echo "
    <ul>
    <li id='filter'><button class='btn btn-info btn-sm' data-toggle='collapse' data-target='#filters'>CERCA DIPENDENTE PER:</button></li>
               <li>
                  <ul id='filters' class='collapse'>
                    <li>
                        <div class='form-group'>
                        <label for='cercaXmatricola'>Matricola</label><br>
                        <input id='cercaXmatricola' class='filtro' type='text' value='' data-toggle='tooltip' data-placement='bottom' title='Inserisci la matricola'>
                        <button id='cercaDIPXmatricolaButton' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>   
                    <li>
                        <div class='form-group'>
                        <label for='cercaXcognome'>Cognome</label><br>
                        <input id='cercaXcognome' type='text'class='filtro' value='' data-toggle='tooltip' data-placement='bottom' title='Inserisci il cognome'>
                        <button id='cercaXcognomeButton' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>
                  </ul>
                 </li>
    </ul>
   ";  
}

if(isset($_POST['newTYPE']))
{
    echo "
        <div id='newType' class='table-responsive'>
                          <table class='table'>
                             <thead>
                                 <tr>
                                 <th>DESCRIZIONE</th><th>+</th><th>-</th>
                                 </tr>
                             </thead>
                             <tbody>";
    echo "<tr id='blankTypeRow' class='nascosto'>";
    echo "<td><input type='text' id='description' class='' value=''></td>";
    echo "<td><button id='ADDtype' class='btn btn-sm btn-success TYPE-CD'>ADD</button></td>";
    echo "<td><button id='DELtype' class='btn btn-sm btn-warning TYPE-CD'>DEL</button></td>";
    echo "</tr>";
    echo "<tr id='typeRow'>";
    echo "<td><input type='text' id='description' class='' value=''></td>";
    echo "<td><button id='ADDtype' class='btn btn-sm btn-success TYPE-CD'>ADD</button></td>";
    echo "<td><button id='DELtype' class='btn btn-sm btn-warning TYPE-CD'>DEL</button></td>";
    echo "</tr>";
    echo "</tbody>
          </table>
          <button id='newRow' class='btn btn-md btn-success'>Add Row</button>
          </div>";
    //stampaHintDiv();
         
}

if(isset($_POST['newUSER']))
{
    echo "
     <div id='newUser' class='table-responsive'>
                          <table class='table'>
                             <thead>
                                 <tr>
                                 <th>USERNAME</th><th>PASSWORD</th><th>COGNOME</th><th>NOME</th><th>ADMIN</th><th>+</th><th>-</th>
                                 </tr>
                             </thead>
                             <tbody>";
    echo "<tr id='blankUserRow' class='nascosto'>";
    echo "<td><input type='text' id='userName' class='defwidth' value=''></td>";
    echo "<td><input type='password' id='userPassword' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='userCognome' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='userNome' class='defwidth' value=''></td>";
    echo "<td><select id='userAdmin'>
              <option value='0'>NO</option>
              <option value='1'>SI</option>
              </select>";
    echo "<td><button id='ADDuser' class='btn btn-sm btn-success USER-CD'>ADD</button></td>";
    echo "<td><button id='DELuser' class='btn btn-sm btn-warning USER-CD'>DEL</button></td>";
    echo "</tr>";
    echo "<tr id='firstUserRow' class=''>";
    echo "<td><input type='text' id='userName' class='defwidth' value=''></td>";
    echo "<td><input type='password' id='userPassword' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='userCognome' class='defwidth' value=''></td>";
    echo "<td><input type='text' id='userNome' class='defwidth' value=''></td>";
    echo "<td><select id='userAdmin'>
              <option value='0'>NO</option>
              <option value='1'>SI</option>
              </select>";
    echo "<td><button id='ADDuser' class='btn btn-sm btn-success USER-CD'>ADD</button></td>";
    echo "<td><button id='DELuser' class='btn btn-sm btn-warning USER-CD'>DEL</button></td>";
    echo "</tr>";
    echo "</tbody>
          </table>
          <button id='newRow' class='btn btn-md btn-success'>Add Row</button>
          </div>";
}






?>
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
      <title>DB IMAC V2 - HOME</title>
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
    </head>
    <body>
      <div class='container-fluid' id='header'>
         <h1>DBIMACV2</h1>
		 <p>by GT - Release Candidate 5</p>
      </div>
      <div class='container-fluid' id='bodyPage'>
        <div class='row'>
          <div class='col-sm-2 col-md-2 col-lg-2' id='sideLeftMenu'>
             <ul id='commandList'>
               <li><button class='btn btn-primary btn-sm' id='showLast100'>Mostra ultime IMAC</li>
               <li id='filter'><button class='btn btn-info btn-sm' data-toggle='collapse' data-target='#filters'>CERCA PER:</button></li>
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
                        <button id='cercaXmatricolaButton' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>
					<li>
                        <div class='form-group'>
                        <label for='cercaXnote'>Testo in Note</label><br>
                        <input id='cercaXnote' type='text' class='filtro' value='' data-toggle='tooltip' data-placement='bottom' title='Inserisci il testo da cercare nelle note'>
                        <button id='cercaXnote' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>
                    <li>
                        <div class='form-group'>
                        <label for='cercaXdata'>Data:</label><br>
                        <input type='text' id='cercaXdata' class='dataR'>
                        <button id='cercaXdataButton' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>
                    <li>
                        <input type='checkbox' id='range' name='abilitaRange' value='rangeSI'>Data Range</input>
                        <div class='form-group' id='rangeDiv'>
                        <label for='cercaXdataRange'>Fino a:</label><br>
                        <input type='text' id='cercaXdataRange' class='dataR'>
                        <button id='cercaXdataButtonRange' class='btn btn-xs btn-info buttonFilter'>GO</button>
                        </div>
                    </li>
                  </ul>
                 </li>";
                 if($utente->admin==1) echo "<li><button id='adminConsole' class='btn btn-sm btn-danger'>Amministrazione</button></li>";
                 echo "
                 <li><button id='changePSW' class='btn btn-sm btn-success'>Cambia Password</button></li>
                 <li><button id='logout' class='btn btn-sm btn-warning'>Log Out</button></li>
             </ul>
          </div>
          <div class='col-sm-10 col-md-10 col-lg-10' id='sideRightBody'>
             <div id='container'>
              
             </div>
          </div>
        </div>  
       </div>
    
    <div class='modal' id='loading'></div>
    <div class='popUP' id='popUP-note'>
      <div id='noteContent'></div>
      <button class='btn btn-sm btn-danger' id='closePOPUP'>CHIUDI</button>
    </div>
    </body>
    </html>
    ";
}
else header("location:login.php");

?>






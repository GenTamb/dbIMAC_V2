<?php

require_once ("CLASS_db.php");

class IMAC
{
    public $nProtocollo,$ticket,$matUtente,$stato,$tipoRichiesta,$pathFile,$note,$data,$DBconn;
    
    function __construct()
    {
        $this->DBconn=new DBconn();
    }
    
    function __destruct()
    {
        $this->DBconn->close();
    }
    
    public function istanziaImacByProt($nProtocollo)
    {
        $query="SELECT nProtocollo,ticket,matUtente,stato,tipoRichiesta,pathFile,note FROM imac WHERE nProtocollo ='".$nProtocollo."'";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else
        {
            if($res->num_rows!=0)
            {
                $imac=$res->fetch_assoc();
                $this->nProtocollo=$imac['nProtocollo'];
                $this->ticket=$imac['ticket'];
                $this->matUtente=$imac['matUtente'];
                $this->stato=$imac['stato'];
                $this->tipoRichiesta=$imac['tipoRichiesta'];
                $this->pathFile=$imac['pathFile'];
                $this->note=$imac['note'];
                $this->data=$imac['dataApertura'];
                $fine=true;
            }
            else $fine=false;
        }
        return $fine;
    }
    
    public function inserisciImac()
    {
        $query="INSERT INTO imac (ticket,matUtente,stato,tipoRichiesta,pathFile,note,dataApertura) VALUES ('".$this->ticket."','".$this->matUtente."','".$this->stato."',
               '".$this->tipoRichiesta."','".$this->pathFile."','".$this->note."','".$this->data."')";
        if(!$res=$this->DBconn->query($query))
        {
            echo $this->DBconn->error;
            $fine=false;
        }
        else
        {
            $this->nProtocollo=$this->DBconn->insert_id;
            $fine=true;
        }
        return $fine;
    }
    
    public function aggiornaImac()
    {
        $query="UPDATE imac SET ticket='".$this->ticket."',matUtente='".$this->matUtente."',stato='".$this->stato."',tipoRichiesta='".$this->tipoRichiesta."',pathFile='".$this->pathFile."',
                note='".$this->note."',dataApertura='".$this->data."' WHERE `nProtocollo`='".$this->nProtocollo."'";
        if(!$res=$this->DBconn->query($query))
        {
            echo $this->DBconn->error;
            $fine=false;
        }
        else
        {
            $fine=true;
        }
        return $fine;
    }
    
    public function stampaImacFinoA($numero)
    {
        $query="SELECT im.nProtocollo,im.ticket,im.matUtente,dip.cognome,dip.nome AS nome,im.tipoRichiesta,tipo.nome AS descrizione,im.pathFile,im.note,im.dataApertura
                FROM imac AS im JOIN dipendenti AS dip ON im.matUtente=dip.matricola JOIN tipo_richiesta AS tipo ON im.tipoRichiesta=tipo.id ORDER BY im.nProtocollo DESC LIMIT ".$numero;
        if(!$res=$this->DBconn->query($query)) echo $this->DBconn->error;
        else
        {
            if($res->num_rows>=1)
            {
                echo "<div id='imacList' class='table-responsive'>
                      <table class='table'>
                         <thead>
                             <tr>
                             <th>PROTOCOLLO</th><th>TICKET</th><th>MATRICOLA</th><th>COGNOME,NOME</th><th>TIPO</th><th>NOTE</th><th>FILE</th><th>DATA</th>
                             </tr>
                         </thead>
                         <tbody>";
                    while($row=$res->fetch_assoc())
                    {
                        echo "<tr>";
                        //echo "<td><a class='NPROTOCOLLO' id='nProtocollo' href='#".$row['nProtocollo']."'>N".$row['nProtocollo']."</a></td>";
                        echo "<td><span class='NPROTOCOLLO' id='nProtocollo'>N".$row['nProtocollo']."</span></td>";
                        //echo "<td><a class='TICKET' id='ticket' href='#".$row['ticket']."'>".$row['ticket']."</a></td>";
                        echo "<td><span class='TICKET' id='ticket'>".$row['ticket']."</span></td>";
                        echo "<td><a class='MATRICOLA' id='matricola' href='#".$row['matUtente']."'>".$row['matUtente']."</a></td>";
                        //echo "<td><a class='COGNOME,NOME' id='cognome,nome' href='#".$row['cognome'].",".$row['nome']."'>".$row['cognome'].",".$row['nome']."</a></td>";
                        echo "<td><span class='COGNOME,NOME' id='cognome,nome'>".$row['cognome'].",".$row['nome']."</span></td>";
                        //echo "<td><a class='INDICETIPO' id='indiceTipo' href='#".$row['tipoRichiesta']."'>".$row['tipoRichiesta']."</a></td>";
                        //echo "<td><a class='TIPO' id='tipo' href='#".$row['descrizione']."'>".$row['descrizione']."</a></td>";
                        echo "<td><span class='TIPO' id='tipo'>".$row['descrizione']."</span></td>";
                        if($row['note']!='')
                        {
                            echo "<td><button class='NOTE btn btn-sm btn-info' id='".$row['nProtocollo']."'>--></button>";
                        }
                        else echo "<td><span class='noNOTE'>___</span></td>";
                        //echo "<td><a class='NOTE' id='note' href='#".$row['note']."'>".$row['note']."</a></td>";
                        if($row['pathFile']!='')
                        {
                            echo "<td><button class='PATHFILE btn btn-sm btn-info' id='".$row['nProtocollo']."'>--></button>";
                        }
                        else echo "<td><span class='noFILE'>___</span></td>";
                        //echo "<td><a class='PATHFILE' id='pathfile' href='#".$row['pathFile']."'>".$row['pathFile']."</a></td>";
                        //
                        echo "<td><span class='DATA' id='data'>".$this->formattaData($row['dataApertura'])."</span></td>";
                        echo "</tr>";
                    }
             echo "</tbody>
                  </table>
                  </div>";    
            }
            else
            {
                echo "<span class='alert alert-warning'>Nessuna richiesta IMAC in DB..</span>";
            }
        }
    }
    
    public function stampaImacDaParametro($token,$param,$until=0)
    {
        if($param=='protocollo')
                $query="SELECT im.nProtocollo,im.ticket,im.matUtente,dip.cognome,dip.nome AS nome,im.tipoRichiesta,tipo.nome AS descrizione,im.pathFile,im.note,im.dataApertura 
                        FROM imac AS im JOIN dipendenti AS dip ON im.matUtente=dip.matricola JOIN tipo_richiesta AS tipo ON im.tipoRichiesta=tipo.id  WHERE im.nProtocollo='".$token."'";
        else if($param=='ticket')
                $query="SELECT im.nProtocollo,im.ticket,im.matUtente,dip.cognome,dip.nome AS nome,im.tipoRichiesta,tipo.nome AS descrizione,im.pathFile,im.note,im.dataApertura
                        FROM imac AS im JOIN dipendenti AS dip ON im.matUtente=dip.matricola JOIN tipo_richiesta AS tipo ON im.tipoRichiesta=tipo.id WHERE im.ticket='".$token."'";
        else if($param=='matricola')
                $query="SELECT im.nProtocollo,im.ticket,im.matUtente,dip.cognome,dip.nome AS nome,im.tipoRichiesta,tipo.nome AS descrizione,im.pathFile,im.note,im.dataApertura
                        FROM imac AS im JOIN dipendenti AS dip ON im.matUtente=dip.matricola JOIN tipo_richiesta AS tipo ON im.tipoRichiesta=tipo.id WHERE im.matUtente='".$token."'";
        else if($param=='data')
                $query="SELECT im.nProtocollo,im.ticket,im.matUtente,dip.cognome,dip.nome AS nome,im.tipoRichiesta,tipo.nome AS descrizione,im.pathFile,im.note,im.dataApertura
                        FROM imac AS im JOIN dipendenti AS dip ON im.matUtente=dip.matricola JOIN tipo_richiesta AS tipo ON im.tipoRichiesta=tipo.id WHERE im.dataApertura='".$token."'";
        else if($param=='range')
                $query="SELECT im.nProtocollo,im.ticket,im.matUtente,dip.cognome,dip.nome AS nome,im.tipoRichiesta,tipo.nome AS descrizione,im.pathFile,im.note,im.dataApertura
                        FROM imac AS im JOIN dipendenti AS dip ON im.matUtente=dip.matricola JOIN tipo_richiesta AS tipo ON im.tipoRichiesta=tipo.id WHERE im.dataApertura BETWEEN '".$token."' AND '".$until."' ORDER BY im.dataApertura DESC";
        
        if(!$res=$this->DBconn->query($query)) echo $this->DBconn->error;
        else
        {
            if($res->num_rows>=1)
            {
                echo "<div id='imacList' class='table-responsive'>
                          <table class='table'>
                             <thead>
                                 <tr>
                                 <th>PROTOCOLLO</th><th>TICKET</th><th>MATRICOLA</th><th>COGNOME,NOME</th><th>TIPO</th><th>NOTE</th><th>FILE</th><th>DATA</th>
                                 </tr>
                             </thead>
                             <tbody>";
                while($row=$res->fetch_assoc())
                {
                        echo "<tr>";
                        echo "<td><span class='NPROTOCOLLO' id='nProtocollo'>N".$row['nProtocollo']."</span></td>";
                        echo "<td><span class='TICKET' id='ticket'>".$row['ticket']."</span></td>";
                        echo "<td><a class='MATRICOLA' id='matricola' href='#".$row['matUtente']."'>".$row['matUtente']."</a></td>";
                        echo "<td><span class='COGNOME,NOME' id='cognome,nome'>".$row['cognome'].",".$row['nome']."</span></td>";
                        //echo "<td><a class='INDICETIPO' id='indiceTipo' href='#".$row['tipoRichiesta']."'>".$row['tipoRichiesta']."</a></td>";
                        //echo "<td><a class='TIPO' id='tipo' href='#".$row['descrizione']."'>".$row['descrizione']."</a></td>";
                        echo "<td><span class='TIPO' id='tipo'>".$row['descrizione']."</span></td>";
                        if($row['note']!='')
                        {
                            echo "<td><button class='NOTE btn btn-sm btn-info' id='".$row['nProtocollo']."'>--></button>";
                        }
                        else echo "<td><span class='noNOTE'>___</span></td>";
                        //echo "<td><a class='NOTE' id='note' href='#".$row['note']."'>".$row['note']."</a></td>";
                        if($row['pathFile']!='')
                        {
                            echo "<td><button class='PATHFILE btn btn-sm btn-info' id='".$row['nProtocollo']."'>--></button>";
                        }
                        else echo "<td><span class='noFILE'>___</span></td>";
                        //echo "<td><a class='PATHFILE' id='pathfile' href='#".$row['pathFile']."'>".$row['pathFile']."</a></td>";
                        //
                        echo "<td><span class='DATA' id='data'>".$this->formattaData($row['dataApertura'])."</span></td>";
                        echo "</tr>";
                }
                     echo "</tbody>
                          </table>
                          </div>";
            }
            else
            {
                echo "<span class='alert alert-danger'>Nessun risultato per il filtro impostato!</span>";
            }
        }
    }
    
    private function formattaData($data)
    {
        $token=explode("-",$data);
        $formattata=$token[2]."-".$token[1]."-".$token[0];
        return $formattata;
    }
}
?>
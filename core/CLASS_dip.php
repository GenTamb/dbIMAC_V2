<?php
require_once ("CLASS_db.php");

class DIPENDENTE
{
    public $matricola,$nome,$cognome,$DBconn;
    
    function __construct()
    {
        $this->DBconn=new DBconn();
    }
    
    function __destruct()
    {
        $this->DBconn->close();
    }
    
    public function getDipByMat($matricola)
    {
        $query="SELECT matricola,nome,cognome FROM dipendenti WHERE matricola='".$matricola."'";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else
        {
            if($res->num_rows!=0)
            {
                $dip=$res->fetch_assoc();
                $this->matricola=$dip['matricola'];
                $this->nome=$dip['nome'];
                $this->cognome=$dip['cognome'];
                $fine=true;
            }
            else $fine=false;
        }
        return $fine;
    }
    
   public function stampaTabellaDIPxEdit($token,$param)
    {
        if($param=='matricola')
                $query="SELECT matricola,cognome,nome FROM dipendenti WHERE matricola LIKE '".$token."%'";
        else if($param=='cognome')
                 $query="SELECT matricola,cognome,nome FROM dipendenti WHERE cognome LIKE '".$token."%'";
        
        if(!$res=$this->DBconn->query($query)) echo $this->DBconn->error;
        else
        {
            if($res->num_rows>=1)
            {
                echo "<div id='dipList' class='table-responsive'>
                          <table class='table'>
                             <thead>
                                 <tr>
                                 <th>MATRICOLA</th><th>Nuova Matricola</th><th>COGNOME</th><th>NOME</th><th>EDIT</th><th>DELETE</th>
                                 </tr>
                             </thead>
                             <tbody>";
                while($row=$res->fetch_assoc())
                {
                        echo "<tr>";
                        echo "<td><span class='MATRICOLAUTENTE' id='matUtenteRec'>".$row['matricola']."</span></td>";
                        echo "<td><button id='newMatButton' class='btn btn-sm btn-warning'>V</button>
                                  <input type='text' id='newMatToAdd' class='nascosto'>
                              </td>";
                        echo "<td><input type='text' class='COGNOMEUTENTE' id='cognomeUtenteRec' value='".$row['cognome']."'></td>";
                        echo "<td><input type='text' class='NOMEUTENTE' id='nomeUtenteRec' value='".$row['nome']."'></td>";
                        echo "<td><button id='EDITdiplist' class='btn btn-sm btn-info DIP-CD'>V</button></td>";
                        echo "<td><button id='DELdiplist' class='btn btn-sm btn-danger DIP-CD'>X</button></td>";
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
    
    public function getDipByCognome($cognome)
    {
        $query="SELECT matricola,nome,cognome FROM dipendenti WHERE cognome LIKE '".$cognome."%'";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else
        {
            if($res->num_rows!=0)
            {
                echo "<ul id='DIPlist'>";
                while($dip=$res->fetch_assoc())
                {
                 echo "<li><ul>";
                 echo "<li>span id='DIPmatricola'>".$dip['matricola']."</span></li>";
                 echo "<li><span id='DIPcognome'>".$dip['cognome']."</span></li>";
                 echo "<li><span id='DIPnome'>".$dip['nome']."</span></li>";
                 echo "</ul></li>";
                }
                $fine=true;
            }
            else $fine=false;
        }
        return $fine;
    }
    
    public function insertDip()
    {
        $this->toUpperAll();
        $query="INSERT INTO dipendenti (matricola,nome,cognome) VALUES ('".$this->matricola."','".$this->nome."','".$this->cognome."')";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else $fine=true;
        
        return $fine;
    }
    
    public function aggiornaDip($nuovaMat="")
    {
        $this->toUpperAll();
        if($nuovaMat!="")
        {
            $nuovaMat=strtoupper($nuovaMat);
            $query="UPDATE dipendenti SET matricola='".$nuovaMat."',nome='".$this->nome."',cognome='".$this->cognome."' WHERE matricola='".$this->matricola."'";
            
        }
        else $query="UPDATE dipendenti SET nome='".$this->nome."',cognome='".$this->cognome."' WHERE matricola='".$this->matricola."'";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else $fine=true;
        
        return $fine;
    }
    
    public function cancellaDip()
    {
        $query="DELETE FROM dipendenti WHERE matricola='".$this->matricola."'";
        $this->DBconn->query($query);
    }
    
    public function getHintList($mat)
    {
        $query="SELECT matricola,nome,cognome FROM dipendenti WHERE matricola LIKE '".$mat."%' ";
        if(!$res=$this->DBconn->query($query)) echo $this->DBconn->error;
        else
        {
            if($res->num_rows>0)
            {
                echo "<ul id='dipHints'><h3>Suggerimenti</h3>";
                while($utente=$res->fetch_assoc())
                {
                 echo "<li class='hintLI'><span id='hintMatUtenteSpan'>".$utente['matricola']."</span>: <span id='hintCognomeUtenteSpan'>".$utente['cognome']."</span>, <span id='hintNomeUtenteSpan'>".$utente['nome']."</span></li>";
                }
                echo "</ul>";
            }
            else echo "Nessun risultato";
        }
    }
    
    private function toUpperAll()
    {
        $this->matricola=strtoupper($this->matricola);
        $this->nome=strtoupper($this->nome);
        $this->cognome=strtoupper($this->cognome);
    }
    
    
    
    
}
    
    



?>
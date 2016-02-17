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
        $query="INSERT INTO dipendenti (matricola,nome,cognome) VALUES ('".$this->matricola."','".$this->nome."','".$this->cognome."')";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else $fine=true;
        
        return $fine;
    }
    
    public function aggiornaDip()
    {
         $query="UPDATE dipendenti SET nome='".$this->nome."',cognome='".$this->cognome."' WHERE matricola='".$this->matricola."'";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else $fine=true;
        
        return $fine;
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
    
    
    
    
}
    
    



?>
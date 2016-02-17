<?php

require_once("CLASS_db.php");

class TIPO
{
    public $id,$nome,$DBconn;
    
    function __construct()
    {
        $this->DBconn=new DBconn();
    }
    
    function __destruct()
    {
        $this->DBconn->close();
    }
    
    public function getTypeById($id)
    {
        $query="SELECT id,nome FROM tipo_richiesta WHERE id='".$id."'";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else
        {
            if($res->num_rows!=0)
            {
                $type=$res->fetch_assoc();
                $this->id=$type['id'];
                $this->nome=$type['nome'];
                $fine=true;
            }
            else $fine=false;
        }
        return $fine;
    }
    
    public function getTypeByNome($nome)
    {
        $query="SELECT id,nome FROM tipo_richiesta WHERE nome='".$nome."'";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else
        {
            if($res->num_rows!=0)
            {
                $type=$res->fetch_assoc();
                $this->id=$type['id'];
                $this->nome=$type['nome'];
                $fine=true;
            }
            else $fine=false;
        }
        return $fine;
    }
    
    public function insertType()
    {
        $query="INSERT INTO tipo_richiesta (nome) VALUES ('".$this->nome."')";
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
         $query="UPDATE tipo_richiesta SET nome='".$this->nome."' WHERE id='".$this->id."'";
        if(!$res=$this->DBconn->query($query))
        {
            $fine=false;
            echo $this->DBconn->error;
        }
        else $fine=true;
        
        return $fine;
    }
    
    public function stampaComboType()
    {
        $query="SELECT id,nome FROM tipo_richiesta";
        $res=$this->DBconn->query($query);
        echo "<select id='types'>";
        while($option=$res->fetch_assoc())
        {
            echo "<option class='selettore_tipo' value='".$option['id']."'>".$option['nome']."</option>";
        }
        echo "</select>";
    }
}













?>
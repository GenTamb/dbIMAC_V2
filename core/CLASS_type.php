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
        $this->toUpperAll();
        if(!$this->getTypeByNome($this->nome))
        {
            $query="INSERT INTO tipo_richiesta (nome) VALUES ('".$this->nome."')";
            if(!$res=$this->DBconn->query($query))
            {
                $fine=false;
                echo $this->DBconn->error;
            }
            else
            {
                $this->id=$this->DBconn->insert_id;
                $fine=true;
            }
            return $fine;
        }
        else return false;
        
    }
    
    public function cancellaType()
    {
        $query="DELETE FROM tipo_richiesta WHERE id='".$this->id."'";
        $this->DBconn->query($query);
    }
    
    public function aggiornaType()
    {
        $this->toUpperAll();
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
    
    public function stampaSelectedComboType($tipo)
    {
        $query="SELECT id,nome FROM tipo_richiesta";
        $res=$this->DBconn->query($query);
        echo "<select id='types'>";
        while($option=$res->fetch_assoc())
        {
            echo "<option class='selettore_tipo' value='".$option['id']."' ";
            if($option['id']==$tipo) echo "selected='selected'";
            echo ">".$option['nome']."</option>";
        }
        echo "</select>";
    }
    
    public function stampaTypeListXedit()
    {
        $query="SELECT id,nome FROM tipo_richiesta";
        $res=$this->DBconn->query($query);
        if(!$res=$this->DBconn->query($query)) echo $this->DBconn->error;
        else
        {
            if($res->num_rows>=1)
            {
                echo "<div id='typeList' class='table-responsive'>
                          <table class='table'>
                             <thead>
                                 <tr>
                                 <th>ID</th><th>DESCRIZIONE</th><th>NUOVA DESCRIZIONE</th><th>EDIT</th><th>DELETE</th>
                                 </tr>
                             </thead>
                             <tbody>";
                while($row=$res->fetch_assoc())
                {
                        echo "<tr>";
                        echo "<td><span class='IDTYPE' id='idtype'>".$row['id']."</span></td>";
                        echo "<td><span class='DESCRIZIONE' id='descrizioneStored'>".$row['nome']."</span></td>";
                        echo "<td><input type='text' class='DESCRIZIONE' id='descrizioneNew' value=''></td>";
                        echo "<td><button id='EDITtypelist' class='btn btn-sm btn-info TYPE-CD'>V</button></td>";
                        echo "<td><button id='DELtypelist' class='btn btn-sm btn-danger TYPE-CD'>X</button></td>";
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
    private function toUpperAll()
    {
        $this->nome=strtoupper($this->nome);
    }
}













?>
<?php

include_once "CLASS_db.php";

class UTENTE
{
  public $username,$password,$nome,$cognome,$admin,$dbconn;
  
  function __construct($username,$password,$nome,$cognome,$admin)
  {
    $this->dbconn=new DBconn();
    $this->username=$username;
    $this->password=$password;
    $this->nome=$nome;
    $this->cognome=$cognome;
    $this->admin=$admin;
    $this->saltingPassword();
  }
  
  function __destruct()
  {
    $this->dbconn->close();
  }
  
  public function saltingPassword()
  {
    $token=SALT.$this->password;
    $this->password=hash('ripemd128',"$token");
  }
  
  public function salt($something)
  {
    $token=SALT.$something;
    $something=hash('ripemd128',"$token");
    return $something;
  }

  public function inserisciUtente()
  {
    $query="INSERT INTO utenti (username,password,nome,cognome,admin) VALUES ('".$this->username."','".$this->password."','".$this->nome."','".$this->cognome."','".$this->admin."')";
    ($this->dbconn->query($query)) ? $res=true : $res=false;
    return $res;
  }
  
  public function adminAggiornaUtente($token,$param)
  {
    switch($param)
    {
      case "username":
        $query="UPDATE utenti SET username='".$token."' WHERE username='".$this->username."'";
        break;
      case "nome":
        $query="UPDATE utenti SET nome='".$token."' WHERE username='".$this->username."'";
        break;
      case "cognome":
        $query="UPDATE utenti SET cognome='".$token."' WHERE username='".$this->username."'";
        break;
      case "password": 
       $query="UPDATE utenti SET password='".$token."' WHERE username='".$this->username."'";
       break;
      case "admin": 
       $query="UPDATE utenti SET admin='".$token."' WHERE username='".$this->username."'";
       break;
    }
    if(!$res=$this->dbconn->query($query)) $fine=false;
    else $fine=true;
    return $fine;
  }
  
  public function userAggiornaPassword($oldPass,$newPass)
  {
    /*$getData="SELECT password FROM utenti WHERE username='".$this->username."'";
    if(!$res=$this->dbconn->query($getData)) $return=false;
    else
    {
      $row=$res->fetch_assoc();
      $retriviedPassword=$row['password'];
      if($retriviedPassword==$this->password)
      {
        $token=SALT.$newPass;
        $newPass=hash('ripemd128',"$token");
        $updatePass="UPDATE utenti SET password='".$newPass."' WHERE username='".$this->username."'";
        $return=true;
      }
      else $return=false;
    }
    return $return;*/
    $fine=false;
    $oldPass=$this->salt($oldPass);
    if($oldPass==$this->password)
    {
      $newPass=$this->salt($newPass);
      $updatePass="UPDATE utenti SET password='".$newPass."' WHERE username='".$this->username."'";
      if(!$res=$this->dbconn->query($updatePass)) $fine=false;
      else $fine=true;
    }
    return $fine;
  }
  
  public function cancellaUtente()
  {
    $query="DELETE FROM utenti WHERE username='".$this->username."'";
    ($this->dbconn->query($query)) ? $res=true : $res=false;
    return $res;
  }
  
  public function stampaUserListXedit()
  {
    $query="SELECT username,nome,cognome,admin FROM utenti WHERE 1";
    if(!$res=$this->dbconn->query($query)) $fine=false;
    else
    {
        if($res->num_rows>0)
        {
          $fine=true;
          echo "<div id='userList' class='table-responsive'>
                            <table class='table'>
                               <thead>
                                   <tr>
                                   <th>USERNAME</th><th>New USERNAME</th><th>New PASSWORD</th><th>COGNOME</th><th>New COGNOME</th><th>NOME</th><th>New NOME</th><th>ADMIN</th><th>EDIT</th><th>DELETE</th>
                                   </tr>
                               </thead>
                               <tbody>";
          while($row=$res->fetch_assoc())
          {
            echo "<tr>";
            echo "<td><span class='USERNAME' id='userName'>".$row['username']."</span></td>";
            echo "<td><button id='newUserNameButton' class='NEWVALUEUSER btn btn-sm btn-warning'>V</button>
                      <input type='text' id='newUserName' class='nascosto'>
                  </td>";
            echo "<td><button id='newUserPasswordButton' class='NEWVALUEUSER btn btn-sm btn-warning'>V</button>
                      <input type='password' id='newUserPassword' class='nascosto'>
                  </td>";
            echo "<td><span class='USERCOGNOME' id='userCognome'>".$row['cognome']."</span></td>";
            echo "<td><button id='newUserCognomeButton' class='NEWVALUEUSER btn btn-sm btn-warning'>V</button>
                      <input type='text' id='newUserCognome' class='nascosto'>
                  </td>";
            echo "<td><span class='USERNOME' id='userNome'>".$row['nome']."</span></td>";
            echo "<td><button id='newUserNomeButton' class='NEWVALUEUSER btn btn-sm btn-warning'>V</button>
                      <input type='text' id='newUserNome' class='nascosto'>
                  </td>";
            echo "<td>";
                 $this->stampaSelectAdmin($row['admin']);
            echo "</td>";     
            echo "<td><button id='EDITusrlist' class='btn btn-sm btn-info USR-CD'>V</button></td>";
            echo "<td><button id='DELusrlist' class='btn btn-sm btn-danger USR-CD'>X</button></td>";
            echo "</tr>";
          }
            echo "</tbody>
                  </table>
                  </div>";
          }
        else
        {
          echo "<span class='alert alert-danger'>Nessun risultato per il filtro impostato!</span>";
          $fine=false;
        }
      }
      return $fine;
  }
  
  public function getUsersCount($admin=0)
  {
    if($admin==0) $query="SELECT COUNT(username) AS quanti FROM utenti WHERE 1";
    else $query="SELECT COUNT(username) AS quanti FROM utenti WHERE admin='1'";
    $res=$this->dbconn->query($query);
    $row=$res->fetch_assoc();
    return $row['quanti'];
  }

  private function stampaSelectAdmin($value)
  {
    echo "<select id='userAdmin'>
          <option value='1'";
          if($value==1) echo "selected='selected'";
    echo ">SI</option>";
    echo "<option value='0'";
          if($value==0) echo "selected='selected'";
    echo ">NO</option>";
    echo "</select";
  }
  
  public function checkLogin()
  {
    $query="SELECT username,password,nome,cognome,admin FROM utenti WHERE username='".$this->username."' AND password='".$this->password."'";
    if(!$res=$this->dbconn->query($query)) echo $this->dbconn->error;
    else
    {
      if($res->num_rows==1)
      {
        $fine=true;
        while($user=$res->fetch_assoc())
        {
            $this->nome=$user['nome'];
            $this->cognome=$user['cognome'];
            $this->admin=$user['admin'];
        }
      }
      else
      {
        $fine=false;
      }
    }
    return $fine;
  }
  
  public function passa_a_SESSION()
  {
    $_SESSION['usernameDBIMACV2']=$this->username;
    $_SESSION['nome']=$this->nome;
    $_SESSION['cognome']=$this->cognome;
    $_SESSION['admin']=$this->admin;
    $_SESSION['password']=$this->password;
  }
  
  public function passa_da_SESSION()
  {
    $this->username=$_SESSION['usernameDBIMACV2'];
    $this->nome=$_SESSION['nome'];
    $this->cognome=$_SESSION['cognome'];
    $this->admin=$_SESSION['admin'];
    $this->password=$_SESSION['password'];
  }
  
  public function recuperaUtenteBy($token)
  {
    
  }
  
}









?>
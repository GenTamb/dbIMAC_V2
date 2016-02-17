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

  public function inserisciUtente()
  {
    $query="INSERT INTO utenti (username,password,nome,cognome,admin) VALUES ('".$this->username."','".$this->password."','".$this->nome."','".$this->cognome."','".$this->admin."')";
    ($this->dbconn->query($query)) ? $res=true : $res=false;
    return $res;
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
    $_SESSION['username']=$this->username;
    $_SESSION['nome']=$this->nome;
    $_SESSION['cognome']=$this->cognome;
    $_SESSION['admin']=$this->admin;
  }
  
  public function passa_da_SESSION()
  {
    $this->username=$_SESSION['username'];
    $this->nome=$_SESSION['nome'];
    $this->cognome=$_SESSION['cognome'];
    $this->admin=$_SESSION['admin'];
  }
  
  public function recuperaUtenteBy($token)
  {
    
  }
  
}









?>
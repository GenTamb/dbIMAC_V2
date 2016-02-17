<?php
require_once "db.php";

class DBconn extends mysqli
{
    public $DB=DB,$HOST=HOST,$USER=USER,$PSW=PSW,$SALT=SALT,$ROOT=ROOT;
    
    function __construct()
    {
        parent::__construct($this->HOST,$this->USER,$this->PSW,$this->DB);
    }
    
    function __destruct()
    {
        $this->close();
    }
    
}





?>
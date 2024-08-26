<?php
class User {
    private $id;

    public $login; 

    public $email;

    public $firstname;

    public $lastname;
    
    public function __construct() {
        $this->id = 0;
        $this->login = '';
        $this->email = '';
        $this->firstname = '';
        $this->lastname = '';
    }
}


?>
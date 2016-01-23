<?php

/**
*
* This file is part of the TradyCloud Software package.
*
*
* Author: Mathis André
*
*/

session_start();
header('Access-Control-Allow-Origin: *');

require 'autoload.php';

$name = trim($_POST['name']);
$email = trim($_POST['email']);
$pass = trim($_POST['pass']);

use Parse\ParseClient;
use Parse\ParseObject;
use Parse\ParseQuery;

ParseClient::initialize('x', 'y', 'z');

class Register
{
    private $email;
    private $name;
    private $pass;
    private $firstname;
    private $m_query = new ParseQuery("users");

    public function __construct($email, $name, $pass)
    {
        $this->email = $email;
        $this->name = $name;
        $this->pass = $pass;
    }

    private function sliceName()
    {
        $fullName = explode(" ", $this->name);
        
        count($fullName) > 1 ? return $fullName : return $this->name;
    }

    public function addUser()
    {
        if(!empty($this->email) && !empty($this->name) && !empty($this->pass))
        {
          if(!filter_var($this->email, FILTER_VALIDATE_EMAIL)){
                $datas = 'Please enter a valid email address.';
          }
          else {
                $tmpSlice = $this->sliceName();
                $this->firstname = is_array($tmpSlice) ? $tmpSlice[0] : 0;

                $this->m_query->equalTo('email', $this->email);
                $results = $this->m_query->find();

                if (count($results) > 0)
                    $datas = 'Email already exists.';
                else
                {
                    $userObject = ParseObject::create("users");
                    $userObject->set("email", $this->email);
                    $userObject->set("name", $this->name);
                    $userObject->set("firstname", $this->firstname);
                    $userObject->set("pass", sha1($this->pass));
                    $userObject->save();

                    $this->update_session_id();
                    $datas = 'success_reg';
                }
            }
            return $datas;
        }
    }

    private function update_session_id()
    {
      $this->m_query->equalTo('email', $this->email);
      $resultsUser = $this->m_query->find();
      if (count($resultUser) > 0)
        $_SESSION['id'] = $resultsUser[0]->getObjectId();
    }
}

$user = new Register($email, $name, $pass);

echo $user->addUser();

?>

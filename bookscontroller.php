<?php

require_once('../PHPLIB/services/config.php');

class BooksController
{

  function __construct($db){

}
  
  public function createPDO(){
    // db connect
    try{
      $db = new PDO('mysql:host='.DBHOST.';dbname='.DBNAME.'', DBUSER, DBPASS);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // global exception
    } catch (PDOException $e) {
      echo "Could not connect to database: " . $e->getMessage() . "<br>\n";
    }
    return $db;
  }
  
  public function GETAction($request) {
    $books = $this->readBooks($request->url_elements);
    return $books;
  }

  public function POSTAction($request) {
    // insert event
    // sanitize input
  }

  public function PUTAction($request) {
    // update event
    // sanitize input   
  }

  public function DELETEAction($request) {
    // delete event
    // sanitize input
  }

  protected function readBooks($url_elements) {
    $db = $this->createPDO();
    if(empty($url_elements[2])){
      $sql = "SELECT * FROM books";
      $result = $db->query($sql);
      $books = $result->fetchALL(PDO::FETCH_ASSOC);
    }else{
      $sql = $db->prepare("SELECT * from books where id = ?");
      $id = $url_elements[2];
      $sql->execute(array($id));
      $books = $sql->fetchALL(PDO::FETCH_ASSOC);
    }
    
    return json_encode($books);
  }

  protected function writeBooks($books) {
    $db = $this->createPDO();
    //write entries here
  }
}

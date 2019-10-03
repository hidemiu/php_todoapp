<?php

// CSRF対策
// Publish Token and store in Session
// Also publish Token from form and send it
// Check

namespace MyApp;

class Todo {
  private $_db;

  public function __construct() {
    // Create Token
    $this->_createToken();
    
    // PDO, access DB
    try {
      $this->_db = new \PDO(DSN, DB_USERNAME, DB_PASSWORD);
      $this->_db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    } catch (\PDOException $e) {
      echo $e->getMessage();
      exit;
    }
  }
  
  private function _createToken() {
    if (!isset($_SESSION['token'])) {
      $_SESSION['token'] = bin2hex(openssl_random_pseudo_bytes(16));
    }
  }

  public function getAll() {
    $stmt = $this->_db->query("select * from todos order by id desc");
    return $stmt->fetchAll(\PDO::FETCH_OBJ);
  }
  
  public function post() {
    // Validate Token
    $this->_validateToken();
    
    if (!isset($_POST['mode'])) {
      throw new \Exception('mode not set!');
    }
    
    switch ($_POST['mode']) {
      case 'update':
        return $this->_update();
      case 'create':
        return $this->_create();
      case 'delete':
        return $this->_delete();
    }
  }
  
  private function _validateToken() {
    if (
      !isset($_SESSION['token']) ||
      !isset($_POST['token']) ||
      $_SESSION['token'] !== $_POST['token']
    ) {
      throw new \Exception('invalid token!');
    }
  }
  
  private function _update() {
    if (!isset($_POST['id'])) {
      throw new \Exception('mode not set!');
    }
    
    $this->_db->beginTransaction();
    
    // Update DB
    // When state is 1, set 0. When state is 0, set 1. This is click action.
    $sql = sprintf("update todos set state = (state + 1) %% 2 where id = %d", $_POST['id']);

    $stmt = $this->_db->prepare($sql);
    $stmt = $stmt->execute();
    
    // Get updated data from DB
    $sql = sprintf("select state from todos where id = %d", $_POST['id']);
    $stmt = $this->_db->query($sql);
    $state = $stmt->fetchColumn();
    
    $this->_db->commit();
    
    return [
      'state' => $state
    ];    
  }
  
  private function _create() {
    if (!isset($_POST['title']) || $_POST['title'] === '') {
      throw new \Exception('[create] title not set!');
    }
        
    // Insert into DB
    $sql = "insert into todos (title) values (:title)";
    $stmt = $this->_db->prepare($sql);
    $stmt = $stmt->execute([':title' => $_POST['title']]);
            
    return [
      'id' => $this->_db->lastInsertId()
    ];
  }
  
  private function _delete() {
    if (!isset($_POST['id'])) {
      throw new \Exception('[delete] id not set!');
    }
        
    // Delete from DB
    $sql = sprintf("delete from todos where id = %d", $_POST['id']);
    $stmt = $this->_db->prepare($sql);
    $stmt = $stmt->execute();
            
    return [];
  }

}
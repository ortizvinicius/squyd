<?php 

namespace squyd;

class DbAccess {
    
  private $conex;
  
  function __construct () {
    $this->conex = new PDO(DBCONN, DBUSER, DBPASS);
  }
  
  public function execQuery ($query, $params, $uni == null) {
    try {  
      $sqlQuery = $this->conex->prepare($query);
      
      foreach($params as $i => &$param){
        $i++;
        $sqlQuery->bindParam($i, $param);
      }
      
      $sqlQuery->execute();
      
      if($uni != null && $uni){
        return $sqlQuery->fetch();   
      } else if($uni != null) {
        return $sqlQuery->fetchAll();
      } else {
        return true;
      }
    } catch (Exception $e){
      return $e->getMessage();
    }
  }

  public function select () {

  }

  public function delete () {

  }

  public function insert () {

  }

  public function update () {
    
  }
    
}

?>
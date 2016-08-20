<?php 

namespace squyd;

class DbAccess {
    
  protected $conex,
            $tableName,
            $tableFields;
  
  /* @param {string} tableName
   * @param {array} tableFields
   */
  function __construct ($tableName, $tableFields) {
    
    $this->conex = new PDO(DBCONN, DBUSER, DBPASS);

    $this->tableName = $tableName;
    $this->tableFields = $tableFields;

  }
  
  public function execQuery ($query, $params, $single = null) {
    try {  
      $sqlQuery = $this->conex->prepare($query);
      
      foreach($params as $i => &$param){
        $i++;
        $sqlQuery->bindParam($i, $param);
      }
      
      $sqlQuery->execute();
      
      if($single != null && $single){
        return $sqlQuery->fetch();   
      } else if($single != null) {
        return $sqlQuery->fetchAll();
      } else {
        return true;
      }
    } catch (Exception $e){
      return $e->getMessage();
    }
  }

  /* @param {array} options
   */
  public function select ($options = []) {

    $options["fields"] = isset($options["fields"]) && $options["fields"] ? $options["fields"] : $this->tableFields;
    $options["join"]   = isset($options["join"])   && $options["join"]   ? $options["join"]   : [];
    $options["where"]  = isset($options["where"])  && $options["where"]  ? $options["where"]  : [];
    $options["order"]  = isset($options["order"])  && $options["order"]  ? $options["order"]  : "";
    $options["group"]  = isset($options["group"])  && $options["group"]  ? $options["group"]  : "";
    $options["limit"]  = isset($options["limit"])  && $options["limit"]  ? $options["limit"]  : "";
    $options["single"] = isset($options["single"]) && $options["single"] ? $options["single"] : false;

    $lastFieldsKey = array_pop(array_keys($options["fields"]));

    $params = [];

    $query = "SELECT ";

    foreach ($options["fields"] as $key => $field) {
      $query .= $field;
      $query .= $key != $lastFieldsKey ? ", " : "";
    }

    $query .= " FROM " . $this->tableName;

    foreach ($options["join"] as $type => $join) {
      $query .= " " . $type . " JOIN ";
      $query .= $join["table"] . " ON (";
      $query .= $join["on"] . ") ";
    }

    foreach ($options["where"] as $where) {
      $query .= $where["field"] . " " . $where["cond"] . " ? ";
      array_push($params, $where["value"]);
    }

    if($options["order"]){
      $query .= " ORDER BY ? ";
      array_push($params, $options["order"]);
    }

    if($options["group"]){
      $query .= " GROUP BY ? ";
      array_push($params, $options["group"]);
    }

    if($options["limit"]){
      if(isset($options["limit"][0]) && isset($options["limit"][1])){
        $query .= " LIMIT ?, ? ";
        array_push($params, $options["limit"][0]);
        array_push($params, $options["limit"][1]);
      }
    } else {
      $query .= " LIMIT ? ";
      array_push($params, $options["limit"]);
    }

    return $this->execQuery($query, $params, $options["single"]);

  }

  public function delete () {

  }

  public function insert () {

  }

  public function update () {
    
  }
    
}

?>
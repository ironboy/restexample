<?php
/*
  A generic REST-api helper, v 2.0

  © Thomas Frank, Nodebite AB, 2014
*/

class RestAPI {

  protected $settings;

  protected function readRawInput(){
    // read raw ajax input from the client
    // - no form-encoding, Angular standard,
    // to use with jQuery.ajax:
    // set processData: false
    // and data: JSON.stringify(object)
    $rawInput = file_get_contents("php://input");
    // we presume that the input is valid json
    // and transform it to an associative array
    return json_decode($rawInput,true);
  }

  protected function connectToDatabase(){
   
    // settings for database connection
    $settings = $this -> settings;
    $host = isset($settings["dbhost"]) ? $settings["dbhost"] : "localhost";
    $dbname = isset($settings["dbname"]) ? $settings["dbname"] : "resttest";
    $user = isset($settings["dbuser"]) ? $settings["dbuser"] : "root";
    $pass = isset($settings["dbpwd"]) ? $settings["dbpwd"] : "mysql";

    // connect to our database via PDO
    $dbh = new PDO(
      "mysql:host=$host",
      $user,
      $pass,
      // this FORCES MySQL to use UTF-8
      // - cosy since it minimizes problems 
      // with non-English characters
      array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
    );

    // create the database if it does not exist
    $dbh->exec("CREATE DATABASE IF NOT EXISTS `$dbname`;");

    // switch to the desired database
    $dbh->query("use `$dbname`");

    // return handle to database
    return $dbh;
  }

  public function fixNumerics($result){
    // PDO has the bad habit to treat numbers as strings
    // let us fix it and turn numbers back to numbers
    foreach ($result as &$row) {
      foreach ($row as $key => &$val) {
        if (is_numeric($val)) {
          $row[$key] = (float) $val;
        }
      }
    }
    return $result;
  }

  public function performQuery($sql){
    // if debugging/setting says so - return the sql query
    // as a response header
    if(
      isset($this -> settings["sqlQueryAsResponseHeader"]) &&
      $this -> settings["sqlQueryAsResponseHeader"]
    ){
       header("X-Sql-Query: $sql");
    }
    // perform the query
    $query = $this->PDO->prepare($sql);
    $query->execute();
    // just fetch aresult if we do a select
    if(stripos($sql,'SELECT') === 0){
      $result = $this->fixNumerics($query->fetchAll(PDO::FETCH_ASSOC));
      return $result;
    }
    // store how many rows that were affected by the question
    $this -> rowsAffected = $query -> rowCount();
    // otherwise just return true
    return true;
  }

  protected function requestURL(){
    // get the requested url
    $url = utf8_decode(urldecode($_SERVER["REQUEST_URI"]));

    // base path to this file
    $path = str_replace($_SERVER['DOCUMENT_ROOT'],'',$_SERVER['PHP_SELF']);
    $path = str_replace(array_pop(explode("/",__FILE__)),'',$path);
    
    // remove the base path from the url
    $url = str_replace($path,'',$url);

    // return the url stripped of the base path
    return $url;
  }

  protected function requestMethod(){
    // check which method that is used for the call
    // (POST, GET, PUT, DELETE)
    // equivalent to Create, Read, Update, Delete (CRUD)
    return $_SERVER['REQUEST_METHOD'];
  }

  protected function analyzeURL(){

    // split the url into fragments based on slashes
    $urlFragments = explode("/",$this -> requestURL());

    // remove trailing slashes if they exist
    while($urlFragments[count($urlFragments)-1] == "" && count($urlFragments)){
      array_pop($urlFragments);
    }

    // nullify entity type and where
    $type = NULL;
    $where = NULL;
    
    // if there are no fragments - do not do anything more
    if(!count($urlFragments)){
      return;
    }

    // assume the first fragment is an entity type
    $type = $urlFragments[0];

    // do not allow numeric entity type values
    if(is_numeric($type)){
      return;
    }

    // always convert the entity name into plural
    if(substr($type,-1) != "s"){
      $type .= "s";
    }

    // if there is just one more fragment and the last one is numeric
    // then assume it is an id
    if(count($urlFragments) == 2 && is_numeric($urlFragments[1])){
      $where = "id = $urlFragments[1]";
      $this -> entityId = $urlFragments[1];
    }

    // see the following url in order to stand comparison operators:
    // http://docs.mongodb.org/manual/reference/operator/query-comparison/
    $comparisonOperators = array(
      '-eq-' => '=',
      '-gt-' => '>',
      '-gte-' => '>=',
      '-in-' => '?',
      '-lt-' => '<',
      '-lte-' => '<=',
      '-ne-' => '!=',
      '-like-' => 'LIKE'
    );
    $comparisonOperatorsKeys = array_keys($comparisonOperators);
    $andor = "";
    for($i = 1; $i < count($urlFragments); $i+=2){
      $compare = "=";
      $extras = "";
      // break if there is not a value...
      if(!isset($urlFragments[$i+1])){break;}
      // add an and/or statement if needed
      if($where){$where .= $andor;}
      $theField = $urlFragments[$i];
      $theValue = $urlFragments[$i+1];
      // check for comparison operators
      if(
        isset($urlFragments[$i+1]) 
        && in_array($urlFragments[$i+1],$comparisonOperatorsKeys)
      ){
        $compare =   $comparisonOperators[$urlFragments[$i+1]];
        $theValue = $urlFragments[$i+2];
        $i++;
      }
     
      // check for an or operator
      if(isset($urlFragments[$i+2]) && $urlFragments[$i+2] == "-or-"){
        $andor = " || ";
        $i++;
      }
      // check for anything else enclosed in hyphens
      // and if found assume the where statements has ended
      // and translate the rest more literally to sql
      else if(
        substr($urlFragments[$i+2],-1) == "-" &&
        substr($urlFragments[$i+2],0,1) == "-"
      ){
        for($j = $i+2; $j < count($urlFragments); $j++){
          $extras .= ($j > $i+3 ? " " : "").str_replace("_",", ",
            str_replace("-"," ",$urlFragments[$j])
          );
        }
        $i = $j;
        $andor = "";
      }
      // else assume and
      else {
        $andor = " && ";
      }
      // convert numeric values to numbers
      if(!is_numeric($theValue)){$theValue = '"'.$theValue.'"';}
      // add to where statement
      $where .= $theField.' '.$compare.' '.$theValue.$extras;
    }

    // set entity and id as properties of the current instance
    $this -> entityType = $type;
    $this -> where = $where;
  }

  // table to translate request methods to basic SQL commands
  protected $crudTranslateMethodsTable = array(
    "GET" => "SELECT",
    "POST" => "INSERT",
    "PUT" => "UPDATE",
    "DELETE" => "DELETE"
  );

  protected function action(){
    // find our basic sql-kommando
    $sqlCommand = $this -> crudTranslateMethodsTable[$this -> requestMethod()];
   
    // delete, update, insert
    if($sqlCommand == "DELETE"){$this -> delete();}
    if($sqlCommand == "INSERT"){$this -> insert();}
    if($sqlCommand == "UPDATE"){$this -> update();}

    // build or select question (don't use where if nt set)
    // - always build a SELECT (to be used to return a result
    // even on create or update)
    $sql = "SELECT * FROM $this->entityType"
      .($this->where === null ? "" : " WHERE $this->where");
    // fetch our result / ask the sql question
    $result =  $this -> performQuery($sql);
    // if there is no found result or an error
    // then set the result to null
    if(!count($result) || isset($this -> error)){
      $result = null;
      if(!isset($this -> error)){
        $this -> error = "HTTP/1.0 404 Not Found";
      }
    }

    // if entityId exist then just return ONE object
    if($result !== null && $this -> entityId !== null){
      $result = $result[0];
    }

    // return the result
    return $result;
  }

  protected function delete(){
    // we choose to demand an id for delete 
    //(not allowing the deletion of all instances of entity at once )
    if($this -> entityId === null){
      $this -> error = "HTTP/1.0 403 Forbidden (provide an id for DELETE)";
      return null;
    }
    // create and run the sql question
    $sql = "DELETE FROM $this->entityType WHERE id=$this->entityId";
    $this -> performQuery($sql);
    // if we succeed we will modify the 404-error to point this out
    if($this -> rowsAffected){
      $this -> error = "HTTP/1.0 404 Not Found (DELETED by this request)";
    }
  }

  protected function insert(){
    $input = $this -> inputData;
    // we choose to demand that you will NOT use an id when creating a new post
    // (we want the database to handle the creation of id:s)
    if($this -> entityId !== null){
      $this -> error = "HTTP/1.0 403 Forbidden (do NOT provide an id for POST/CREATE)";
      return null;
    }
    // create and run the sql question
    $sql = "INSERT INTO $this->entityType (".implode(", ",array_keys($input)).") ".
      'VALUES ("'.implode('","',array_values($input)).'")';
    $this -> performQuery($sql);
    // if we fail to create a new post, then modify the 404 error to point this out
    if(!$this -> rowsAffected){
      $this -> error = "HTTP/1.0 403 Forbidden (POST/CREATE failed, check your input)";
    }
    // otherwis redirect to the newly created post (perform a corresponding GET-request)
    else {
      $id = $this -> performQuery(
        "SELECT id FROM $this->entityType ORDER BY id DESC limit 1"
      );
      $redirectTo = rtrim($this -> requestURL(),"/")."/".$id[0]["id"];
      header("Location: $redirectTo");
    }
  }

  protected function update(){
    $input = $this -> inputData;
    // we choose to demand an id for updates 
    // (not allowing you to update all post at once)
    if($this -> entityId === null){
      $this -> error = "HTTP/1.0 403 Forbidden (provide an id for PUT/UPDATE)";
      return null;
    }
    // create and run the sql question
    $sql = "UPDATE $this->entityType SET ";
    foreach ($input as $fieldname => $fieldval){
      $sql.= "$fieldname = \"$fieldval\", ";
    }
    $sql = rtrim($sql,", ")." WHERE id=$this->entityId";
    $this -> performQuery($sql);
  }

  // construktor (run when we create a new instance)
  public function __construct($settings = array()){
    // transfer the $settings to a property
    $this -> settings = $settings;
    // check our input (and remove id from the json,
    // since we want to recieve the id as part of the url)
    $this -> inputData = $this -> readRawInput();
    if($this -> inputData === null){$this -> inputData = array();}
    unset($this -> inputData["id"]);
    // connect to the database
    $this -> PDO = $this -> connectToDatabase();
    // analyze our url
    $this -> analyzeURL(); // sets the properties entityType and entityId
    // do our job properly depending on url and request method
    $result = $this -> action();
    // return a result or an error message
    if($result !== null){
      // return the result as json
      echo(json_encode($result));
    }
    else {
      // or return a proper http error
      header($this -> error); 
    }
  }

}

// create a new instance of the api
// ...since this file (in its own folder together with an .htaccess file 
// is usually all we need) we relax our coding style here 
// and allow for instantiation in the same file as the class itself...
// (also looking for a settings.json file for db-settings)
new RestApi(
  file_exists("settings.json") ? 
    json_decode(file_get_contents("settings.json"),1) :
    array()
);
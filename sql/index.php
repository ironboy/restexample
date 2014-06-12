<?php
// Parsing dbquestions in json format...
// Returning json answers...
// (c) Thomas Frank, Nodebite AB, November 2013, v 0.1
//
// DO NOT TOUCH this code if you don't know what you're doing!
// Instead add your SQL questions to dbquestions.sql

$c = json_decode(file_get_contents("dbquestions.json"),true);
if($c === null){
  $result = array("error" => "invalid json in dbquestions.json");
}
else {
  $result = array("error" => "action not found");
}

// connect
$dns = "mysql:dbname=".$c["database"].";".
  "host=".$c["host"].";charset=utf8";
$myPDO = new PDO($dns , $c["username"], $c["password"]);

// check for action
$a = isset($_REQUEST["action"]) ? $_REQUEST["action"] : "";

// perform action
if(isset($c[$a])){
  $result = array();
  $q = $c[$a];
  $sql = "";
  foreach($q as $p){
    if(is_array($p)){
      if(isset($p[0]) && is_array($p[0])){
        $sql.='"';  
        foreach($p[0] as $el){
          if(isset($_REQUEST[$el])){
            $sql .= $_REQUEST[$el];
          }
          else if ($el == "%") {
            $sql .= "%";
          }
          else {
            $result["error"] = 'Parameter [['.$el.']] not found.';
            break;
          }
        }
        $sql.='"';
      }
      else {
        if(isset($_REQUEST[$p[0]])){
          $sql .= '"'.$_REQUEST[$p[0]].'"';
        }
        else {
          $result["error"] = 'Parameter ['.$p[0].'] not found.';  
          break;
        }
      }
    }
    else {
      $sql .= str_replace(";","*new sqlstatement*",$p);
    }
  }

  // ask database
  if(!isset($result["error"])){
    $sqls = explode("*new sqlstatement*",$sql);
    foreach($sqls as $sql){
      $statement = $myPDO -> prepare($sql);
      $statement -> execute();
    }
    $result = $statement -> fetchALL(PDO::FETCH_ASSOC);
  }
}

if(isset($result["error"])){
  header("HTTP/1.0 400 Malformed");
}
header('Content-Type: application/json');
echo(indent(json_encode($result)));



/**
 * Indents a flat JSON string to make it more human-readable.
 *
 * @param string $json The original JSON string to process.
 *
 * @return string Indented version of the original JSON string.
 *
 * Dave Perrett : http://www.daveperrett.com/articles/2008/03/11/format-json-with-php/in
 */
function indent($json) {

    $result      = '';
    $pos         = 0;
    $strLen      = strlen($json);
    $indentStr   = '  ';
    $newLine     = "\n";
    $prevChar    = '';
    $outOfQuotes = true;

    for ($i=0; $i<=$strLen; $i++) {

        // Grab the next character in the string.
        $char = substr($json, $i, 1);

        // Are we inside a quoted string?
        if ($char == '"' && $prevChar != '\\') {
            $outOfQuotes = !$outOfQuotes;

        // If this character is the end of an element,
        // output a new line and indent the next line.
        } else if(($char == '}' || $char == ']') && $outOfQuotes) {
            $result .= $newLine;
            $pos --;
            for ($j=0; $j<$pos; $j++) {
                $result .= $indentStr;
            }
        }

        // Add the character to the result string.
        $result .= $char;

        // If the last character was the beginning of an element,
        // output a new line and indent the next line.
        if (($char == ',' || $char == '{' || $char == '[') && $outOfQuotes) {
            $result .= $newLine;
            if ($char == '{' || $char == '[') {
                $pos ++;
            }

            for ($j = 0; $j < $pos; $j++) {
                $result .= $indentStr;
            }
        }

        $prevChar = $char;
    }

    return $result;
}
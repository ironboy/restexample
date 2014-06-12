<?php

/*
  CALJAC
  - Collect And Load JavaScript And CSS

  Simple collector of css and js files
  (c) TF 2013
*/

ini_set('max_execution_time', 300);
$settingspath = "../../settings/app.json";
$settings = json_decode(file_get_contents($settingspath),1);
$debug = $settings["compressJsAndCss"] ? 0 : 1;
$version = $settings["version"];
$lastChange = max(js_files(false,true),css_files(false,true));
$lastChange = max($lastChange,filemtime($settingspath));
$cachefname = "cache/_caljac_cache_v".($version ? $version : 0)."_".$lastChange.".js";

if($debug!=1){
  if(file_exists($cachefname)){
    header("Location: ".$cachefname,TRUE,302);
    die();
  }
}
else {
  clearCache();
}

require_once("jsminplus.php");

function sortByLoadOrder($ar,$loadOrder){
  if(is_array($loadOrder)){
    $bp = array_search("...",$loadOrder);
    for($i = 0; $i < count($ar); $i++){
      $prefix = "5000";
      $lastPart = explode("/",$ar[$i]);
      $lastPart = array_pop($lastPart);
      for($j = 0; $j < count($loadOrder); $j++){
        if($lastPart == $loadOrder[$j] && $j < $bp){
          $prefix = 1000 + $j;
        }
        if($lastPart == $loadOrder[$j] && $j > $bp){
          $prefix = 7500 + $j;
        }
      }
      $prefix = $prefix."";
      $ar[$i] = $prefix.$ar[$i];
    }
  }
  sort($ar);
  for($i = 0; $i < count($ar); $i++){
    $ar[$i] = substr($ar[$i],4);
  }
  return $ar;
}

function getFilePaths($dir,$strip = ""){
  $files = array();
  $objects = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($dir), 
    RecursiveIteratorIterator::SELF_FIRST
  );

  foreach($objects as $name => $object){
      $files[] = str_replace($strip,"",str_replace('\\','/',$name));
  }
  return $files;
}

function js_files($debug = 0,$changeTime = 0){
  global $settings;
  $js = ""; $biggestTime = 0; $t;
  $f = sortByLoadOrder(getFilePaths("../../js","../../"),$settings["loadOrderJS"]);
  foreach($f as $i){
    if(strpos($i,"/.") > 0) continue;
    if(!strpos($i,".")) continue;
    if(!strpos($i,".js")) continue;
    if($changeTime){
      $t = filemtime("../../".$i);
      if($t > $biggestTime) $biggestTime = $t;
      continue;
    }
    if($debug) $js .= '<script src="'.$i.'"></script>';
    else $js .= file_get_contents("../../".$i)."\n\n";
  }
  if($changeTime) return $biggestTime;
  return $js;
}

function css_files($debug = 0,$changeTime = 0){
  global $settings;
  $css = ""; $biggestTime = 0; $t;
  $f = sortByLoadOrder(getFilePaths("../../css","../../"),$settings["loadOrderCSS"]);
  foreach($f as $i){
    if(strpos($i,"/.") > 0) continue;
    if(strpos($i,"caljac_cache") > 0) continue;
    if(!strpos($i,".")) continue;
    if(!strpos($i,".less") && !strpos($i,".css")) continue;
    if($changeTime){
      $t = filemtime("../../".$i);
      if($t > $biggestTime) $biggestTime = $t;
      continue;
    }
    if($debug)$css .= '<link rel="stylesheet" type="text/'.(strpos($i,".less") ? "less" : "css").'" href="'.$i.'"/>';
    else $css .= file_get_contents("../../".$i)."\n\n";
  }
  if($changeTime) return $biggestTime;
  return $css;
}

function clearCache(){
  $f = getFilePaths("cache");
  foreach($f as $i){
    if(strpos($i,"caljac_cache") > 0){
      unlink($i);
    }
  }
}

function standardHeaders(){
  global $settings;
  return '<title>'.$settings["title"].'</title>\n';
}

function googleFonts(){
  global $settings;
  if(!isset($settings["googleFonts"])) return "";
  $fonts = "";
  foreach ($settings["googleFonts"] as $i){
    if($fonts != "") $fonts .= "|";
    $fonts .= str_replace(" ","+",$i);
  }
  $fonts = '<link href="http://fonts.googleapis.com/css?family='.$fonts.'" rel="stylesheet" type="text/css">';
  return $fonts;
}

googleFonts();
if($debug == 1){
  header("content-type: application/javascript");
  echo("document.write('".standardHeaders().googleFonts().css_files(1).js_files(1)."');");
}
else {
  clearCache();
  file_put_contents($cachefname,JSMinPlus::minify(
    js_files().
    ';document.write(\''.standardHeaders().googleFonts().'\'+"<style>'.
      '" + (function(cssString){var css;new(less.Parser)({rootpath:"/"}).parse(cssString, function (e, tree) {css = tree.toCSS({rootPath:"imgs/"});});return css;})("'.
      str_replace(
        '"', '\"',
        str_replace(
          "\n",'\n',
          css_files()
        )
      ).
    '") + "</style>");'
  ));
  header("Location: ".$cachefname,TRUE,302);
}
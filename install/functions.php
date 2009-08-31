<?php
function install_get_title($description){
  $return = "<h2>Instalace - krok {$GLOBALS["step"]}. - Redirecter {$GLOBALS["version"]}</h2>";
  $return .= "<strong>$description</strong><br><br>";
  return $return;
  }
function install_check_status() {
  for($i=1;$i<$GLOBALS["step"];$i++){
    if(!isset($_SESSION["step$i"]["checked"])){
      redirect("?step=$i&e=399");
      exit;
      }
    }
  }
function install_get_header() {
  $subtitle = array("Kontrola systému",
  "Nastavení MySQL databáze",
  "Nastavení údajů o administrátorovi.",
  "Instalace dokončena");
  $echo = get_header("Instalace - Krok {$GLOBALS["step"]}.");
  $echo .= install_get_title($subtitle[$GLOBALS['step']-1]);
  $echo .= get_error();
  return $echo;
  }
function install_get_error(){
  $error = array("Váš systém neprošel testováním kompatibility!",
  "Nezadali jste všechny povinné údaje!",
  null,);
  return $error[$GLOBALS['step']-1];
  }
?>
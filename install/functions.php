<?php
function install_get_title($description){
  $return = "<h2>Instalace - krok {$GLOBALS["step"]}. - Redirecter {$GLOBALS["version"]}</h2>";
  $return .= "<strong>$description</strong><br><br>";
  return $return;
  }
function install_check_status() {
  for($i=1;$i<$GLOBALS["step"];$i++){
    if(!isset($SESSION["step$i"]["checked"])){
      redirect(?step=$i&e=399);
      exit;
      }
    }
  }
?>
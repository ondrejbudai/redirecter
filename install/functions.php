<?php
function get_title($description){
  $return = "<h2>Instalace - krok {$GLOBALS["step"]}. - Redirecter {$GLOBALS["version"]}</h2>";
  $return .= "<strong>$description</strong><br><br>";
  return $return;
  }
?>
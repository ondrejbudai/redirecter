<?php
define("get","_GET");
define("post","_POST");
define("session","_SESSION");
function get_header($title) {
$return = <<<HTXT
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<meta name="generator" content="PSPad editor, www.pspad.com">
<link rel="stylesheet" media="screen,projection" type="text/css" href="main.css">
<title>$title - Redirecter {$GLOBALS['version']}</title>
</head>
<body>
HTXT;
return $return;
}

function get_footer(){
return "</body></html>";
}

function write_login() {
?>
<div class="login">
<h1><div style="text-align: center;">Redirecter
<?php
echo($GLOBALS['version']);
?>
</div></h1><br>

<h2>Heslo:</h2>
<form method="post" action="admin.php?action=login">
<input type="password" name="pass"><br><br>
<input type="submit" value="Přihlásit">
</form>

</div>
<?php
}

function redirect($location){
Header("Location: $location");
}

function connect(){
mysql_connect($GLOBALS['sql']['server'],$GLOBALS['sql']['user'],$GLOBALS['sql']['pass']);
mysql_select_db($GLOBALS['sql']['db']);
}
function rand_chars() {//Function by php.vrana.net, thanks!
$count = 16;
$chars = 36;
$return = "";
for ($i=0; $i < $count; $i++) {
$rand = mt_rand(0, $chars - 1);
$return .= chr($rand + ($rand < 10 ? ord('0') : ($rand < 36 ? ord('a') - 10 : ord('A') - 36)));
}
return $return;
}
function var_exists($array,$names){
  $return = 1;
  if(is_array($names)){
    for($i=0;$i<count($names);$i++){
      if(!isset($$array[$names[$i]])){
        $return = 0;
        break;
        }
      }
    }
  else if(!isset($$array[$names])) $return = 0;
  return $return;
}

function var_notnull($array,$names) {
  if(!var_exists($array,$names)) return 0;
  $return = 1;
  if(is_array($names)){
    for($i=0;$i<count($names);$i++){
      if($$array[$names[$i]]==null){
        $return = 0;
        break;
        }
      }
    }
  else if($$array[$names]==null) $return = 0;
  return $return;
}

function get_error($e){
  $error = "<div class=\"error\"><b>Chyba při vykonávání skriptu:</b><br>";
  switch($e){
    //INSTALLATION ERRORS:
    case 301:
    $error .= "Nevyplnili jste všechny povinná pole!";
    break;
    case 302:
    $error .= "Nelze se spojit s databází!";
    $error .= get_mysql_error();
    break;
    case 303:
    $error .= "Vámi zadaná databáze neexistuje!";
    $error .= get_mysql_error();
    break;
    case 304:
    $error .= "Soubor settings.php není zapisovatelný!<br>Změňte prosím jeho práva tak, aby byl zapisovatelný!";
    break;
    case 305:
    $error .= "Vámi používaná verze PHP (" . phpversion() . ") není podporovaná!<br>
              Upgradujte prosím Vaši instalaci PHP na minimální verzi 4.2.0 (doporučená verze je 5.2.x nebo 5.3.x) !";
    }
  $error .= "</div><br>";
  return $error;
}

function get_mysql_error() {
$error = "";
if(check_ifdefine(session,"mysql_error")==1){
  $error = "<br>MySQL hlásí:\"".$_SESSION['mysql_error']."\"";
  }
return $error;
}

function post_to_session($data){
  if(check_isset(post,$data)){
    if(!(is_array($data))){
      $_SESSION[$data] = $_POST[$data];
      }
    else {
      for($i=0;$i<count($data);$i++){
        $_SESSION[$data[$i]] = $_POST[$data[$i]];
        }
      }
    }
  }
?>
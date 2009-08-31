<?php
session_start();
require_once("settings.php");
require_once("functions.php");
require_once("install/functions.php");
if(var_notnull(get,"step")){
  $step = $_GET['step'];
  }
else {
  $step = 1;
  }
if(!(isset($instal))){
  redirect("admin.php");
  exit;
}
global $step;
$echo = "";
switch($step){
  case 1:
  session_unset();
  $echo .= install_get_header();
  $exit = false;
  if((!(strnatcmp(substr(phpversion(),0,strpos(phpversion(), '-')),'4.2.0') >= 0 ) && ereg("^.+-.+$",phpversion())) xor (
      !(strnatcmp(phpversion(), '4.2.0') >= 0) && !(ereg("^.+-.+$",phpversion())))){
    $echo .= get_error(305);
    $exit = true;
    }    
  if(!is_writable("settings.php")){
    $echo .= get_error(304);
    $exit = true;
    }   
  if(!$exit){
    $_SESSION["step1"]["checked"] = true;
    $echo .= "
    <b><div class=\"green\">Systém byl úspěšně otestován pro Redirecter $version</div</b><br><br>
    <a href=\"?step=2\">Pokračovat</a>";
    }
  else{
    $echo .= "<br><br><a href=\"?step=1\">Znovu otestovat</a>";
    }
  break;
  case 2:
  install_check_status();
  
  if(isset($_SESSION["filled"])){
    $user   = $_SESSION['user'];
    $pass   = $_SESSION['pass'];
    $server = $_SESSION['server'];
    $db     = $_SESSION['db'];
    $prefix = $_SESSION['prefix'];
    $port   = $_SESSION['port'];
    }
  else{
    $user   = "";
    $pass   = "";
    $server = "localhost";
    $db     = "";
    $prefix = "redirecter_";
    $port   = "3306";
    }
  $echo .= install_get_header();
  $echo .= <<<HTXT
<form action="?step=3" method="post">
<b>Zadejte prosím údaje pro přístup do databáze MySQL:</b><br><br>
Uživatelské jméno:<br>
<input type="text" name="user" value="$user"><br><br>
Heslo:<br>
<input type="password" name="pass" value="$pass"><br><br>
Server:<br>
<input type="text" name="server" value="$server"><br><br>
Databáze:<br>
<input type="text" name="db" value="$db"><br><br>
Prefix:<br>
<input type="text" name="prefix" value="$prefix"><br><br>
Port:<br>
<input type="text" name="port" value="$port"><br><br>
<input type="submit" value="Odeslat">
</form>
HTXT;
  break;
  
  case 3:
  if(var_notnull(post,array("user","server","db","port")) && var_exists(post,array("pass","prefix"))
  && !isset($_SESSION["step3"]["checked"])){
    $_SESSION['step2']['checked'] = true;
    }
  $echo .= install_check_status();
  if(!isset($_SESSION["step3"]["checked"])){
    post_to_session(array("user","pass","server","db","port","prefix"));
    $port = ":{$_POST["port"]}";
    if(!mysql_connect($_POST["server"].$port,$_POST["user"],$_POST["pass"])){
      $_SESSION["mysql_error"] = mysql_error();  
      $_SESSION["filled"] = true; 
      redirect("?step=2&e=302");
      exit;
    }
    if(!mysql_select_db($_POST["db"])){
      $_SESSION["mysql_error"] = mysql_error();
      $_SESSION["filled"] = true; 
      redirect("?step=2&e=303");
      exit;
    }
    $_SESSION["step3"]["checked"] = true;
  }
  $echo .= install_get_header();
  $echo .= <<<HTXT
<form method="post" action="?step=4">
E-mail:<br>
<input type="text" name="mail" value="@"><br>
Heslo:<br>
<input type="password" name="pass"><br><br>
<input type="submit" value="Odeslat">
</form>
HTXT;
  break;
  case 4:
  install_check_status();
  if (!var_notnull(post,array("mail","pass"))){
    redirect("?step=3&e=1");
    exit;
    }
  $server = $_SESSION['server'] .":".$_SESSION['port'];
  $user = $_SESSION['user'];
  $pass = $_SESSION['pass'];
  $db = $_SESSION['db'];
  $prefix = $_SESSION['prefix'];
  mysql_connect($server,$user,$pass);
  mysql_select_db($db);
  mysql_query("DROP TABLE IF EXISTS {$prefix}redirecter");
  mysql_query(" CREATE TABLE {$prefix}redirecter (
    id varchar(10) NOT NULL DEFAULT '',
    address varchar(100) NOT NULL,
    clicks smallint(5) unsigned DEFAULT NULL,
    PRIMARY KEY (id))
  DEFAULT CHARSET=utf8");
  mysql_query("SET names utf8");
  mysql_query("SET character_set_client=utf8");
  mysql_query("SET character_set_connection=utf8");
  mysql_query("SET character_set_results=utf8");
  $file_default = fopen("default-settings.php","r");
  $settings = fread($file_default,filesize("default-settings.php"));
  fclose($file_default);
  $file_settings = fopen("settings.php","w");
  $settings = str_replace("*USER*",$user,$settings);
  $settings = str_replace("*PASS*",$pass,$settings);
  $settings = str_replace("*SERVER*",$server,$settings);
  $settings = str_replace("*DB*",$db,$settings);
  $settings = str_replace("*PREFIX*",$prefix,$settings);
  $settings = str_replace("*USERPASS*",md5($_POST['pass']),$settings);
  $settings = str_replace("*MAIL*",$_POST['mail'],$settings);
  fwrite($file_settings,$settings);
  fclose($file_settings);
  $echo .= install_get_header();
  $echo .= <<<HTXT
<div class="green">Instalace byla úspěšně dokončena.<br>
Pokračujte přihlášením do <a href="admin.php">administrace.</a></div>
HTXT;
  break;
  }
$echo .= get_footer();
echo $echo;
?>
<?php
session_start();
require_once("settings.php");
require_once("functions.php");
if(var_notnull(get,"step")){
  $step = $_GET['step'];
  }
else {
  $step = 1;
  }
if(!(isset($instal))){
  redirect("admin.php");
}
switch($step){
  case 0:
  redirect("?step=1");
  break;
  
  case 1:
  session_destroy();
  $echo = get_header("Instalace - krok $step.");
  $echo .= "<h2>Instalace - Krok 1. - Redirecter $version </h2><br><br>";
  $exit = false;
  if((!(strnatcmp(substr(phpversion(),0,strpos(phpversion(), '-')),'4.2.0') >= 0 ) && ereg("^.+-.+$",phpversion())) xor (
      !(strnatcmp(phpversion(), '4.2.0') >= 0) && !(ereg("^.+-.+$",phpversion())))){
    $echo .= get_error(305);
    $exit = true;
    }
  
  if(!is_writable("settings.php")){
    $echo .= get_error(304);
    $echo .= "<br><br><a href=\"?step=1\">Znovu otestovat</a>";
    $exit = true;
    }
  if(!$exit){
    $_SESSION['step1']['control'] = true;
    $echo .= "
    <b><div class=\"green\">Systém byl úspěšně otestován pro Redirecter $version</div</b><br><br>
    <a href=\"?step=2\">Pokračovat</a>";
    }
  break;
  
  case 2:
  write_header("Instalace - krok 2.");
  ?>
<h2>Instalace - Krok 2. - Redirecter <?php echo($version);?></h2><br><br>
<?php
if(check_ifdefine(get,"e")==1){
  echo(get_error("instal", $_GET['e']));
  }
if(check_ifdefine(session,"filled")==1){
$user = $_SESSION['user'];
$pass = $_SESSION['pass'];
$server = $_SESSION['server'];
$db = $_SESSION['db'];
$prefix = $_SESSION['prefix'];
$port = $_SESSION['port'];
}
else{
$user = "";
$pass = "";
$server = "localhost";
$db = "";
$prefix = "redirecter_";
$port = "3306";
}
?>
<form action="?step=3" method="post">
<b>Zadejte prosím údaje pro přístup do databáze MySQL:</b><br><br>
Uživatelské jméno:<br>
<input type="text" name="user" value="<?php echo($user);?>"><br><br>
Heslo:<br>
<input type="password" name="pass" value="<?php echo($pass);?>"><br><br>
Server:<br>
<input type="text" name="server" value="<?php echo($server);?>"><br><br>
Databáze:<br>
<input type="text" name="db" value="<?php echo($db);?>"><br><br>
Prefix:<br>
<input type="text" name="prefix" value="<?php echo($prefix);?>"><br><br>
Port:<br>
<input type="text" name="port" value="<?php echo($port);?>"><br><br>
<input type="submit" value="Odeslat">
</form>
  <?php
  break;
  
  case 3:
  
  if((check_ifdefine(post,array("user","pass","server","db","port"))==0 || check_isset(post,"prefix")==0)
      && check_ifdefine(session,"db_tested")==0){
    redirect("?step=2&e=1");
    exit;
    }
  if(check_ifdefine(session,"db_tested")==0){
    post_to_session(array("user","pass","server","db","port","prefix"));
    $port = ":".$_POST['port'];
    $status = mysql_connect($_POST['server'].$port,$_POST['user'],$_POST['pass']);
    if($status == false){
      $_SESSION['mysql_error'] = mysql_error();
      
      $_SESSION['filled'] = true; 
      redirect("?step=2&e=2");
      exit;
    }
    $status = mysql_select_db($_POST['db']);
    if($status == false){
      $_SESSION['mysql_error'] = mysql_error();
      $_SESSION['filled'] = true; 
      redirect("?step=2&e=3");
      exit;
    }
    $_SESSION['db_tested'] = true;
  }
  write_header("Instalace - Krok 3.");
  
  ?>
<h2>Instalace - Krok 3. - Redirecter <?php echo($version);?></h2><br><br>
  <?php
  if(check_ifdefine(get,"e")==1){
    echo(get_error("instal", $_GET['e']));
  }
  ?>
<form method="post" action="?step=4">
E-mail:<br>
<input type="text" name="mail" value="@"><br>
Heslo:<br>
<input type="password" name="pass"><br><br>
<input type="submit" value="Odeslat">
</form>
  <?php
  break;
  case 4:
  if (check_ifdefine(post,array("mail","pass"))==0){
    redirect("?step=3&e=1");
    exit;
    }
  if (check_ifdefine(session,array("user","pass","server","db","port"))==0 
      || check_isset(session,"prefix")==0){
    redirect("?step=1");
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
  redirect("admin.php");
  
  break;
  }
$echo .= get_footer();
echo $echo;
?>
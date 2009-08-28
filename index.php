<?php
require_once("settings.php");
if(isset($_GET['r']) && !($_GET['r']=="")){
  $r = $_GET['r'];
  mysql_connect($sql['server'],$sql['user'],$sql['pass']);
  mysql_select_db($sql['db']);
  
  $query = mysql_query("SELECT `address`,`clicks` FROM `".$sql['prefix']."redirecter` WHERE `id`='".$r."'") or die(mysql_error());
  $count = mysql_num_rows($query);
  if($count==1){
    $data = mysql_fetch_array($query);
    $data['clicks']++;
    mysql_query("UPDATE `".$sql['prefix']."redirecter` SET `clicks`=".$data['clicks']." WHERE `id`='".$r."'") or die(mysql_error());
    mysql_close();
    Header("Location: ".$data['address']);
    }
  mysql_close();
  $error = "Záznam s vámi zadaným ID nebyl nalezen!";
  }
else {
$error = "Nezadali jste ID požadavku!";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <meta name="generator" content="PSPad editor, www.pspad.com">
  <title>
  <?php
  echo("ERROR");
  ?>
  </title>
  </head>
  <body>
  <?php
  echo($error."<br /> Kontaktujte prosím webmastera na adrese <a href=\"mailto:".$mail."\">".$mail."</a>, díky!");
  ?>
  </body>
</html>

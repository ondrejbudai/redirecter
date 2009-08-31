<?php
session_start();
require_once("settings.php");
require_once("functions.php");
check_install();
if(isset($_GET['action'])){
  switch($_GET['action']){
    case "login":
    
      if(isset($_POST['pass']) && !($_POST['pass']=="")){
        $pass = md5(strip_tags($_POST['pass']));
        if($pass == $password) {
          $_SESSION['pass'] = $pass;
          redirect("admin.php?action=home");
          }
        else {
          redirect("admin.php?e=2");
          }
        }
      else {
        redirect("admin.php?e=1");
        }
    break;
    
    case "home":
    
      if($_SESSION['pass']==$password){
        echo get_header("Administrace");
        ?>
<h2>ADMINISTRACE - Redirecter <?php echo($version);?></h2><br><br>

<div class="link">
<a href="?action=edit-entries">Upravit záznamy</a><br>
<a href="?action=new-entry">Nový záznam</a><br>
<i>Změnit nastavení</i><br>
<a href="?action=logout">Odhlásit</a>
</div><br><br>
©Budi, 2009

        <?php
        }
      else {
        redirect("admin.php?e=2");
        }
    break;
    
    case "edit-entries":
    
      if($_SESSION['pass']==$password){
        echo get_header("Upravit záznamy");
        ?>
<h2>Upravit záznamy</h2>
<table cellspacing="0" cellpadding="2">
<tr><td class="table-header">ID</td>
<td class="table-header">Adresa</td>
<td class="table-header">Počet kliknutí</td>
<td class="table-header">Vyzkoušet</td>
<td class="table-header">Změnit</td>
<td class="table-header">Smazat</td>

</tr>
        <?php
        connect();
        $query = mysql_query("SELECT * FROM `{$sql["prefix"]}redirecter`") or die(mysql_error());
        while($data = mysql_fetch_array($query)){
          echo("<tr><td>".strip_tags($data['id'])."</td>");
          echo("<td><a href=\"".strip_tags($data['address'])."\">".strip_tags($data['address'])."</a></td>");
          echo("<td>".strip_tags($data['clicks'])."</td>");
          echo("<td><a href=\"$".strip_tags($data['id'])."\">Vyzkoušet</a></td>");
          echo("<td><a href=\"?action=edit-entry&id=".strip_tags($data['id'])."\">Změnit</a></td>");
          echo("<td><a href=\"?action=delete-entry&id=".strip_tags($data['id'])."\">Smazat</a></td></tr>");
          }
        mysql_close();
        ?>
</table><br><br>
<div class="link"><a href="?action=home">Zpět na úvodní stránku administrace</a></div>
        <?php
        }
      else {
        redirect("admin.php?e=2");
        }
    break;
    
    case "edit-entry":
      if($_SESSION['pass']==$password){
        if(isset($_GET['id']) && !($_GET['id']=="")){
          $id = $_GET['id'];
          echo get_header("Upravit záznam \"".$id."\"");
          connect();
          $query = mysql_query("SELECT * FROM `".$sql['prefix']."redirecter` WHERE `id`='".$id."'");
          $count = mysql_num_rows($query);
          if($count>0){
            $data = mysql_fetch_array($query);
          ?>
<h2>Upravit záznam "<?php echo($id);?>"</h2><br><br>
<form action="?action=edit-entry-do" method="post">
ID:<br>
<input type="text" value="<?php echo(strip_tags($id))?>" name="id"><br><br>
Adresa:<br>
<input type="text" value="<?php echo(strip_tags($data['address']))?>" name="address"><br><br>
Počet kliknutí:<br>
<input type="text" value="<?php echo(strip_tags($data['clicks']))?>" name="clicks" readonly><br><br>
<input type="checkbox" name="null-clicks"> Vynulovat počet kliknutí<br><br>
<input type="hidden" name="token" value="<?php
$rand = rand_chars();
echo($rand);
$_SESSION['token'] = $rand;
?>">
<input type="hidden" name="oid" value="<?php
echo(strip_tags($id));
?>">
<input type="submit" value="Změnit">
</form><br><br>
<div class="link"><a href="?action=edit-entries">Zpět na výpis záznamů</a><br>
<a href="?action=home">Zpět na úvodní stránku administrace</a>
</div>
          <?php

            mysql_close();
            }
          else {
            mysql_close();
            redirect("admin.php?action=edit-entries&e=102");
            }
          }
        else {
          redirect("admin.php?action=edit-entries");
          }
        }
      else {
        redirect("admin.php?e=2");
        }
    break;
    
    case "edit-entry-do":
      if($_SESSION['pass']==$password){
        if(isset($_POST['id']) && isset($_POST['address']) && isset($_POST['token'])
        && isset($_POST['oid']) && isset($_POST['clicks'])){
        
          if(isset($_SESSION['token']) && $_SESSION['token']==$_POST['token']){
            connect();
            if(!($_POST['id']==$_POST['oid'])){
              $query = mysql_query("SELECT COUNT(*) AS count FROM `".$sql['prefix']."redirecter` WHERE `id`='".$_POST['id']."'");
              $count = mysql_fetch_array($query);
              }
            else $count['count'] = 0;
            if($count['count']==0){
              $clicks = strip_tags($_POST['clicks']);
              if(isset($_POST['null-clicks'])){
                $clicks = 0;
                }
              $id = strip_tags($_POST['id']);
              $address = strip_tags($_POST['address']);
              $oid = strip_tags($_POST['oid']);
              mysql_query("UPDATE `".$sql['prefix']."redirecter` SET 
              `id` = '".$id."',
              `address`= '".$address."',
              `clicks`= ".$clicks."
              WHERE `id`='".$oid."'");
              mysql_close();
              redirect("admin.php?action=edit-entries");
            }
            else{
              mysql_close();
              redirect("admin.php?action=edit-entry&e=101");
              }
            }
          else {
            redirect("admin.php?action=logout&e=3");
            }
          }
        else {
          redirect("admin.php?action=edit-entries");
          }
        }
      else {
        redirect("admin.php?e=2");
        }
    break;
    
    case "delete-entry":
      if($_SESSION['pass']==$password){
        if(isset($_GET['id']) && !($_GET['id']=="")){
          connect();
          $query = mysql_query("SELECT COUNT(*) AS count FROM `".$sql['prefix']."redirecter` WHERE `id`='".$_GET['id']."'");
          $count = mysql_fetch_array($query);
          mysql_close();
          if($count['count']==1){
            echo get_header("Smazat záznam \"".$_GET['id']."\"");
            ?>
<h2>Smazat záznam</h2><br><br>
<strong>Opravdu chcete smazat záznam "<?php echo($_GET['id']);?>"?<br>
<form action="?action=delete-entry-do" method=post>
<input type="hidden" name="token" value="<?php
$rand = rand_chars();
echo($rand);
$_SESSION['token'] = $rand;
?>">
<input type="hidden" name="id" value="<?php echo(strip_tags($_GET['id']));?>">
<input type="submit" value="Ano" name="ano">
<input type="submit" value="Ne" name="ne">
</form>
            <?php
            }
          else {
            redirect("?action=edit-entries&e=102");  
            }
           }
        else {
          redirect("?action=edit-entries");  
          }
        }
      else {
        redirect("admin.php?e=2");
        }
    break;
    
    case "delete-entry-do":
    
      if($_SESSION['pass'] == $password){
        if(isset($_POST['token']) && isset($_POST['token']) && (isset($_POST['ano']) || isset($_POST['ne']))){
          if(isset($_SESSION['token']) && $_POST['token'] == $_SESSION['token']){
            if(isset($_POST['ano'])){
              connect();
              $query = mysql_query("SELECT COUNT(*) AS count FROM `".$sql['prefix']."redirecter` WHERE `id`='".$_POST['id']."'");
              $count = mysql_fetch_array($query);
              if($count['count']==1){
                mysql_query("DELETE FROM `".$sql['prefix']."redirecter` WHERE `id`='".$_POST['id']."'");
                mysql_close();
                redirect("?action=edit-entries");
                }
              else {
                mysql_close();
                redirect("?action=edit-entries");
                }
              }
            else {
              redirect("?action=edit-entries");
              }
            }
          else {
            redirect("?action=logout&e=3");
            }
          }
        else {
          redirect("?action=edit-entries");
          }
        }
      else {
          redirect("admin.php?e=2");
        }
      break;
    
    case "new-entry":
    
      if($_SESSION['pass'] == $password){
        echo get_header("Vytvořit nový záznam");
        ?>
<h2>Vytvořit nový záznam</h2><br><br>
<form action="?action=new-entry-do" method="post">
ID:<br><input type="text" name="id"><br><br>
Adresa:<br><input type="text" name="address"><br><br>
<input type="hidden" name="token" value="<?php
$rand = rand_chars();
echo($rand);
$_SESSION['token'] = $rand;
?>">
<input type="submit" value="Vytvořit">
</form>
        <?
        }
      else {
          redirect("admin.php?e=2");
        }
        
    break;
    
    case "new-entry-do":
    
      if($_SESSION['pass'] == $password){
        if(isset($_POST['id']) && isset($_POST['address']) && isset($_POST['token']) && !($_POST['id'] == "") &&!($_POST['address'] == "")) {
          if(isset($_SESSION['token']) && $_SESSION['token']==$_POST['token']){
            connect();
            $query = mysql_query("SELECT COUNT(*) AS count FROM `".$sql['prefix']."redirecter` WHERE `id`='".$_POST['id']."'");
            $count = mysql_fetch_array($query);
            if($count['count']==0){
              mysql_query("INSERT INTO `".$sql['prefix']."redirecter` (`id`, `address`, `clicks`) VALUES (
              '".strip_tags($_POST['id'])."',
              '".strip_tags($_POST['address'])."',
              0
              )");
              mysql_close();
              redirect("?action=edit-entries");
              }
            else {
              mysql_close();
              redirect("?action=new-entry");
              }
            }
          else {
            redirect("admin.php?action=logout&e=3");
            }
          }
        else {
          redirect("admin.php?action=new-entry");
          }
        }
      else {
        redirect("admin.php?e=2");
        }
    
    break;
    
    case "logout":
      session_destroy();
      if(isset($_GET['e'])){
        $e = "?e=".$_GET['e'];
        }
      else {
        $e = "?";
        }
      redirect($e);
    break;
    
    default:
      echo get_header("Login");
      write_login();
    break;
    }
  }
else {
  echo get_header("Login");
  write_login();
  }
?>
</body>
</html>
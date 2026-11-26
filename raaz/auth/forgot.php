<?php
require __DIR__.'/../config/config.php';
$ok=''; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();
 $u=trim($_POST['username']??''); $n=trim($_POST['full_name']??''); $p=trim($_POST['phone']??'');
 if($u&&$n&&$p){
  $s=$pdo->prepare('INSERT INTO password_requests(username,full_name,phone) VALUES(?,?,?)');
  $s->execute([$u,$n,$p]);
  $ok='Request submitted. After admin approval, ask the admin for your one-time reset code.';
 } else $err='Please complete all fields.';
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/app.css"><title>Password recovery · RAAZ</title></head><body class="auth"><form class="authbox" method="post"><a class="logo" href="../">RAAZ<span>.</span></a><h1>Password recovery</h1><?php if($ok):?><p class="success"><?=e($ok)?></p><?php endif?><?php if($err):?><p class="error"><?=e($err)?></p><?php endif?><input type="hidden" name="csrf" value="<?=e(csrf())?>"><label>Username</label><input name="username" maxlength="30" required><label>Full name</label><input name="full_name" maxlength="80" required><label>Phone number</label><input name="phone" maxlength="24" required><button class="primary">Submit request</button><p class="sub"><a href="reset-password.php">Already approved? Set a new password</a></p><p class="sub"><a href="login.php">Back to login</a></p></form></body></html>
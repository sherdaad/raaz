<?php
require __DIR__.'/../config/config.php';
$ok=''; $err='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();
 $u=trim($_POST['username']??''); $code=strtoupper(trim($_POST['reset_code']??''));
 $pw=$_POST['new_password']??''; $confirm=$_POST['confirm_password']??'';
 if($u===''||$code===''||$pw===''||$confirm==='') $err='Please complete all fields.';
 elseif(strlen($pw)<8) $err='Password must be at least 8 characters.';
 elseif($pw!==$confirm) $err='Passwords do not match.';
 else {
  $q=$pdo->prepare("SELECT id,reset_token_hash FROM password_requests WHERE username=? AND status='approved' AND reset_token_used_at IS NULL AND reset_token_expires_at>NOW() ORDER BY reviewed_at DESC,id DESC LIMIT 1");
  $q->execute([$u]); $request=$q->fetch();
  if($request && !empty($request['reset_token_hash']) && password_verify($code,$request['reset_token_hash'])){
   $userQ=$pdo->prepare('SELECT id FROM users WHERE username=? LIMIT 1'); $userQ->execute([$u]); $user=$userQ->fetch();
   if($user){
    $pdo->beginTransaction();
    try {
     $pdo->prepare('UPDATE users SET password_hash=? WHERE id=?')->execute([password_hash($pw,PASSWORD_DEFAULT),$user['id']]);
     $pdo->prepare('UPDATE password_requests SET reset_token_used_at=NOW() WHERE id=?')->execute([$request['id']]);
     $pdo->commit(); $ok='Your password has been changed. You can now sign in with your new password.';
    } catch(Throwable $e){$pdo->rollBack();throw $e;}
   } else $err='The account could not be found. Contact the administrator.';
  } else $err='Invalid or expired reset code. Contact the administrator for a new request.';
 }
}
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/app.css"><title>Set new password · RAAZ</title></head><body class="auth"><form class="authbox" method="post"><a class="logo" href="../">RAAZ<span>.</span></a><h1>Set a new password</h1><?php if($ok):?><p class="success"><?=e($ok)?></p><?php endif?><?php if($err):?><p class="error"><?=e($err)?></p><?php endif?><input type="hidden" name="csrf" value="<?=e(csrf())?>"><label>Username</label><input name="username" maxlength="30" autocomplete="username" required><label>One-time reset code</label><input name="reset_code" maxlength="32" autocomplete="one-time-code" required><label>New password</label><input type="password" name="new_password" minlength="8" autocomplete="new-password" required><label>Confirm new password</label><input type="password" name="confirm_password" minlength="8" autocomplete="new-password" required><button class="primary">Change password</button><p class="sub"><a href="login.php">Back to login</a></p></form></body></html>
<?php
require __DIR__.'/config/config.php';
$u=login_required();
$msg='';$err='';
$profileDir=__DIR__.'/uploads/profiles/';
$allowed=['image/jpeg'=>'jpg','image/png'=>'png','image/webp'=>'webp'];
function remove_old_profile_picture(?string $path,string $dir): void {
 if(!$path || !preg_match('~^uploads/profiles/[A-Za-z0-9_-]+\.(jpg|png|webp)$~',$path)) return;
 $file=$dir.basename($path);
 if(is_file($file)) @unlink($file);
}
if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf();
 if(isset($_POST['remove_picture'])){
  $q=$pdo->prepare('SELECT profile_picture FROM users WHERE id=?');$q->execute([$u['id']]);$old=$q->fetchColumn();
  $pdo->prepare('UPDATE users SET profile_picture=NULL WHERE id=?')->execute([$u['id']]);
  remove_old_profile_picture($old?:null,$profileDir);$msg='Profile picture removed.';
 } else {
  $name=trim($_POST['full_name']??'');$status=trim($_POST['status_text']??'');
  if($name===''||mb_strlen($name)>80||mb_strlen($status)>160){$err='Please check your name and status fields.';}
  else {
   $newPicture=null;
   if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error']!==UPLOAD_ERR_NO_FILE){
    $file=$_FILES['profile_picture'];
    if($file['error']!==UPLOAD_ERR_OK){$err='Upload failed. Please try again.';}
    elseif($file['size']>3*1024*1024){$err='Image must be 3 MB or smaller.';}
    else {
     $info=@getimagesize($file['tmp_name']);$mime=$info['mime']??'';
     if(!$info || !isset($allowed[$mime])){$err='Only JPG, PNG and WEBP images are allowed.';}
     else {
      if(!is_dir($profileDir)) @mkdir($profileDir,0755,true);
      if(!is_dir($profileDir)||!is_writable($profileDir)){$err='Profile upload folder is not writable.';}
      else {
       $filename=bin2hex(random_bytes(16)).'.'.$allowed[$mime];
       if(move_uploaded_file($file['tmp_name'],$profileDir.$filename))$newPicture='uploads/profiles/'.$filename;
       else $err='Could not save the profile picture.';
      }
     }
    }
   }
   if(!$err){
    if($newPicture){$q=$pdo->prepare('SELECT profile_picture FROM users WHERE id=?');$q->execute([$u['id']]);$old=$q->fetchColumn();$s=$pdo->prepare('UPDATE users SET full_name=?,status_text=?,profile_picture=? WHERE id=?');$s->execute([$name,$status,$newPicture,$u['id']]);remove_old_profile_picture($old?:null,$profileDir);}
    else {$s=$pdo->prepare('UPDATE users SET full_name=?,status_text=? WHERE id=?');$s->execute([$name,$status,$u['id']]);}
    $_SESSION['user']['full_name']=$name;$msg='Profile updated successfully.';
   }
  }
 }
}
$q=$pdo->prepare('SELECT username,full_name,status_text,profile_picture FROM users WHERE id=?');$q->execute([$u['id']]);$profile=$q->fetch();
$picture=$profile['profile_picture']??'';
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/css/app.css"><title>Profile · RAAZ</title></head>
<body class="auth"><section class="authbox"><a class="logo" href="dashboard.php">RAAZ<span>.</span></a><h1>Your profile</h1>
<?php if($msg):?><p class="success"><?=e($msg)?></p><?php endif?><?php if($err):?><p class="error"><?=e($err)?></p><?php endif?>
<div class="profile-preview"><?php if($picture):?><img src="<?=e($picture)?>?v=<?=time()?>" alt="Your profile picture"><?php else:?><div class="profile-placeholder"><?=e(mb_strtoupper(mb_substr($profile['full_name']?:$profile['username'],0,1)))?></div><?php endif?></div>
<form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=e(csrf())?>">
<label>Profile picture</label><input type="file" name="profile_picture" accept="image/jpeg,image/png,image/webp"><p class="sub">JPG, PNG or WEBP · Maximum 3 MB</p>
<label>Username</label><input value="<?=e($profile['username'])?>" disabled>
<label>Full name</label><input name="full_name" value="<?=e($profile['full_name'])?>" maxlength="80" required>
<label>Status</label><input name="status_text" value="<?=e($profile['status_text']??'')?>" maxlength="160" placeholder="Set a short status">
<button class="primary" type="submit">Save profile</button></form>
<?php if($picture):?><form method="post" onsubmit="return confirm('Remove your profile picture?')"><input type="hidden" name="csrf" value="<?=e(csrf())?>"><button class="secondary" name="remove_picture" value="1" type="submit">Remove profile picture</button></form><?php endif?>
<p class="sub"><a href="dashboard.php">Back to chats</a></p></section></body></html>

<?php
require __DIR__.'/../config/config.php';
if(empty($_SESSION['admin_id'])){header('Location: login.php');exit;}
$uid=(int)($_GET['user_id']??0);
$q=$pdo->prepare('SELECT id,username,full_name,account_status FROM users WHERE id=?');$q->execute([$uid]);$user=$q->fetch();
if(!$user){http_response_code(404);exit('User not found.');}
$sql="SELECT c.id conversation_id, other.id peer_id, other.username, other.full_name,
       (SELECT COUNT(*) FROM admin_message_archive a WHERE a.conversation_id=c.id) message_count,
       (SELECT MAX(sent_at) FROM admin_message_archive a WHERE a.conversation_id=c.id) last_message
      FROM conversation_members mine
      JOIN conversations c ON c.id=mine.conversation_id
      JOIN conversation_members om ON om.conversation_id=c.id AND om.user_id<>mine.user_id
      JOIN users other ON other.id=om.user_id
      WHERE mine.user_id=? ORDER BY last_message DESC, c.id DESC";
$s=$pdo->prepare($sql);$s->execute([$uid]);$chats=$s->fetchAll();
?><!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="../assets/css/app.css"><title>User conversations · RAAZ Admin</title></head><body><header class="top"><a class="logo" href="index.php">RAAZ<span>.</span> ADMIN</a><a href="index.php">Back to dashboard</a></header><main class="admin"><p class="sub"><a href="index.php">Admin</a> / Users / @<?=e($user['username'])?></p><h1><?=e($user['full_name'])?></h1><p class="sub">@<?=e($user['username'])?> · <?=e($user['account_status'])?> · <?=count($chats)?> conversations</p><div class="notice">Admin-only moderation view. Messages are retained in the moderation archive after disappearing from users' chat screens. Access is recorded.</div><h2>Conversations</h2><?php if(!$chats):?><p class="sub">No conversations found for this user.</p><?php else:?><div class="tablewrap"><table><tr><th>Conversation</th><th>Messages archived</th><th>Last message</th><th>Action</th></tr><?php foreach($chats as $c):?><tr><td><?=e($user['username'])?> → <?=e($c['username'])?><small style="display:block;color:#999"><?=e($c['full_name'])?></small></td><td><?= (int)$c['message_count']?></td><td><?=e($c['last_message']??'No archived messages')?></td><td><a class="mini" href="chat.php?conversation_id=<?=(int)$c['conversation_id']?>">View chat</a></td></tr><?php endforeach?></table></div><?php endif?></main></body></html>

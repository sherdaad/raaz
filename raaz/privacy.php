<?php
require __DIR__.'/config/config.php';
$u=me();
if(!$u){header('Location: auth/login.php');exit;}
$waNumber='923425834947';
$screenMessage='Assalam-o-Alaikum, I want to request lifetime screenshot permission for my RAAZ account (@'.$u['username'].'). Please share the payment and activation details. Price: Rs. 250.';
$screenUrl='https://wa.me/'.$waNumber.'?text='.rawurlencode($screenMessage);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link rel="stylesheet" href="assets/css/app.css"><title>Privacy & permissions · RAAZ</title></head>
<body class="auth"><section class="authbox"><a class="logo" href="dashboard.php">RAAZ<span>.</span></a><h1>Privacy & moderation</h1>
<p class="sub">RAAZ is not end-to-end encrypted. Authorized administrators can review message content for safety, abuse prevention, and handling of lawful requests. Messages may disappear from participants’ chat screens after the configured expiry period, while a separate moderation copy is retained in a restricted archive. Admin access to archived conversations is logged. Do not send highly sensitive information through RAAZ.</p>
<p class="sub">The moderation archive is separate from the normal chat history. Access is limited to authorized admin accounts and should only be used for legitimate platform-safety and legal-compliance purposes. Retention periods and handling of lawful requests should be reviewed with qualified local counsel before public launch.</p>
<hr style="border-color:#333;margin:22px 0"><h2>Screenshot permission</h2>
<p class="sub">Choose a permission and contact the RAAZ administrator directly on WhatsApp. The request message will be pre-filled; you can review it before sending.</p>
<div class="permission-card"><div><b>Lifetime screenshot access</b><p class="sub">Rs. 250 · One-time payment</p></div><a class="primary wa-button" href="<?=e($screenUrl)?>" target="_blank" rel="noopener noreferrer">Contact admin on WhatsApp</a></div>
<p class="sub">Permission is not activated automatically by sending a WhatsApp message. The administrator must verify payment and activate screenshot access.</p>
<a href="dashboard.php">Back to chats</a></section></body></html>

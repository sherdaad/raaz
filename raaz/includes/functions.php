<?php
function e($v): string { return htmlspecialchars((string)($v??''),ENT_QUOTES|ENT_SUBSTITUTE,'UTF-8'); }
function csrf(): string { if(empty($_SESSION['csrf'])) $_SESSION['csrf']=bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function check_csrf(): void {
 $v=$_SERVER['HTTP_X_CSRF_TOKEN']??$_POST['csrf']??'';
 if(!is_string($v)||!hash_equals(csrf(),$v)){http_response_code(419);exit('Invalid security token. Refresh and retry.');}
}
function me(): ?array { return $_SESSION['user']??null; }
function login_required(): array { if(!me()){http_response_code(401);exit('Please sign in.');} return me(); }
function json_out($data,int $code=200): never { http_response_code($code); header('Content-Type: application/json; charset=utf-8'); echo json_encode($data,JSON_UNESCAPED_UNICODE); exit; }
function clean_expired(PDO $pdo): void { $pdo->exec("DELETE FROM messages WHERE expires_at IS NOT NULL AND expires_at <= NOW()"); $pdo->exec("DELETE FROM statuses WHERE expires_at <= NOW()"); }
function peer_id(PDO $pdo,int $conversation,int $uid): ?int {
 $s=$pdo->prepare('SELECT user_id FROM conversation_members WHERE conversation_id=? AND user_id<>? LIMIT 1');
 $s->execute([$conversation,$uid]); $v=$s->fetchColumn(); return $v===false?null:(int)$v;
}
function owns_conversation(PDO $pdo,int $conversation,int $uid): bool {
 $s=$pdo->prepare('SELECT 1 FROM conversation_members WHERE conversation_id=? AND user_id=?');$s->execute([$conversation,$uid]);return (bool)$s->fetchColumn();
}

function has_moderation_consent(PDO $pdo,int $uid): bool { $q=$pdo->prepare('SELECT moderation_notice_accepted_at FROM users WHERE id=?'); $q->execute([$uid]); return (bool)$q->fetchColumn(); }

<?php
declare(strict_types=1);
const DB_HOST='localhost'; const DB_NAME='raaz'; const DB_USER='root'; const DB_PASS='';
const BASE_URL='/raaz';
date_default_timezone_set('Asia/Karachi');
if(session_status()!==PHP_SESSION_ACTIVE){
 $secure=!empty($_SERVER['HTTPS'])&&$_SERVER['HTTPS']!=='off';
 session_set_cookie_params(['lifetime'=>0,'path'=>'/','httponly'=>true,'secure'=>$secure,'samesite'=>'Lax']);
 session_start();
}
require_once __DIR__.'/database.php';
require_once __DIR__.'/../includes/functions.php';

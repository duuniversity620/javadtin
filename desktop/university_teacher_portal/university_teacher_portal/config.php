<?php
// University Teacher Portal - VPS Configuration
// 1) Create MySQL database and import sql/database.sql
// 2) Update these values
$db_host = 'localhost';
$db_name = 'university_portal';
$db_user = 'root';
$db_pass = '';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed. Please check config.php');
}

session_start();
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('MAX_UPLOAD_BYTES', 20 * 1024 * 1024); // 20 MB
$allowed_ext = ['pdf','doc','docx','ppt','pptx','xls','xlsx','zip','jpg','jpeg','png','txt'];

function h($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
function current_user(){ return $_SESSION['user'] ?? null; }
function require_login(){ if(!current_user()){ header('Location: index.php'); exit; } }
function require_role($role){ require_login(); if(current_user()['role'] !== $role && current_user()['role'] !== 'admin'){ http_response_code(403); die('Access denied'); } }
function redirect($url){ header("Location: $url"); exit; }
function csrf_token(){ if(empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_check(){ if($_SERVER['REQUEST_METHOD']==='POST' && (!isset($_POST['csrf']) || !hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']))){ die('Invalid security token'); } }

<?php require 'config.php'; csrf_check();
if(current_user()) redirect('dashboard.php');
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $email=trim($_POST['email']??''); $pass=$_POST['password']??'';
  $st=$pdo->prepare('SELECT * FROM users WHERE email=? AND status="active" LIMIT 1'); $st->execute([$email]); $u=$st->fetch();
  if($u && password_verify($pass,$u['password_hash'])){ $_SESSION['user']=['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']]; redirect('dashboard.php'); }
  else $error='Invalid email or password';
}
?>
<!doctype html><html><head><title>University Teacher Portal</title><link rel="stylesheet" href="assets/style.css"></head><body>
<div class="login card"><h2>University Teacher Portal</h2><p class="muted">Assignment, quiz, homework and student management system.</p>
<?php if($error): ?><p class="badge late"><?=h($error)?></p><?php endif; ?>
<form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><label>Email</label><input name="email" type="email" required><label>Password</label><input name="password" type="password" required><button>Login</button></form>
<p class="small muted">Default admin: admin@university.local / Admin@12345</p></div></body></html>

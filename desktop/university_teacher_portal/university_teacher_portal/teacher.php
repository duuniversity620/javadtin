<?php require 'header.php'; require_role('teacher'); csrf_check(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $action=$_POST['action']??'';
  if($action==='student'){
    $name=trim($_POST['name']); $email=trim($_POST['email']); $sid=trim($_POST['student_id']); $pass=$_POST['password'] ?: 'Student@12345';
    $st=$pdo->prepare('INSERT INTO users(name,email,password_hash,role,student_id) VALUES(?,?,?,?,?)');
    $st->execute([$name,$email,password_hash($pass,PASSWORD_DEFAULT),'student',$sid]); $msg="Student created. Default/password used: $pass";
  }
  if($action==='work'){
    $type=$_POST['type']; $title=trim($_POST['title']); $desc=trim($_POST['description']); $due=$_POST['due_at']; $duration=(int)$_POST['duration_minutes'];
    $filename=null; $stored=null;
    if(!empty($_FILES['file']['name'])){
      global $allowed_ext; $ext=strtolower(pathinfo($_FILES['file']['name'],PATHINFO_EXTENSION));
      if(!in_array($ext,$allowed_ext)) die('File type not allowed'); if($_FILES['file']['size']>MAX_UPLOAD_BYTES) die('File too large');
      $filename=basename($_FILES['file']['name']); $stored=bin2hex(random_bytes(16)).'.'.$ext; move_uploaded_file($_FILES['file']['tmp_name'],UPLOAD_DIR.$stored);
    }
    $st=$pdo->prepare('INSERT INTO works(type,title,description,due_at,duration_minutes,file_name,stored_name,created_by) VALUES(?,?,?,?,?,?,?,?)');
    $st->execute([$type,$title,$desc,$due,$duration,$filename,$stored,current_user()['id']]); $msg='Work created successfully';
  }
}
$students=$pdo->query("SELECT * FROM users WHERE role='student' ORDER BY id DESC")->fetchAll();
$works=$pdo->query("SELECT w.*, u.name teacher FROM works w JOIN users u ON u.id=w.created_by ORDER BY w.id DESC")->fetchAll();
$subs=$pdo->query("SELECT s.*, w.title, u.name student, u.student_id FROM submissions s JOIN works w ON w.id=s.work_id JOIN users u ON u.id=s.student_id ORDER BY s.id DESC")->fetchAll();
?>
<?php if($msg): ?><div class="card"><span class="badge ok"><?=h($msg)?></span></div><?php endif; ?>
<div class="grid"><div class="card"><h2>Create Student ID</h2><form method="post"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="student"><label>Student Name</label><input name="name" required><label>Email/Login</label><input name="email" type="email" required><label>Student ID</label><input name="student_id" required><label>Password</label><input name="password" placeholder="Student@12345"><button>Create Student</button></form></div>
<div class="card"><h2>Create Assignment / Quiz / Homework</h2><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="action" value="work"><label>Type</label><select name="type"><option>assignment</option><option>quiz</option><option>homework</option><option>viva</option></select><label>Title</label><input name="title" required><label>Description</label><textarea name="description"></textarea><label>Due Date & Time</label><input name="due_at" type="datetime-local" required><label>Timer After Download / Duration Minutes</label><input name="duration_minutes" type="number" value="60" min="1"><label>Attach File</label><input type="file" name="file"><button>Create Work</button></form></div></div>
<div class="card"><h2>Students</h2><table><tr><th>Name</th><th>Student ID</th><th>Email</th></tr><?php foreach($students as $s): ?><tr><td><?=h($s['name'])?></td><td><?=h($s['student_id'])?></td><td><?=h($s['email'])?></td></tr><?php endforeach; ?></table></div>
<div class="card"><h2>Created Work</h2><table><tr><th>Type</th><th>Title</th><th>Due</th><th>Timer</th><th>File</th></tr><?php foreach($works as $w): ?><tr><td><?=h($w['type'])?></td><td><?=h($w['title'])?></td><td><?=h($w['due_at'])?></td><td><?=h($w['duration_minutes'])?> min</td><td><?= $w['file_name'] ? '<a href="download.php?id='.h($w['id']).'">Download</a>' : '-' ?></td></tr><?php endforeach; ?></table></div>
<div class="card"><h2>Student Submissions</h2><table><tr><th>Student</th><th>ID</th><th>Work</th><th>Submitted</th><th>File</th></tr><?php foreach($subs as $s): ?><tr><td><?=h($s['student'])?></td><td><?=h($s['student_id'])?></td><td><?=h($s['title'])?></td><td><?=h($s['submitted_at'])?></td><td><a href="submission_download.php?id=<?=h($s['id'])?>">Download</a></td></tr><?php endforeach; ?></table></div>
<?php require 'footer.php'; ?>

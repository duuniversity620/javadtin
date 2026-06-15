<?php require 'header.php'; require_role('student'); csrf_check(); $msg='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $work_id=(int)$_POST['work_id']; $ext=strtolower(pathinfo($_FILES['answer']['name'],PATHINFO_EXTENSION)); global $allowed_ext;
  if(!in_array($ext,$allowed_ext)) die('File type not allowed'); if($_FILES['answer']['size']>MAX_UPLOAD_BYTES) die('File too large');
  $stored=bin2hex(random_bytes(16)).'.'.$ext; move_uploaded_file($_FILES['answer']['tmp_name'],UPLOAD_DIR.$stored);
  $st=$pdo->prepare('INSERT INTO submissions(work_id,student_id,file_name,stored_name,submitted_at) VALUES(?,?,?,?,NOW())');
  $st->execute([$work_id,current_user()['id'],basename($_FILES['answer']['name']),$stored]); $msg='Submitted successfully';
}
$works=$pdo->query('SELECT * FROM works ORDER BY due_at ASC')->fetchAll();
?>
<?php if($msg): ?><div class="card"><span class="badge ok"><?=h($msg)?></span></div><?php endif; ?>
<div class="card"><h2>My Assignments, Quizzes, Viva and Homework</h2><table><tr><th>Type</th><th>Title</th><th>Due</th><th>Timer</th><th>Question File</th><th>Submit</th></tr>
<?php foreach($works as $w): $due=strtotime($w['due_at']); $late=$due<time(); ?><tr><td><?=h($w['type'])?></td><td><strong><?=h($w['title'])?></strong><br><span class="muted small"><?=h($w['description'])?></span></td><td><span class="badge <?=$late?'late':'warn'?>"><?=h($w['due_at'])?></span></td><td><span class="timer" data-due="<?=h(date('c',$due))?>"></span><br><span class="small muted">Duration after download: <?=h($w['duration_minutes'])?> min</span></td><td><?= $w['file_name'] ? '<a class="btn secondary" href="download.php?id='.h($w['id']).'">Download</a>' : '-' ?></td><td><form method="post" enctype="multipart/form-data"><input type="hidden" name="csrf" value="<?=csrf_token()?>"><input type="hidden" name="work_id" value="<?=h($w['id'])?>"><input type="file" name="answer" required><button>Upload Answer</button></form></td></tr><?php endforeach; ?></table></div>
<script>function tick(){document.querySelectorAll('.timer').forEach(el=>{let d=new Date(el.dataset.due).getTime()-Date.now(); if(d<=0){el.textContent='Time expired';return;} let h=Math.floor(d/3600000),m=Math.floor((d%3600000)/60000),s=Math.floor((d%60000)/1000); el.textContent=h+'h '+m+'m '+s+'s left';});} setInterval(tick,1000); tick();</script>
<?php require 'footer.php'; ?>

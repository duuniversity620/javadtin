<?php require 'header.php'; $u=current_user(); ?>
<div class="hero"><h1>Welcome, <?=h($u['name'])?></h1><p>Manage assignments, quizzes, homework, submissions and student records.</p></div>
<div class="grid" style="margin-top:18px">
<?php if(in_array($u['role'],['admin','teacher'])): ?>
<div class="card"><h3>Teacher Panel</h3><p>Create students, assignments, quizzes and review submissions.</p><a class="btn" href="teacher.php">Open Teacher Panel</a></div>
<?php endif; ?>
<?php if($u['role']==='student'): ?>
<div class="card"><h3>Student Panel</h3><p>Download assignments, see countdown timer and upload completed work.</p><a class="btn" href="student.php">Open Student Panel</a></div>
<?php endif; ?>
<div class="card"><h3>Security</h3><p class="muted">Uploads are validated by extension and file size. Downloads are served through PHP access checks.</p></div>
</div><?php require 'footer.php'; ?>

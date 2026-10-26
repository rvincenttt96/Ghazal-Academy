<?php
$title = 'Observation Forms';
require_once 'layout.php';
require_once '../../includes/jdate.php';
require_once '../../includes/db.php';
require_role('teacher');

$teacher_id = $_SESSION['user_id'];

// Fetch observation forms for this teacher's classes
$stmt = $pdo->prepare("
    SELECT 
        of.id,
        of.date,
        of.class_level,
        of.number_of_students,
        of.observer_name,
        c.title as class_title,
        t.title as term_title
    FROM observation_forms of
    JOIN classes c ON of.class_id = c.id
    JOIN terms t ON c.term_id = t.id
    WHERE of.teacher_id = ?
    ORDER BY of.date DESC, of.created_at DESC
");
$stmt->execute([$teacher_id]);
$forms = $stmt->fetchAll();
?>

<link href="../../assets/css/teacher/observation-forms.css?v=<?= time() ?>" rel="stylesheet">

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Your Observation Records</h5>
    </div>
    <div class="card-body">
        <?php if (empty($forms)): ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                No observation forms have been submitted for your classes yet.
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Observer</th>
                            <th>Level</th>
                            <th>Students</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($forms as $form): ?>
                        <tr>
                            <td><?= jdate('Y/m/d', strtotime($form['date'])) ?></td>
                            <td><?= htmlspecialchars($form['class_title']) ?></td>
                            <td><?= htmlspecialchars($form['term_title']) ?></td>
                            <td><?= htmlspecialchars($form['observer_name']) ?></td>
                            <td><?= htmlspecialchars($form['class_level'] ?? '-') ?></td>
                            <td><?= $form['number_of_students'] ?? '-' ?></td>
                            <td>
                                <a href="observation-form-view.php?id=<?= $form['id'] ?>" 
                                   class="btn btn-sm btn-primary">View Details</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'layout-end.php'; ?>
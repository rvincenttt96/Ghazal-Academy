<?php
$title = 'Observation Form Details';
require_once 'layout.php';
require_once '../../includes/db.php';
require_role('teacher');

$form_id = (int)($_GET['id'] ?? 0);
if (!$form_id) {
    header("Location: observation-forms.php?error=Form not found.");
    exit;
}

$teacher_id = $_SESSION['user_id'];

// Fetch form details
$stmt = $pdo->prepare("
    SELECT 
        of.*,
        c.title as class_title,
        t.title as term_title,
        u.full_name as teacher_full_name
    FROM observation_forms of
    JOIN classes c ON of.class_id = c.id
    JOIN terms t ON c.term_id = t.id
    JOIN users u ON of.teacher_id = u.id
    WHERE of.id = ? AND of.teacher_id = ?
");
$stmt->execute([$form_id, $teacher_id]);
$form = $stmt->fetch();

if (!$form) {
    header("Location: observation-forms.php?error=Form not found or access denied.");
    exit;
}

?>
<link href="../../assets/css/teacher/observation-form-view.css?v=<?= time() ?>" rel="stylesheet">

<div class="dashboard-header">
    <h1 class="mb-0">Observation Form Details</h1>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Class: <?= htmlspecialchars($form['class_title']) ?> | Term: <?= htmlspecialchars($form['term_title']) ?></h5>
    </div>
    <div class="card-body">
        <!-- General Info -->
        <div class="row general-info-card">
            <div class="col-md-3">
                <strong>Observer:</strong><br>
                <?= htmlspecialchars($form['observer_name']) ?>
            </div>
            <div class="col-md-3">
                <strong>Date:</strong><br>
                <?= date('Y/m/d', strtotime($form['date'])) ?>
            </div>
            <div class="col-md-3">
                <strong>Class Level:</strong><br>
                <?= htmlspecialchars($form['class_level'] ?? 'N/A') ?>
            </div>
            <div class="col-md-3">
                <strong>Students:</strong><br>
                <?= $form['number_of_students'] ?? 'N/A' ?>
            </div>
        </div>

<!-- Section 1: Lesson Planning & Organization -->
<div class="observation-section">
    <h5 class="mb-3">1. Lesson Planning & Organization</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li>
                    <i class="fas fa-<?= ($form['lesson_aims'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Lesson aims are clear and achievable
                </li>
                <li>
                    <i class="fas fa-<?= ($form['lesson_stages'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Lesson stages are logically sequenced (warm-up, presentation, practice, production)
                </li>
                <li>
                    <i class="fas fa-<?= ($form['lesson_timing'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Lesson timing is realistic and well-paced
                </li>
                <li>
                    <i class="fas fa-<?= ($form['anticipates_problems'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Anticipates potential problems and provides solutions
                </li>
            </ul>
        </div>
        <div class="col-md-6">
		    <div class="comments-section">
       			 <strong>Comments:</strong>
       			 <p><?= nl2br(htmlspecialchars($form['lesson_comments'] ?? 'No comments')) ?></p>
    		</div>          
        </div>
    </div>
</div>

<!-- Section 2: Classroom Management -->
<div class="observation-section">
    <h5 class="mb-3">2. Classroom Management</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li>
                    <i class="fas fa-<?= ($form['seating_arrangement'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Effective seating arrangement
                </li>
                <li>
                    <i class="fas fa-<?= ($form['clear_instructions'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Clear instructions for transitions between activities
                </li>
                <li>
                    <i class="fas fa-<?= ($form['monitoring_students'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Monitoring students during pair/group work
                </li>
                <li>
                    <i class="fas fa-<?= ($form['fair_attention'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Fair distribution of teacher’s attention among students
                </li>
            </ul>
        </div>
        <div class="col-md-6">
			<div class="comments-section">
            <strong>Comments:</strong>
            <p><?= nl2br(htmlspecialchars($form['management_comments'] ?? 'No comments')) ?></p>
			</div>
        </div>
    </div>
</div>

<!-- Section 3: Teaching Methodology -->
<div class="observation-section">
    <h5 class="mb-3">3. Teaching Methodology</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li>
                    <i class="fas fa-<?= ($form['stt_vs_ttt'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    STT vs TTT
                </li>
                <li>
                    <i class="fas fa-<?= ($form['elicitation_ccqs'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Use of elicitation and concept checking questions (CCQs)
                </li>
                <li>
                    <i class="fas fa-<?= ($form['error_correction'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Clear error correction techniques (self-correction, peer correction, teacher correction)
                </li>
                <li>
                    <i class="fas fa-<?= ($form['interaction_patterns'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Variety of interaction patterns (individual, pair, group, whole class)
                </li>
            </ul>
        </div>
        <div class="col-md-6">
			<div class="comments-section">
            <strong>Comments:</strong>
            <p><?= nl2br(htmlspecialchars($form['methodology_comments'] ?? 'No comments')) ?></p>
			</div>
        </div>
    </div>
</div>

<!-- Section 4: Teacher’s Language Use -->
<div class="observation-section">
    <h5 class="mb-3">4. Teacher’s Language Use</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li>
                    <i class="fas fa-<?= ($form['graded_language'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Graded language appropriate to students’ level
                </li>
                <li>
                    <i class="fas fa-<?= ($form['models_language'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Models target language accurately and clearly
                </li>
                <li>
                    <i class="fas fa-<?= ($form['uses_visuals'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Uses gestures, examples, and visuals to aid comprehension
                </li>
            </ul>
        </div>
        <div class="col-md-6">
			<div class="comments-section">
            <strong>Comments:</strong>
            <p><?= nl2br(htmlspecialchars($form['language_comments'] ?? 'No comments')) ?></p>
			</div>
        </div>
    </div>
</div>

<!-- Section 5: Student Engagement -->
<div class="observation-section">
    <h5 class="mb-3">5. Student Engagement</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li>
                    <i class="fas fa-<?= ($form['encourages_english'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Students are encouraged to speak in English
                </li>
                <li>
                    <i class="fas fa-<?= ($form['shows_motivation'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Students show motivation and interest
                </li>
                <li>
                    <i class="fas fa-<?= ($form['equal_opportunities'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Teacher gives equal opportunities to weaker/stronger learners
                </li>
                <li>
                    <i class="fas fa-<?= ($form['checks_understanding'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Teacher checks students’ understanding (ICQs – Instruction Checking Questions)
                </li>
            </ul>
        </div>
        <div class="col-md-6">
			<div class="comments-section">
            <strong>Comments:</strong>
            <p><?= nl2br(htmlspecialchars($form['engagement_comments'] ?? 'No comments')) ?></p>
			</div>
        </div>
    </div>
</div>

<!-- Section 6: Use of Materials & Technology -->
<div class="observation-section">
    <h5 class="mb-3">6. Use of Materials & Technology</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li>
                    <i class="fas fa-<?= ($form['relevant_materials'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Materials are relevant, authentic, and adapted to the class
                </li>
                <li>
                    <i class="fas fa-<?= ($form['clear_boardwork'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Whiteboard/boardwork is clear and well-organized
                </li>
                <li>
                    <i class="fas fa-<?= ($form['technology_supports'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Technology (if used) supports learning, not distracts
                </li>
            </ul>
        </div>
        <div class="col-md-6">
			<div class="comments-section">
            <strong>Comments:</strong>
            <p><?= nl2br(htmlspecialchars($form['materials_comments'] ?? 'No comments')) ?></p>
			</div>
        </div>
    </div>
</div>

<!-- Section 7: Teacher’s Attitude & Professionalism -->
<div class="observation-section">
    <h5 class="mb-3">7. Teacher’s Attitude & Professionalism</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li>
                    <i class="fas fa-<?= ($form['punctual_prepared'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Teacher is punctual and well-prepared
                </li>
                <li>
                    <i class="fas fa-<?= ($form['positive_rapport'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Builds positive rapport with students
                </li>
                <li>
                    <i class="fas fa-<?= ($form['encourages_confidence'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Encourages confidence and participation
                </li>
                <li>
                    <i class="fas fa-<?= ($form['enthusiasm_patience'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Demonstrates enthusiasm and patience
                </li>
            </ul>
        </div>
        <div class="col-md-6">
			<div class="comments-section">
            <strong>Comments:</strong>
            <p><?= nl2br(htmlspecialchars($form['attitude_comments'] ?? 'No comments')) ?></p>
			</div>
        </div>
    </div>
</div>

<!-- Section 8: Assessment & Feedback -->
<div class="observation-section">
    <h5 class="mb-3">8. Assessment & Feedback</h5>
    <div class="row">
        <div class="col-md-6">
            <ul class="list-unstyled">
                <li>
                    <i class="fas fa-<?= ($form['constructive_feedback'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Provides constructive feedback during activities
                </li>
                <li>
                    <i class="fas fa-<?= ($form['formative_assessment'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Uses formative assessment techniques (e.g., observation, questioning)
                </li>
                <li>
                    <i class="fas fa-<?= ($form['tracks_progress'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Tracks individual student progress
                </li>
                <li>
                    <i class="fas fa-<?= ($form['clear_summary'] == '1') ? 'check text-success' : 'times text-muted' ?>"></i> 
                    Ends lesson with a clear summary/review
                </li>
            </ul>
        </div>
        <div class="col-md-6">
			<div class="comments-section">
            <strong>Comments:</strong>
            <p><?= nl2br(htmlspecialchars($form['assessment_comments'] ?? 'No comments')) ?></p>
			</div>
        </div>
    </div>
</div>

        <!-- Overall Evaluation -->
        <div class="overall-evaluation">
   			 <h5>Overall Evaluation</h5>
            <div class="row">
                <div class="col-md-4">
                    <strong>Strengths:</strong>
                    <p><?= nl2br(htmlspecialchars($form['strengths'] ?? 'N/A')) ?></p>
                </div>
                <div class="col-md-4">
                    <strong>Areas for Improvement:</strong>
                    <p><?= nl2br(htmlspecialchars($form['areas_for_improvement'] ?? 'N/A')) ?></p>
                </div>
                <div class="col-md-4">
                    <strong>Suggestions:</strong>
                    <p><?= nl2br(htmlspecialchars($form['suggestions'] ?? 'N/A')) ?></p>
                </div>
            </div>
        </div>

        <!-- Additional Notes -->
        <div class="additional-notes">
            <strong>Additional Notes:</strong>
            <p><?= nl2br(htmlspecialchars($form['additional_notes'] ?? 'N/A')) ?></p>
        </div>

        <div class="text-end">
            <a href="observation-forms.php" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>

<?php require_once 'layout-end.php'; ?>
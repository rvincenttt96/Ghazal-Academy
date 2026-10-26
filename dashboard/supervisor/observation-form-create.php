<?php
$title = 'Classroom Observation & Teacher Evaluation Form';
require_once 'layout.php';
require_once '../../includes/db.php';
require_role('supervisor');

$class_id = (int)($_GET['class_id'] ?? 0);
if (!$class_id) {
    header("Location: observation-forms.php?error=No class selected.");
    exit;
}

// Get class info
$stmt = $pdo->prepare("
    SELECT c.id, c.title, u.full_name as teacher_name, u.id as teacher_id
    FROM classes c
    JOIN users u ON c.teacher_id = u.id
    WHERE c.id = ? AND c.is_deleted = 0
");
$stmt->execute([$class_id]);
$class_info = $stmt->fetch();

if (!$class_info) {
    header("Location: observation-forms.php?error=Class not found.");
    exit;
}
?>

<link href="../../assets/css/supervisor/observation-form-create.css?v=<?= time() ?>" rel="stylesheet">

<div class="dashboard-header">
    <h1 class="mb-0">Classroom Observation & Teacher Evaluation Form</h1>
    <small class="text-muted">Class: <?= htmlspecialchars($class_info['title']) ?> | Teacher: <?= htmlspecialchars($class_info['teacher_name']) ?></small>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="save-observation.php">
            <input type="hidden" name="class_id" value="<?= $class_id ?>">
            <input type="hidden" name="teacher_id" value="<?= $class_info['teacher_id'] ?>">
            <input type="hidden" name="observer_id" value="<?= $_SESSION['user_id'] ?>">
            
            
            <!-- General Info -->
            <div class="row mb-4">
				<div class="col-md-4">
					<label class="form-label">Observer's Name *</label>
					<?php
					$stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
					$stmt->execute([$_SESSION['user_id']]);
					$observer_name = $stmt->fetchColumn();
					?>
					<input type="text" class="form-control" value="<?= htmlspecialchars($observer_name) ?>" readonly>
				</div>
                <div class="col-md-4">
                    <label class="form-label">Date *</label>
                    <input type="date" name="date" class="form-control" required value="<?= date('Y-m-d') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Class Level</label>
                    <input type="text" name="class_level" class="form-control" placeholder="e.g., Elementary">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Number of Students</label>
                    <input type="number" name="number_of_students" class="form-control" min="1" max="50">
                </div>
            </div>

            <!-- Section 1: Lesson Planning & Organization -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">1. Lesson Planning & Organization</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lesson_aims" id="lesson_aims">
                            <label class="form-check-label" for="lesson_aims">Lesson aims are clear and achievable</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lesson_stages" id="lesson_stages">
                            <label class="form-check-label" for="lesson_stages">Lesson stages are logically sequenced (warm-up, presentation, practice, production)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="lesson_timing" id="lesson_timing">
                            <label class="form-check-label" for="lesson_timing">Lesson timing is realistic and well-paced</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="anticipates_problems" id="anticipates_problems">
                            <label class="form-check-label" for="anticipates_problems">Anticipates potential problems and provides solutions</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comments:</label>
                        <textarea name="lesson_comments" class="form-control" rows="3" placeholder="Comments..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 2: Classroom Management -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">2. Classroom Management</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="seating_arrangement" id="seating_arrangement">
                            <label class="form-check-label" for="seating_arrangement">Effective seating arrangement</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="clear_instructions" id="clear_instructions">
                            <label class="form-check-label" for="clear_instructions">Clear instructions for transitions between activities</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="monitoring_students" id="monitoring_students">
                            <label class="form-check-label" for="monitoring_students">Monitoring students during pair/group work</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="fair_attention" id="fair_attention">
                            <label class="form-check-label" for="fair_attention">Fair distribution of teacher’s attention among students</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comments:</label>
                        <textarea name="management_comments" class="form-control" rows="3" placeholder="Comments..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Teaching Methodology -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">3. Teaching Methodology</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="stt_vs_ttt" id="stt_vs_ttt">
                            <label class="form-check-label" for="stt_vs_ttt">STT vs TTT</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="elicitation_ccqs" id="elicitation_ccqs">
                            <label class="form-check-label" for="elicitation_ccqs">Use of elicitation and concept checking questions (CCQs)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="error_correction" id="error_correction">
                            <label class="form-check-label" for="error_correction">Clear error correction techniques (self-correction, peer correction, teacher correction)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="interaction_patterns" id="interaction_patterns">
                            <label class="form-check-label" for="interaction_patterns">Variety of interaction patterns (individual, pair, group, whole class)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comments:</label>
                        <textarea name="methodology_comments" class="form-control" rows="3" placeholder="Comments..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 4: Teacher’s Language Use -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">4. Teacher’s Language Use</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="graded_language" id="graded_language">
                            <label class="form-check-label" for="graded_language">Graded language appropriate to students’ level</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="models_language" id="models_language">
                            <label class="form-check-label" for="models_language">Models target language accurately and clearly</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="uses_visuals" id="uses_visuals">
                            <label class="form-check-label" for="uses_visuals">Uses gestures, examples, and visuals to aid comprehension</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comments:</label>
                        <textarea name="language_comments" class="form-control" rows="3" placeholder="Comments..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 5: Student Engagement -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">5. Student Engagement</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="encourages_english" id="encourages_english">
                            <label class="form-check-label" for="encourages_english">Students are encouraged to speak in English</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="shows_motivation" id="shows_motivation">
                            <label class="form-check-label" for="shows_motivation">Students show motivation and interest</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="equal_opportunities" id="equal_opportunities">
                            <label class="form-check-label" for="equal_opportunities">Teacher gives equal opportunities to weaker/stronger learners</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="checks_understanding" id="checks_understanding">
                            <label class="form-check-label" for="checks_understanding">Teacher checks students’ understanding (ICQs – Instruction Checking Questions)</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comments:</label>
                        <textarea name="engagement_comments" class="form-control" rows="3" placeholder="Comments..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 6: Use of Materials & Technology -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">6. Use of Materials & Technology</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="relevant_materials" id="relevant_materials">
                            <label class="form-check-label" for="relevant_materials">Materials are relevant, authentic, and adapted to the class</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="clear_boardwork" id="clear_boardwork">
                            <label class="form-check-label" for="clear_boardwork">Whiteboard/boardwork is clear and well-organized</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="technology_supports" id="technology_supports">
                            <label class="form-check-label" for="technology_supports">Technology (if used) supports learning, not distracts</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comments:</label>
                        <textarea name="materials_comments" class="form-control" rows="3" placeholder="Comments..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 7: Teacher’s Attitude & Professionalism -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">7. Teacher’s Attitude & Professionalism</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="punctual_prepared" id="punctual_prepared">
                            <label class="form-check-label" for="punctual_prepared">Teacher is punctual and well-prepared</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="positive_rapport" id="positive_rapport">
                            <label class="form-check-label" for="positive_rapport">Builds positive rapport with students</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="encourages_confidence" id="encourages_confidence">
                            <label class="form-check-label" for="encourages_confidence">Encourages confidence and participation</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="enthusiasm_patience" id="enthusiasm_patience">
                            <label class="form-check-label" for="enthusiasm_patience">Demonstrates enthusiasm and patience</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comments:</label>
                        <textarea name="attitude_comments" class="form-control" rows="3" placeholder="Comments..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 8: Assessment & Feedback -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">8. Assessment & Feedback</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="constructive_feedback" id="constructive_feedback">
                            <label class="form-check-label" for="constructive_feedback">Provides constructive feedback during activities</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="formative_assessment" id="formative_assessment">
                            <label class="form-check-label" for="formative_assessment">Uses formative assessment techniques (e.g., observation, questioning)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tracks_progress" id="tracks_progress">
                            <label class="form-check-label" for="tracks_progress">Tracks individual student progress</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="clear_summary" id="clear_summary">
                            <label class="form-check-label" for="clear_summary">Ends lesson with a clear summary/review</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Comments:</label>
                        <textarea name="assessment_comments" class="form-control" rows="3" placeholder="Comments..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Overall Evaluation -->
            <div class="observation-section mb-4 p-3 border rounded">
                <h5 class="mb-3">Overall Evaluation</h5>
                <div class="row">
                    <div class="col-md-4">
                        <label class="form-label">Strengths:</label>
                        <textarea name="strengths" class="form-control" rows="3" placeholder="Strengths..."></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Areas for Improvement:</label>
                        <textarea name="areas_for_improvement" class="form-control" rows="3" placeholder="Areas for Improvement..."></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Suggestions:</label>
                        <textarea name="suggestions" class="form-control" rows="3" placeholder="Suggestions..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div class="mb-4">
                <label class="form-label">Additional Notes:</label>
                <textarea name="additional_notes" class="form-control" rows="4" placeholder="Additional notes..."></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Save Observation Form</button>
                <a href="observation-forms.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?php require_once 'layout-end.php'; ?>
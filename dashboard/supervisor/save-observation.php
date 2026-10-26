<?php
require_once '../../includes/db.php';
require_once '../../includes/auth.php';
require_role('supervisor');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request.');
    }
    
    // Get observer full name
    $stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $observer_full_name = $stmt->fetchColumn();
    
    $required_fields = ['class_id', 'teacher_id', 'date'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Field {$field} is required.");
        }
    }
    
    $class_id = (int)$_POST['class_id'];
    $teacher_id = (int)$_POST['teacher_id'];
    $observer_id = (int)$_SESSION['user_id'];
    $observer_name = $observer_full_name;
    $date = $_POST['date'];
    
    if (!strtotime($date)) {
        throw new Exception('Invalid date format.');
    }
    
    // Verify class exists
    $stmt = $pdo->prepare("SELECT id FROM classes WHERE id = ? AND is_deleted = 0");
    $stmt->execute([$class_id]);
    if (!$stmt->fetch()) {
        throw new Exception('Class not found.');
    }
    
    $pdo->beginTransaction();
    
    // INSERT with all 45 columns
    $stmt = $pdo->prepare("
        INSERT INTO observation_forms (
            class_id, teacher_id, observer_id, observer_name, date, 
            class_level, number_of_students,
            lesson_aims, lesson_stages, lesson_timing, anticipates_problems, lesson_comments,
            seating_arrangement, clear_instructions, monitoring_students, fair_attention, management_comments,
            stt_vs_ttt, elicitation_ccqs, error_correction, interaction_patterns, methodology_comments,
            graded_language, models_language, uses_visuals, language_comments,
            encourages_english, shows_motivation, equal_opportunities, checks_understanding, engagement_comments,
            relevant_materials, clear_boardwork, technology_supports, materials_comments,
            punctual_prepared, positive_rapport, encourages_confidence, enthusiasm_patience, attitude_comments,
            constructive_feedback, formative_assessment, tracks_progress, clear_summary, assessment_comments,
            strengths, areas_for_improvement, suggestions, additional_notes
        ) VALUES (
            ?, ?, ?, ?, ?, 
            ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?
        )
    ");
    
    $stmt->execute([
        $class_id, $teacher_id, $observer_id, $observer_name, $date,
        $_POST['class_level'] ?? null,
        !empty($_POST['number_of_students']) ? (int)$_POST['number_of_students'] : null,
        
        isset($_POST['lesson_aims']) ? 1 : 0,
        isset($_POST['lesson_stages']) ? 1 : 0,
        isset($_POST['lesson_timing']) ? 1 : 0,
        isset($_POST['anticipates_problems']) ? 1 : 0,
        $_POST['lesson_comments'] ?? '',
        
        isset($_POST['seating_arrangement']) ? 1 : 0,
        isset($_POST['clear_instructions']) ? 1 : 0,
        isset($_POST['monitoring_students']) ? 1 : 0,
        isset($_POST['fair_attention']) ? 1 : 0,
        $_POST['management_comments'] ?? '',
        
        isset($_POST['stt_vs_ttt']) ? 1 : 0,
        isset($_POST['elicitation_ccqs']) ? 1 : 0,
        isset($_POST['error_correction']) ? 1 : 0,
        isset($_POST['interaction_patterns']) ? 1 : 0,
        $_POST['methodology_comments'] ?? '',
        
        isset($_POST['graded_language']) ? 1 : 0,
        isset($_POST['models_language']) ? 1 : 0,
        isset($_POST['uses_visuals']) ? 1 : 0,
        $_POST['language_comments'] ?? '',
        
        isset($_POST['encourages_english']) ? 1 : 0,
        isset($_POST['shows_motivation']) ? 1 : 0,
        isset($_POST['equal_opportunities']) ? 1 : 0,
        isset($_POST['checks_understanding']) ? 1 : 0,
        $_POST['engagement_comments'] ?? '',
        
        isset($_POST['relevant_materials']) ? 1 : 0,
        isset($_POST['clear_boardwork']) ? 1 : 0,
        isset($_POST['technology_supports']) ? 1 : 0,
        $_POST['materials_comments'] ?? '',
        
        isset($_POST['punctual_prepared']) ? 1 : 0,
        isset($_POST['positive_rapport']) ? 1 : 0,
        isset($_POST['encourages_confidence']) ? 1 : 0,
        isset($_POST['enthusiasm_patience']) ? 1 : 0,
        $_POST['attitude_comments'] ?? '',
        
        isset($_POST['constructive_feedback']) ? 1 : 0,
        isset($_POST['formative_assessment']) ? 1 : 0,
        isset($_POST['tracks_progress']) ? 1 : 0,
        isset($_POST['clear_summary']) ? 1 : 0,
        $_POST['assessment_comments'] ?? '',
        
        $_POST['strengths'] ?? '',
        $_POST['areas_for_improvement'] ?? '',
        $_POST['suggestions'] ?? '',
        $_POST['additional_notes'] ?? ''
    ]);
    
    $pdo->commit();
    
    // ریدایرکت به صفحه لیست فرم‌ها
    header("Location: observation-forms.php?success=Observation form saved successfully.");
    exit;
    
} catch (Exception $e) {
    $pdo->rollBack();
    
    // ریدایرکت با خطا
    header("Location: observation-forms.php?error=" . urlencode($e->getMessage()));
    exit;
}
?>
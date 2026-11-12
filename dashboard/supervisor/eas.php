<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/terms.php';
require_role('supervisor'); 

$current_page = 'eas.php';
$title = 'EAS Dashboard';

try {
    $stmt = $pdo->prepare("
        SELECT 
            c.id as main_class_id,
            c.title as class_title,
            c.schedule_type,
            c.class_type,
            c.schedule, 
            c.class_time,
            u_teacher.full_name as teacher_name,
            e.*,
            c.teacher_id,
            c.online_link as class_online_link
        FROM classes c
        JOIN terms t ON c.term_id = t.id
        LEFT JOIN users u_teacher ON c.teacher_id = u_teacher.id
        LEFT JOIN eas_management e ON c.id = e.class_id
        WHERE c.is_deleted = 0 AND " . term_sql_teaching('t') . " AND (c.is_archived = 0 OR c.is_archived IS NULL)
        ORDER BY c.id DESC
    ");
    $stmt->execute();
    $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $students_by_class = [];
    $class_ids = [];
    foreach ($classes as $row) {
        $cid = (int)($row['main_class_id'] ?? 0);
        if ($cid > 0) {
            $class_ids[$cid] = $cid;
        }
    }
    if ($class_ids) {
        $ids = array_values($class_ids);
        $place = implode(',', array_fill(0, count($ids), '?'));
        $st_students = $pdo->prepare("
            SELECT u.id, u.full_name, u.username, cs.class_id
            FROM users u
            JOIN class_students cs ON u.id = cs.user_id
            WHERE cs.class_id IN ($place)
              AND u.role = 'student'
              AND IFNULL(u.is_deleted, 0) = 0
            ORDER BY u.full_name
        ");
        $st_students->execute($ids);
        foreach ($st_students->fetchAll(PDO::FETCH_ASSOC) as $student_row) {
            $students_by_class[(int)$student_row['class_id']][] = $student_row;
        }
    }

} catch (PDOException $e) {
    die("<div style='background:#e74c3c; color:white; padding:20px; border-radius:10px; margin:20px; text-align:left; direction:ltr;'>
            <h3>⚠️ Database Error</h3><p>" . $e->getMessage() . "</p>
         </div>");
}

function getDayBadgeClass($type) {
    $type = strtolower($type ?? '');
    if ($type == 'even' || strpos($type, 'زوج') !== false) return ['day-even', 'روزهای زوج', 'even'];
    if ($type == 'odd' || strpos($type, 'فرد') !== false) return ['day-odd', 'روزهای فرد', 'odd'];
    if ($type == 'private' || strpos($type, 'خصوصی') !== false) return ['day-private', 'خصوصی', 'private'];
    return ['day-even', 'نامشخص', 'all']; 
}

require_once 'layout.php';
?>
<link rel="stylesheet" href="../../assets/css/jalalidatepicker.min.css">

<style>
    /* Bright, high-contrast, crisp card & border design for EAS */
    .eas-shell {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    #easShell .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        background: rgba(255, 255, 255, 0.97) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 18px !important;
        padding: 18px 24px !important;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.10) !important;
        color: #0f172a !important;
    }
    #easShell .header-title h1 {
        font-size: 1.65rem;
        font-weight: 900;
        margin: 0 0 4px 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #0f172a !important;
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
    }
    #easShell .header-title h1 span {
        background: #ffe4e6 !important;
        color: #be123c !important;
        border: 1px solid #fecdd3 !important;
        padding: 2px 10px;
        border-radius: 10px;
        font-size: 1.15rem;
        letter-spacing: 0.5px;
    }
    #easShell .header-title p {
        color: #475569 !important;
        font-size: 0.88rem;
        font-weight: 600;
        margin: 0;
    }
    .header-right {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .eas-count-pill {
        background: #f1f5f9;
        color: #1e293b;
        border: 1px solid #cbd5e1;
        padding: 8px 14px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 800;
        white-space: nowrap;
    }
    #easShell .live-clock-container {
        text-align: left;
        background: #f8fafc !important;
        padding: 9px 18px;
        border-radius: 12px;
        border: 1.5px solid #cbd5e1 !important;
        min-width: 175px;
    }
    #easShell .live-time {
        font-size: 1.35rem;
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
        font-weight: 900;
        color: #0f172a !important;
        letter-spacing: 2px;
        line-height: 1.2;
    }
    .live-time span { color: #e11d48; animation: blink 1s infinite; }
    #easShell .live-date { font-size: 0.78rem; color: #475569 !important; font-weight: 600; margin-top: 2px; }
    @keyframes blink { 50% { opacity: 0; } }

    #easShell .controls-section {
        background: rgba(255, 255, 255, 0.97) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: 16px 20px !important;
        border-radius: 16px !important;
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: space-between;
        box-shadow: 0 6px 20px rgba(15, 23, 42, 0.08) !important;
        color: #0f172a !important;
    }
    .filters-left {
        display: flex;
        gap: 14px;
        align-items: center;
        flex-wrap: wrap;
        flex: 1;
        min-width: 0;
    }
    .filter-group {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    #easShell .filter-group label {
        color: #1e293b !important;
        font-size: 0.85rem;
        font-weight: 800;
        white-space: nowrap;
        margin: 0;
    }
    #easShell .custom-select,
    #easShell .eas-search-input {
        background: #ffffff !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: 8px 13px;
        border-radius: 10px !important;
        outline: none;
        font-size: 0.85rem;
        font-weight: 600;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .custom-select { cursor: pointer; }
    #easShell .custom-select option {
        background: #ffffff !important;
        color: #0f172a !important;
    }
    .eas-search-input {
        min-width: 220px;
        flex: 1;
        max-width: 300px;
    }
    #easShell .eas-search-input::placeholder { color: #94a3b8 !important; }
    #easShell .custom-select:focus,
    #easShell .eas-search-input:focus {
        border-color: #e11d48 !important;
        box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.14) !important;
    }

    .export-excel-btn {
        background: #15803d;
        color: #ffffff;
        border: 1.5px solid #166534;
        padding: 9px 16px;
        border-radius: 10px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: 0.2s;
        font-size: 0.85rem;
        white-space: nowrap;
    }
    .export-excel-btn:hover {
        background: #166534;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(21, 128, 61, 0.28);
    }

    #easShell .time-chips-container {
        background: rgba(255, 255, 255, 0.97) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 14px !important;
        padding: 12px 18px !important;
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
        box-shadow: 0 4px 14px rgba(15, 23, 42, 0.06) !important;
        color: #0f172a !important;
    }
    #easShell .time-chips-container label {
        color: #1e293b !important;
        font-size: 0.85rem;
        font-weight: 800;
        margin: 0 0 0 6px;
        white-space: nowrap;
    }
    #easShell .time-chip {
        background: #f8fafc !important;
        border: 1.5px solid #cbd5e1 !important;
        padding: 6px 14px;
        border-radius: 999px;
        color: #334155 !important;
        font-size: 0.84rem;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
        user-select: none;
        white-space: nowrap;
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
    }
    #easShell .time-chip:hover {
        background: #fff1f2 !important;
        color: #be123c !important;
        border-color: #fda4af !important;
    }
    #easShell .time-chip.active {
        background: #e11d48 !important;
        color: #ffffff !important;
        border-color: #be123c !important;
        font-weight: 800;
        box-shadow: 0 3px 10px rgba(225, 29, 72, 0.25) !important;
    }

    .cards-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(min(100%, 380px), 1fr));
        gap: 20px;
        min-width: 0;
    }
    #cardsContainer .eas-card {
        background: #ffffff !important;
        background-color: #ffffff !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        border-radius: 20px !important;
        padding: 20px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, 0.12) !important;
        border: 1.5px solid #cbd5e1 !important;
        display: flex;
        flex-direction: column;
        gap: 16px;
        transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
        min-width: 0;
        max-width: 100%;
        box-sizing: border-box;
        color: #0f172a !important;
    }
    #cardsContainer .eas-card:hover {
        border-color: #94a3b8 !important;
        box-shadow: 0 14px 34px rgba(15, 23, 42, 0.16) !important;
        transform: translateY(-2px);
    }

    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        border-bottom: 1.5px solid #e2e8f0;
        padding-bottom: 14px;
    }
    .class-info { min-width: 0; flex: 1; }
    #cardsContainer .eas-card .class-info h2 {
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
        font-size: 1.2rem;
        font-weight: 900;
        margin: 0 0 6px 0;
        color: #0f172a !important;
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        line-height: 1.4;
    }
    .class-info h2 .class-live-dot.is-on { display: inline-block; flex: 0 0 8px; }
    .class-info .teacher {
        color: #be123c;
        background: #fff1f2;
        border: 1px solid #fecdd3;
        padding: 4px 10px;
        border-radius: 8px;
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
        font-size: 0.85rem;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .class-info .teacher.eas-teacher { cursor: pointer; user-select: none; }
    .class-info .teacher.eas-teacher:hover {
        background: #ffe4e6;
        border-color: #fda4af;
    }
    .eas-join-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-top: 10px;
        background: #dcfce7;
        color: #15803d;
        border: 1.5px solid #22c55e;
        border-radius: 10px;
        padding: 7px 14px;
        font-size: 0.82rem;
        font-weight: 800;
        text-decoration: none;
        width: fit-content;
        transition: 0.2s;
    }
    .eas-join-btn:hover { background: #16a34a; color: #ffffff; border-color: #15803d; }

    .meta-info {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 7px;
        flex-shrink: 0;
    }
    .badges-wrapper {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .type-badge, .day-badge, .time-badge {
        white-space: nowrap !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 28px;
        line-height: 1;
        box-sizing: border-box;
    }
    .type-badge { font-size: 0.76rem; padding: 0 10px; border-radius: 8px; font-weight: 800; }
    .type-online { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
    .type-inperson { background: #f1f5f9; color: #334155; border: 1px solid #cbd5e1; }
    .type-private { background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; }

    .time-badge {
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
        font-size: 0.84rem;
        font-weight: 800;
        background: #ffe4e6;
        color: #9f1239;
        border: 1px solid #fda4af;
        padding: 0 11px;
        border-radius: 8px;
    }
    .day-badge { padding: 0 10px; border-radius: 8px; font-size: 0.76rem; font-weight: 800; }
    .day-even { background: #dbeafe; color: #1d4ed8; border: 1px solid #93c5fd; }
    .day-odd { background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; }
    .day-private { background: #fef3c7; color: #b45309; border: 1px solid #fcd34d; }

    .eas-section-box {
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 13px 14px;
    }

    #cardsContainer .eas-card .section-title {
        font-size: 0.82rem;
        color: #1e293b !important;
        margin-bottom: 10px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 6px;
        padding-bottom: 6px;
        border-bottom: 1px dashed #cbd5e1;
    }

    .student-badges { display: flex; flex-wrap: wrap; gap: 7px; }
    .student-badge {
        background: #ffffff;
        color: #0f172a;
        padding: 5px 11px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 700;
        border: 1.5px solid #cbd5e1;
        transition: 0.15s;
    }
    .student-badge.eas-student {
        cursor: pointer;
        user-select: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .student-badge.eas-student:hover {
        border-color: #e11d48;
        background: #fff1f2;
        color: #9f1239;
    }
    .student-badge-empty {
        background: #f1f5f9;
        color: #64748b;
        border: 1px dashed #cbd5e1;
    }
    .student-file {
        color: #475569;
        background: #f1f5f9;
        padding: 1px 7px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
        direction: ltr;
        unicode-bidi: isolate;
        font-weight: 800;
    }

    #cardsContainer .eas-card .sessions-container {
        background: #f8fafc !important;
        background-color: #f8fafc !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        padding: 14px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0 !important;
        position: relative;
        min-width: 0;
    }
    .sessions-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 8px;
        margin-bottom: 12px;
        padding-bottom: 8px;
        border-bottom: 1px dashed #cbd5e1;
    }
    .sessions-head .section-title {
        margin: 0;
        padding: 0;
        border: none;
        flex: 1;
        min-width: 0;
    }
    .zoom-btn {
        background: #ffffff;
        border: 1.5px solid #cbd5e1;
        color: #1e293b;
        padding: 5px 11px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 800;
        cursor: pointer;
        transition: 0.2s;
        flex-shrink: 0;
    }
    .zoom-btn:hover {
        background: #e11d48;
        color: #ffffff;
        border-color: #be123c;
    }

    .sessions-grid { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-start; transition: 0.3s; }
    .session-item { display: flex; flex-direction: column; align-items: center; gap: 5px; }
    #cardsContainer .eas-card .session-box,
    #zoomModal .session-box {
        width: 34px;
        height: 34px;
        background: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.86rem;
        font-weight: 800;
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
        color: #0f172a !important;
        cursor: pointer;
        user-select: none;
        transition: 0.15s;
    }
    #cardsContainer .eas-card .session-box:hover,
    #zoomModal .session-box:hover { transform: scale(1.07); border-color: #64748b !important; background: #f1f5f9 !important; }
    #cardsContainer .eas-card .session-box.status-tick,
    #zoomModal .session-box.status-tick { background: #dcfce7 !important; color: #15803d !important; border-color: #22c55e !important; }
    #cardsContainer .eas-card .session-box.status-cross,
    #zoomModal .session-box.status-cross { background: #fee2e2 !important; color: #b91c1c !important; border-color: #ef4444 !important; }
    .makeup-box { background: #fef3c7 !important; color: #b45309 !important; border-color: #f59e0b !important; }

    #cardsContainer .eas-card .date-input,
    #zoomModal .date-input {
        width: 64px;
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 6px !important;
        padding: 2px 3px !important;
        color: #0f172a !important;
        font-size: 0.68rem !important;
        font-weight: 700;
        text-align: center;
        font-family: 'YekanBakhFaNum', Tahoma, Arial, sans-serif;
        outline: none;
        transition: 0.2s;
        cursor: pointer;
    }
    body.lg-app .date-input::placeholder { color: #94a3b8 !important; }
    body.lg-app .date-input:focus { border-color: #e11d48 !important; box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.14) !important; }

    .zoomed-mode { gap: 18px !important; justify-content: center; padding: 18px; }
    .zoomed-mode .session-box { width: 58px; height: 58px; font-size: 1.45rem; border-radius: 14px; }
    .zoomed-mode .date-input { width: 82px; font-size: 0.88rem; padding: 4px; }

    .actions-wrapper {
        display: flex;
        flex-direction: column;
        gap: 10px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 14px;
        padding: 12px;
    }
    .action-row { display: flex; gap: 10px; }
    #cardsContainer .eas-card .action-box {
        flex: 1;
        background: #ffffff !important;
        padding: 10px 12px;
        border-radius: 10px;
        border: 1.5px solid #cbd5e1 !important;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .action-box span {
        font-size: 0.82rem;
        font-weight: 800;
        color: #1e293b;
    }

    .custom-checkbox {
        appearance: none;
        width: 22px;
        height: 22px;
        background: #f1f5f9;
        border-radius: 6px;
        border: 2px solid #94a3b8;
        cursor: pointer;
        position: relative;
        margin: 0;
        transition: 0.2s;
        flex-shrink: 0;
    }
    .custom-checkbox:hover { border-color: #475569; }
    .custom-checkbox:checked { background: #16a34a; border-color: #15803d; }
    .custom-checkbox:checked::after {
        content: '✔';
        position: absolute;
        color: #ffffff;
        font-size: 13px;
        font-weight: 900;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    #cardsContainer .eas-card .interview-box {
        background: #f8fafc !important;
        background-color: #f8fafc !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        padding: 14px;
        border-radius: 14px;
        border: 1.5px solid #e2e8f0 !important;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .interview-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 8px;
        border-bottom: 1px dashed #cbd5e1;
    }
    .interview-head .section-title { margin: 0; padding: 0; border: none; }
    #cardsContainer .eas-card .interview-done-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #0f172a !important;
        font-size: 0.8rem;
        font-weight: 800;
        margin: 0;
        cursor: pointer;
    }
    .interview-inputs { display: flex; gap: 10px; }

    #cardsContainer .eas-card .eas-input,
    #cardsContainer .eas-card .eas-textarea {
        width: 100%;
        background: #ffffff !important;
        background-color: #ffffff !important;
        border: 1.5px solid #cbd5e1 !important;
        color: #0f172a !important;
        padding: 9px 12px !important;
        border-radius: 10px !important;
        font-size: 0.85rem;
        font-weight: 600;
        outline: none;
        transition: 0.2s;
    }
    body.lg-app .eas-input::placeholder,
    body.lg-app .eas-textarea::placeholder { color: #94a3b8 !important; font-weight: 500; }
    body.lg-app .eas-input:focus,
    body.lg-app .eas-textarea:focus {
        border-color: #e11d48 !important;
        box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.14) !important;
    }
    .eas-textarea { resize: vertical; min-height: 76px; line-height: 1.6; }

    .text-fields { display: flex; flex-direction: column; gap: 12px; }

    .eas-empty-state {
        grid-column: 1 / -1;
        background: #ffffff;
        border: 1.5px dashed #cbd5e1;
        border-radius: 16px;
        padding: 36px 20px;
        text-align: center;
        color: #475569;
        font-size: 0.95rem;
        font-weight: 700;
        display: none;
    }

    .eas-save-toast {
        position: fixed;
        bottom: 22px;
        left: 22px;
        z-index: 10500;
        padding: 10px 16px;
        border-radius: 12px;
        font-size: 0.84rem;
        font-weight: 800;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.18);
        opacity: 0;
        transform: translateY(10px);
        pointer-events: none;
        transition: opacity 0.2s, transform 0.2s;
    }
    .eas-save-toast.show { opacity: 1; transform: translateY(0); }
    .eas-save-toast.ok { background: #dcfce7; color: #166534; border: 1.5px solid #22c55e; }
    .eas-save-toast.err { background: #fee2e2; color: #991b1b; border: 1.5px solid #ef4444; }

    jdp-container { z-index: 9999 !important; }

    @media (max-width: 767.98px) {
        .dashboard-header { flex-direction: column; align-items: stretch; padding: 16px; }
        .header-right { justify-content: space-between; }
        .live-clock-container { text-align: center; flex: 1; }
        .controls-section, .filters-left { flex-direction: column; align-items: stretch; }
        .filter-group, .custom-select, .eas-search-input, .export-excel-btn { width: 100%; max-width: none; }
        .time-chips-container { overflow-x: auto; flex-wrap: nowrap; -webkit-overflow-scrolling: touch; padding: 10px 12px; }
        .eas-card { padding: 15px; gap: 12px; }
        .card-header { flex-direction: column; align-items: stretch; gap: 10px; }
        .meta-info { align-items: flex-start; }
        .badges-wrapper { justify-content: flex-start; }
        .sessions-grid { gap: 8px; justify-content: center; }
        .action-row { flex-direction: column; }
        .interview-inputs { flex-direction: column; }
        .date-input { width: 58px; }
        .eas-join-btn { width: 100%; }
    }
</style>

<div class="eas-shell" id="easShell">
    <div class="dashboard-header">
        <div class="header-title">
            <h1><span>EAS</span> مدیریت داخلی</h1>
            <p>Educational Administration System</p>
        </div>

        <div class="header-right">
            <div class="eas-count-pill" id="easVisibleCount">نمایش <?= count($classes) ?> کلاس</div>
            <div class="live-clock-container" id="liveClockContainer">
                <div class="live-time" id="liveTime">00<span>:</span>00</div>
                <div class="live-date" id="liveDate">در حال بارگذاری...</div>
            </div>
        </div>
    </div>

    <div class="controls-section">
        <div class="filters-left">
            <div class="filter-group">
                <label for="easSearchInput">جستجو:</label>
                <input type="text" class="eas-search-input" id="easSearchInput" placeholder="نام کلاس، مدرس یا زبان‌آموز..." oninput="filterCards()">
            </div>
            <div class="filter-group">
                <label for="teacherFilter">مدرس:</label>
                <select class="custom-select" id="teacherFilter" onchange="filterCards()">
                    <option value="all">همه مدرسین</option>
                    <?php 
                        $teachers = array_unique(array_column($classes, 'teacher_name'));
                        foreach($teachers as $teacher) {
                            if($teacher) echo "<option value='".htmlspecialchars($teacher, ENT_QUOTES, 'UTF-8')."'>".htmlspecialchars($teacher, ENT_QUOTES, 'UTF-8')."</option>";
                        }
                    ?>
                </select>
            </div>
            <div class="filter-group">
                <label for="dayFilter">برگزاری:</label>
                <select class="custom-select" id="dayFilter" onchange="filterCards()">
                    <option value="all">همه روزها</option>
                    <option value="today" style="color: #be123c; font-weight: bold;">امروز</option>
                    <option value="even">زوج</option>
                    <option value="odd">فرد</option>
                    <option value="private">خصوصی</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="typeFilter">نوع کلاس:</label>
                <select class="custom-select" id="typeFilter" onchange="filterCards()">
                    <option value="all">همه</option>
                    <option value="online">آنلاین</option>
                    <option value="offline">حضوری</option>
                    <option value="private">خصوصی</option>
                </select>
            </div>
        </div>

        <div>
            <button class="export-excel-btn" onclick="exportToExcel()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="13"></line><line x1="8" y1="17" x2="16" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                خروجی اکسل جامع
            </button>
        </div>
    </div>

    <div class="time-chips-container" id="timeChipsContainer">
        <label>فیلتر ساعت:</label>
        <span class="time-chip active" data-time="all" onclick="selectTimeFilter(this)">همه کلاس‌ها</span>
        <span class="time-chip" data-time="10" onclick="selectTimeFilter(this)">۱۰ به بعد</span>
        <span class="time-chip" data-time="11" onclick="selectTimeFilter(this)">۱۱ به بعد</span>
        <span class="time-chip" data-time="12" onclick="selectTimeFilter(this)">۱۲ به بعد</span>
        <span class="time-chip" data-time="13" onclick="selectTimeFilter(this)">۱۳ به بعد</span>
        <span class="time-chip" data-time="14" onclick="selectTimeFilter(this)">۱۴ به بعد</span>
        <span class="time-chip" data-time="15" onclick="selectTimeFilter(this)">۱۵ به بعد</span>
        <span class="time-chip" data-time="16" onclick="selectTimeFilter(this)">۱۶ به بعد</span>
        <span class="time-chip" data-time="17" onclick="selectTimeFilter(this)">۱۷ به بعد</span>
        <span class="time-chip" data-time="18" onclick="selectTimeFilter(this)">۱۸ به بعد</span>
    </div>

    <div class="cards-container" id="cardsContainer">
        <div class="eas-empty-state" id="easEmptyState">هیچ کلاسی مطابق فیلترهای انتخابی یافت نشد.</div>

        <?php foreach ($classes as $class): 
            $badgeInfo = getDayBadgeClass($class['schedule_type']);
            $teacherName = $class['teacher_name'] ?? 'بدون مدرس';
            
            $classTypeKey = strtolower(trim((string)($class['class_type'] ?? '')));
            if ($classTypeKey === '' || in_array($classTypeKey, ['inperson', 'in-person', 'حضوری'], true)) {
                $classTypeKey = 'offline';
            }

            if ($classTypeKey === 'online') {
                $classTypeBadge = "<span class='type-badge type-online'>آنلاین</span>";
            } elseif ($classTypeKey === 'private') {
                $classTypeBadge = "<span class='type-badge type-private'>خصوصی</span>";
            } else {
                $classTypeBadge = "<span class='type-badge type-inperson'>حضوری</span>";
            }

            $dayKey = $badgeInfo[2];
            if ($classTypeKey === 'private' && $dayKey === 'all') {
                $dayKey = 'private';
            }
            
            $total_sessions = ($classTypeKey === 'private') ? 10 : 13;
            
            $students = $students_by_class[(int)$class['main_class_id']] ?? [];
            $online_link = trim((string)($class['class_online_link'] ?? ''));
            $teacher_id = (int)($class['teacher_id'] ?? 0);

            $session_dates = json_decode($class['session_dates_json'] ?? '{}', true);
            $makeup_sessions = json_decode($class['makeup_sessions_json'] ?? '{}', true);
            
            $display_time = !empty($class['schedule']) ? $class['schedule'] : (!empty($class['class_time']) ? $class['class_time'] : 'تنظیم نشده');
            $student_search_blob = implode(' ', array_map(function($s) {
                return ($s['full_name'] ?? '') . ' ' . ($s['username'] ?? '');
            }, $students));
            $search_blob = mb_strtolower(trim($class['class_title'] . ' ' . $teacherName . ' ' . $student_search_blob), 'UTF-8');
        ?>
        
        <div class="eas-card"
             data-teacher="<?= htmlspecialchars($teacherName, ENT_QUOTES, 'UTF-8') ?>"
             data-day="<?= htmlspecialchars($dayKey, ENT_QUOTES, 'UTF-8') ?>"
             data-time="<?= htmlspecialchars($display_time, ENT_QUOTES, 'UTF-8') ?>"
             data-classtype="<?= htmlspecialchars($classTypeKey, ENT_QUOTES, 'UTF-8') ?>"
             data-classid="<?= (int)$class['main_class_id'] ?>"
             data-search="<?= htmlspecialchars($search_blob, ENT_QUOTES, 'UTF-8') ?>">
            
            <div class="card-header">
                <div class="class-info">
                    <h2><?= htmlspecialchars($class['class_title']) ?><?= class_live_dot([
                        'schedule' => $class['schedule'] ?: ($class['class_time'] ?? ''),
                        'schedule_type' => $class['schedule_type'] ?? '',
                    ]) ?></h2>
                    <div class="teacher<?= $teacher_id > 0 ? ' eas-teacher' : '' ?>"
                         data-teachername="<?= htmlspecialchars($teacherName, ENT_QUOTES, 'UTF-8') ?>"
                         <?php if ($teacher_id > 0): ?>
                         data-teacher-id="<?= $teacher_id ?>"
                         title="دبل‌کلیک: Teachers Information"
                         ondblclick="openTeacherProfile(<?= $teacher_id ?>)"
                         <?php endif; ?>
                    >👨‍🏫 <?= htmlspecialchars($teacherName) ?></div>
                    <?php if ($online_link !== ''): ?>
                        <a class="eas-join-btn" href="<?= htmlspecialchars($online_link, ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener"><?= $classTypeKey === 'offline' ? 'ورود به رویداد آنلاین' : 'ورود به کلاس' ?></a>
                    <?php endif; ?>
                </div>
                <div class="meta-info">
                    <div class="badges-wrapper">
                        <?= $classTypeBadge ?>
                        <span class="day-badge <?= $badgeInfo[0] ?>" data-daytext="<?= $badgeInfo[1] ?>"><?= $badgeInfo[1] ?></span>
                    </div>
                    <span class="time-badge"><?= htmlspecialchars($display_time) ?></span>
                </div>
            </div>

            <div class="eas-section-box">
                <div class="section-title">👥 زبان‌آموزان فعلی (<?= count($students) ?>)</div>
                <div class="student-badges">
                    <?php if(empty($students)): ?>
                        <span class="student-badge student-badge-empty">ثبت نشده</span>
                    <?php else: ?>
                        <?php foreach($students as $student): ?>
                            <span class="student-badge eas-student"
                                  data-student-id="<?= (int)$student['id'] ?>"
                                  title="دبل‌کلیک: Students Information"
                                  ondblclick="openStudentProfile(<?= (int)$student['id'] ?>)">
                                <?= htmlspecialchars($student['full_name']) ?>
                                <?php if (trim((string)($student['username'] ?? '')) !== ''): ?>
                                    <span class="student-file"><?= htmlspecialchars($student['username']) ?></span>
                                <?php endif; ?>
                            </span>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="sessions-container">
                <div class="sessions-head">
                    <div class="section-title">📅 تشکیل جلسه (<?= $total_sessions ?> جلسه‌ای)</div>
                    <button type="button" class="zoom-btn" onclick="openZoomModal(<?= $class['main_class_id'] ?>)">🔍 بزرگنمایی</button>
                </div>
                
                <div id="placeholder-<?= $class['main_class_id'] ?>">
                    <div class="sessions-grid" id="grid-<?= $class['main_class_id'] ?>">
                        <?php 
                            for($i=1; $i<=$total_sessions; $i++) {
                                $session_status = $class['session_'.$i] ?? 0;
                                $status_class = ($session_status == 1) ? 'status-tick' : (($session_status == 2) ? 'status-cross' : '');
                                $content = ($session_status == 1) ? '✔' : (($session_status == 2) ? '✖' : $i);
                                $saved_date = $session_dates['date_'.$i] ?? '';
                                
                                echo "<div class='session-item'>
                                        <div class='session-box {$status_class}' data-num='{$i}' onclick='toggleSession(this)'>{$content}</div>
                                        <input type='text' data-jdp readonly class='date-input regular-date' placeholder='تاریخ' value='".htmlspecialchars($saved_date, ENT_QUOTES, 'UTF-8')."'>
                                      </div>";
                                      
                                if(isset($makeup_sessions['makeup_'.$i])) {
                                    echo "<div class='session-item makeup-item'>
                                            <div class='session-box makeup-box' title='جبرانی جلسه {$i}'>M</div>
                                            <input type='text' data-jdp readonly class='date-input makeup-date' data-num='{$i}' value='".htmlspecialchars($makeup_sessions['makeup_'.$i], ENT_QUOTES, 'UTF-8')."'>
                                          </div>";
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>

            <div class="actions-wrapper">
                <div class="action-row">
                    <div class="action-box">
                        <span>📊 نظرسنجی</span>
                        <input type="checkbox" class="custom-checkbox" onchange="saveEasData(<?= $class['main_class_id'] ?>, 'poll_status', this.checked ? 1 : 0)" <?= (isset($class['poll_status']) && $class['poll_status'] == 1) ? 'checked' : '' ?>>
                    </div>
                    <div class="action-box">
                        <span>👁 ارزیابی</span>
                        <input type="checkbox" class="custom-checkbox" onchange="saveEasData(<?= $class['main_class_id'] ?>, 'observation_status', this.checked ? 1 : 0)" <?= (isset($class['observation_status']) && $class['observation_status'] == 1) ? 'checked' : '' ?>>
                    </div>
                </div>
                <div class="action-row">
                    <div class="action-box">
                        <span>🧮 محاسبه حقوق</span>
                        <input type="checkbox" class="custom-checkbox" onchange="saveEasData(<?= $class['main_class_id'] ?>, 'salary_calculated', this.checked ? 1 : 0)" <?= (isset($class['salary_calculated']) && $class['salary_calculated'] == 1) ? 'checked' : '' ?>>
                    </div>
                    <div class="action-box">
                        <span>💳 پرداخت حقوق</span>
                        <input type="checkbox" class="custom-checkbox" onchange="saveEasData(<?= $class['main_class_id'] ?>, 'salary_paid', this.checked ? 1 : 0)" <?= (isset($class['salary_paid']) && $class['salary_paid'] == 1) ? 'checked' : '' ?>>
                    </div>
                </div>
            </div>

            <div class="interview-box">
                <div class="interview-head">
                    <div class="section-title">🎙 مصاحبه (Interview)</div>
                    <label class="interview-done-label">
                        انجام شد
                        <input type="checkbox" class="custom-checkbox" onchange="saveEasData(<?= $class['main_class_id'] ?>, 'interview_status', this.checked ? 1 : 0)" <?= (isset($class['interview_status']) && $class['interview_status'] == 1) ? 'checked' : '' ?>>
                    </label>
                </div>
                <div class="interview-inputs">
                    <input type="text" data-jdp readonly class="eas-input interview-date-input" style="flex: 1; text-align: left; direction: ltr;" placeholder="انتخاب تاریخ..." value="<?= htmlspecialchars($class['interview_date'] ?? '') ?>">
                    <input type="text" class="eas-input interview-name-input" style="flex: 2;" placeholder="نام مصاحبه‌کننده..." value="<?= htmlspecialchars($class['interviewer_name'] ?? '') ?>" onblur="saveEasData(<?= $class['main_class_id'] ?>, 'interviewer_name', this.value)">
                </div>
            </div>

            <div class="text-fields">
                <div class="eas-section-box">
                    <div class="section-title">⏳ در انتظار ثبت‌نام</div>
                    <textarea class="eas-textarea pending-text" onblur="saveEasData(<?= $class['main_class_id'] ?>, 'pending_students', this.value)" placeholder="اسامی افراد در صف انتظار..."><?= htmlspecialchars($class['pending_students'] ?? '') ?></textarea>
                </div>
                <div class="eas-section-box">
                    <div class="section-title">📝 یادداشت مدیر</div>
                    <textarea class="eas-textarea admin-note-text" onblur="saveEasData(<?= $class['main_class_id'] ?>, 'admin_note', this.value)" placeholder="نکات مربوط به کلاس..."><?= htmlspecialchars($class['admin_note'] ?? '') ?></textarea>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="eas-save-toast" id="easSaveToast"></div>

<div class="modal fade" id="zoomModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="background: #ffffff; border: 1.5px solid #cbd5e1; border-radius: 22px; box-shadow: 0 20px 50px rgba(15, 23, 42, 0.25);">
            <div class="modal-header" style="border-bottom: 1.5px solid #e2e8f0; padding: 16px 24px;">
                <h5 class="modal-title" style="color: #0f172a; font-weight: 900;">جزئیات جلسات</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="zoomModalBody" style="padding: 26px; background: #f8fafc; border-radius: 0 0 22px 22px;">
            </div>
        </div>
    </div>
</div>

<script src="../../assets/js/jalalidatepicker.min.js"></script>
<script>
    if (window.jalaliDatepicker) {
        jalaliDatepicker.startWatch({ minDate: "attr", maxDate: "attr", time: false });
    }

    document.addEventListener("jdp:change", function (e) {
        const inp = e.target;
        const classId = inp.closest('.eas-card') ? inp.closest('.eas-card').getAttribute('data-classid') : currentZoomClassId;
        if(!classId) return;

        if (inp.classList.contains('regular-date')) {
            saveDateUI(classId);
        } else if (inp.classList.contains('makeup-date')) {
            saveMakeupUI(classId);
        } else if (inp.classList.contains('interview-date-input')) {
            saveEasData(classId, 'interview_date', inp.value);
        }
    });

    function updateLiveClock() {
        const now = new Date();
        let hours = now.getHours().toString().padStart(2, '0');
        let minutes = now.getMinutes().toString().padStart(2, '0');
        const persianDate = now.toLocaleDateString('fa-IR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        document.getElementById('liveTime').innerHTML = `${hours}<span>:</span>${minutes}`;
        document.getElementById('liveDate').innerText = persianDate;
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();

    function openStudentProfile(id) {
        id = parseInt(id, 10);
        if (!id) return;
        window.location.href = 'students-info-view.php?id=' + id;
    }

    function openTeacherProfile(id) {
        id = parseInt(id, 10);
        if (!id) return;
        window.location.href = 'teachers-info-view.php?id=' + id;
    }

    let easToastTimer = null;
    function showEasToast(text, isError) {
        const el = document.getElementById('easSaveToast');
        if (!el) return;
        el.textContent = text;
        el.className = 'eas-save-toast show ' + (isError ? 'err' : 'ok');
        clearTimeout(easToastTimer);
        easToastTimer = setTimeout(() => { el.classList.remove('show'); }, 2200);
    }

    function saveEasData(classId, field, value) {
        let formData = new FormData();
        formData.append('class_id', classId);
        formData.append('field', field);
        formData.append('value', value);

        fetch('ajax_eas_update.php', { method: 'POST', body: formData })
            .then(r => {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.text();
            })
            .then(() => showEasToast('✓ تغییرات ذخیره شد', false))
            .catch(() => showEasToast('خطا در ذخیره‌سازی تغییرات', true));
    }

    let currentTimeFilterVal = 'all';

    function selectTimeFilter(el) {
        document.querySelectorAll('.time-chip').forEach(c => c.classList.remove('active'));
        el.classList.add('active');
        currentTimeFilterVal = el.getAttribute('data-time');
        filterCards();
    }

    function toEnglishDigits(str) {
        return String(str || '').replace(/[۰-۹]/g, function (d) {
            return '۰۱۲۳۴۵۶۷۸۹'.indexOf(d);
        }).replace(/[٠-٩]/g, function (d) {
            return '٠١٢٣٤٥٦٧٨٩'.indexOf(d);
        });
    }

    function parseTimeStr(timeStr) {
        if (!timeStr || timeStr === 'تنظیم نشده') return 9999;
        const s = toEnglishDigits(timeStr);
        const match = s.match(/(\d{1,2})(?::(\d{1,2}))?/);
        if (!match) return 9999;
        const h = parseInt(match[1], 10);
        const m = match[2] ? parseInt(match[2], 10) : 0;
        if (h > 23 || m > 59) return 9999;
        return h * 60 + m;
    }

    function tehranDayOfWeek() {
        const wd = new Intl.DateTimeFormat('en-US', { timeZone: 'Asia/Tehran', weekday: 'short' }).format(new Date());
        return { Sun: 0, Mon: 1, Tue: 2, Wed: 3, Thu: 4, Fri: 5, Sat: 6 }[wd] ?? new Date().getDay();
    }

    function filterCards() {
        const teacherFilter = document.getElementById('teacherFilter').value;
        const dayFilter = document.getElementById('dayFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        const searchQuery = (document.getElementById('easSearchInput')?.value || '').trim().toLowerCase();
        const cardsContainer = document.getElementById('cardsContainer');
        const cards = Array.from(document.querySelectorAll('.eas-card'));
        
        const dayOfWeek = tehranDayOfWeek();
        let todayType = 'none';
        if (dayOfWeek === 6 || dayOfWeek === 1 || dayOfWeek === 3) todayType = 'even';
        else if (dayOfWeek === 0 || dayOfWeek === 2 || dayOfWeek === 4) todayType = 'odd';

        let visibleCards = [];

        cards.forEach(card => {
            const cardTeacher = card.getAttribute('data-teacher');
            const cardDay = card.getAttribute('data-day');
            const cardTimeStr = card.getAttribute('data-time');
            const cardType = (card.getAttribute('data-classtype') || 'offline').toLowerCase();
            const cardSearch = (card.getAttribute('data-search') || '').toLowerCase();
            const cardMinutes = parseTimeStr(cardTimeStr);
            
            const matchTeacher = (teacherFilter === 'all' || cardTeacher === teacherFilter);
            const matchSearch = (searchQuery === '' || cardSearch.includes(searchQuery));
            
            let matchDay = false;
            if (dayFilter === 'all') {
                matchDay = true;
            } else if (dayFilter === 'today') {
                matchDay = (cardDay === todayType) || (cardType === 'private' && todayType !== 'none');
            } else if (dayFilter === 'private') {
                matchDay = (cardType === 'private' || cardDay === 'private');
            } else {
                matchDay = (cardDay === dayFilter);
            }

            let matchTime = true;
            if (currentTimeFilterVal !== 'all') {
                const filterMinutes = parseInt(currentTimeFilterVal, 10) * 60;
                matchTime = (cardMinutes >= filterMinutes && cardMinutes < 9999);
            }

            const normalizedType = (typeFilter === 'offline' && cardType === '') ? 'offline' : cardType;
            const matchType = (typeFilter === 'all' || normalizedType === typeFilter);

            if (matchTeacher && matchDay && matchTime && matchType && matchSearch) {
                card.style.display = 'flex';
                visibleCards.push({el: card, time: cardMinutes});
            } else {
                card.style.display = 'none';
            }
        });

        if(currentTimeFilterVal !== 'all') {
            visibleCards.sort((a, b) => a.time - b.time);
            visibleCards.forEach(item => cardsContainer.appendChild(item.el));
        }

        const countEl = document.getElementById('easVisibleCount');
        if (countEl) countEl.textContent = `نمایش ${visibleCards.length} کلاس`;
        const emptyEl = document.getElementById('easEmptyState');
        if (emptyEl) emptyEl.style.display = visibleCards.length === 0 ? 'block' : 'none';
    }

    function loadXlsx() {
        return new Promise(function (resolve, reject) {
            if (window.XLSX) {
                resolve(window.XLSX);
                return;
            }
            var s = document.createElement('script');
            s.src = '../../assets/js/xlsx.full.min.js';
            s.onload = function () { window.XLSX ? resolve(window.XLSX) : reject(new Error('xlsx')); };
            s.onerror = function () { reject(new Error('xlsx')); };
            document.head.appendChild(s);
        });
    }

    async function exportToExcel() {
        var btn = document.querySelector('.export-excel-btn');
        if (btn) {
            btn.disabled = true;
        }
        try {
            await loadXlsx();
        let exportData = [];
        
        document.querySelectorAll('.eas-card').forEach(card => {
            if (card.style.display === 'none') return;
            
            const className = card.querySelector('.class-info h2').childNodes[0].nodeValue.trim();
            const classType = card.querySelector('.type-badge').innerText.trim();
            const teacher = card.getAttribute('data-teacher');
            const time = card.getAttribute('data-time');
            const dayStr = card.querySelector('.day-badge').innerText.trim();
            
            let studentsArr = [];
            card.querySelectorAll('.student-badge').forEach(badge => {
                let text = badge.innerText.trim();
                if(text !== 'ثبت نشده') studentsArr.push(text);
            });
            const studentsStr = studentsArr.length > 0 ? studentsArr.join('، ') : 'بدون زبان‌آموز';

            const actionBoxes = card.querySelectorAll('.action-box');
            const pollStatus = (actionBoxes[0] && actionBoxes[0].querySelector('.custom-checkbox').checked) ? 'انجام شده' : 'خیر';
            const obsStatus = (actionBoxes[1] && actionBoxes[1].querySelector('.custom-checkbox').checked) ? 'انجام شده' : 'خیر';
            const calcStatus = (actionBoxes[2] && actionBoxes[2].querySelector('.custom-checkbox').checked) ? 'انجام شده' : 'خیر';
            const paidStatus = (actionBoxes[3] && actionBoxes[3].querySelector('.custom-checkbox').checked) ? 'انجام شده' : 'خیر';

            const intBox = card.querySelector('.interview-box');
            const intStatus = intBox.querySelector('.custom-checkbox').checked ? 'انجام شده' : 'خیر';
            const intDate = card.querySelector('.interview-date-input').value.trim();
            const intName = card.querySelector('.interview-name-input').value.trim();

            const pendingStudents = card.querySelector('.pending-text').value.trim();
            const adminNote = card.querySelector('.admin-note-text').value.trim();

            let rowData = {
                "نام کلاس": className,
                "مدرس": teacher,
                "روز برگزاری": dayStr,
                "ساعت": time,
                "نوع کلاس": classType,
                "محاسبه حقوق": calcStatus,
                "پرداخت حقوق": paidStatus,
                "وضعیت نظرسنجی": pollStatus,
                "وضعیت ارزیابی": obsStatus,
                "وضعیت مصاحبه": intStatus,
                "تاریخ مصاحبه": intDate,
                "مصاحبه‌کننده": intName,
                "زبان‌آموزان فعلی": studentsStr,
                "در انتظار ثبت‌نام": pendingStudents,
                "یادداشت مدیر": adminNote
            };

            for (let i = 1; i <= 13; i++) {
                let sessionBox = card.querySelector(`.session-box[data-num="${i}"]`);
                if (sessionBox) {
                    let status = "ثبت نشده";
                    if (sessionBox.classList.contains('status-tick')) status = "برگزار شد";
                    else if (sessionBox.classList.contains('status-cross')) status = "لغو شد";

                    let dateInput = sessionBox.closest('.session-item').querySelector('.regular-date');
                    let dateVal = dateInput ? dateInput.value.trim() : '';

                    let makeupStr = "";
                    let makeupInput = card.querySelector(`.makeup-date[data-num="${i}"]`);
                    if (makeupInput) {
                        makeupStr = makeupInput.value.trim();
                    }

                    let finalStr = status;
                    if (dateVal) finalStr += ` (${dateVal})`;
                    if (makeupStr) finalStr += ` [جبرانی: ${makeupStr}]`;

                    rowData[`جلسه ${i}`] = finalStr;
                }
            }

            exportData.push(rowData);
        });

        if(exportData.length === 0) {
            alert("هیچ کلاسی یافت نشد!");
            return;
        }

        const worksheet = XLSX.utils.json_to_sheet(exportData);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "EAS_Classes");
        
        const date = new Date().toLocaleDateString('fa-IR').replace(/\//g, '-');
        XLSX.writeFile(workbook, `Ghazal_EAS_Classes_${date}.xlsx`);
        } catch (err) {
            alert('خروجی اکسل لود نشد. صفحه را تازه کنید و دوباره بزنید.');
        } finally {
            if (btn) btn.disabled = false;
        }
    }

    function saveDateUI(classId) {
        const grid = document.getElementById('grid-' + classId);
        const inputs = grid.querySelectorAll('.regular-date');
        let datesObj = {};
        inputs.forEach((inp, index) => {
            if(inp.value.trim() !== '') datesObj['date_' + (index + 1)] = inp.value;
        });
        saveEasData(classId, 'session_dates_json', JSON.stringify(datesObj));
    }

    function saveMakeupUI(classId) {
        const grid = document.getElementById('grid-' + classId);
        const inputs = grid.querySelectorAll('.makeup-date');
        let obj = {};
        inputs.forEach(inp => {
            if(inp.value.trim() !== '') obj['makeup_' + inp.getAttribute('data-num')] = inp.value;
        });
        saveEasData(classId, 'makeup_sessions_json', JSON.stringify(obj));
    }

    function toggleSession(el) {
        const num = el.getAttribute('data-num');
        const card = el.closest('.eas-card') || document.getElementById('placeholder-' + currentZoomClassId).closest('.eas-card');
        const classId = card.getAttribute('data-classid');
        let newValue = 0;

        if (el.classList.contains('status-tick')) {
            el.classList.remove('status-tick');
            el.classList.add('status-cross');
            el.innerHTML = '✖';
            newValue = 2;
            
            let makeupBox = `
                <div class="session-item makeup-item">
                    <div class="session-box makeup-box" title="جبرانی جلسه ${num}">M</div>
                    <input type="text" data-jdp readonly class="date-input makeup-date" data-num="${num}" placeholder="تاریخ">
                </div>`;
            el.closest('.session-item').insertAdjacentHTML('afterend', makeupBox);
            if (window.jalaliDatepicker) jalaliDatepicker.startWatch(); 
            
        } 
        else if (el.classList.contains('status-cross')) {
            el.classList.remove('status-cross');
            el.innerHTML = num;
            newValue = 0;
            let nextEl = el.closest('.session-item').nextElementSibling;
            if(nextEl && nextEl.classList.contains('makeup-item')) {
                nextEl.remove();
                saveMakeupUI(classId); 
            }
        } 
        else {
            el.classList.add('status-tick');
            el.innerHTML = '✔';
            newValue = 1;
        }

        saveEasData(classId, 'session_' + num, newValue);
    }

    let currentZoomClassId = null;
    function openZoomModal(classId) {
        currentZoomClassId = classId;
        const grid = document.getElementById('grid-' + classId);
        const modalBody = document.getElementById('zoomModalBody');
        
        modalBody.appendChild(grid); 
        grid.classList.add('zoomed-mode'); 
        
        new bootstrap.Modal(document.getElementById('zoomModal')).show();
    }

    document.getElementById('zoomModal').addEventListener('hidden.bs.modal', function () {
        if (currentZoomClassId) {
            const grid = document.getElementById('grid-' + currentZoomClassId);
            grid.classList.remove('zoomed-mode');
            document.getElementById('placeholder-' + currentZoomClassId).appendChild(grid); 
            currentZoomClassId = null;
        }
    });

</script>

<?php require_once 'layout-end.php'; ?>

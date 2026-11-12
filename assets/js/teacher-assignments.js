(function () {
    const root = document.querySelector('[data-asg-teacher]');
    if (!root) {
        return;
    }
    let boot = {};
    try {
        boot = JSON.parse(root.getAttribute('data-boot') || '{}');
    } catch (err) {
        boot = {};
    }
    const ajax = boot.ajax || 'ajax-assignments.php';
    const fileBase = boot.file || 'assignment-file.php';
    const classId = parseInt(boot.class_id || '0', 10);
    const sessionMax = parseInt(boot.session_max || '14', 10);
    let items = boot.assignments || [];
    let currentId = 0;
    let filterSession = 0;

    const form = document.getElementById('asgForm');
    const alertBox = document.getElementById('asgAlert');
    const progress = document.getElementById('asgProgress');
    const progressText = document.getElementById('asgProgressText');
    const saveBtn = document.getElementById('asgSaveBtn');
    const resetBtn = document.getElementById('asgResetBtn');
    const removeFileBtn = document.getElementById('asgRemoveFileBtn');
    const listEl = document.getElementById('asgList');
    const sessionsEl = document.getElementById('asgSessions');
    const detailEl = document.getElementById('asgDetail');
    const idEl = document.getElementById('asgId');
    const sessionEl = document.getElementById('asgSession');
    const titleEl = document.getElementById('asgTitle');
    const dueEl = document.getElementById('asgDue');
    const bodyEl = document.getElementById('asgBody');
    const fileEl = document.getElementById('asgFile');
    const fileHint = document.getElementById('asgFileHint');

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function toast(msg) {
        let el = document.querySelector('.qb-toast');
        if (!el) {
            el = document.createElement('div');
            el.className = 'qb-toast';
            document.body.appendChild(el);
        }
        el.textContent = msg;
        el.classList.add('show');
        clearTimeout(el._t);
        el._t = setTimeout(function () {
            el.classList.remove('show');
        }, 2800);
    }

    function showAlert(msg, ok) {
        if (!alertBox) {
            return;
        }
        alertBox.hidden = !msg;
        alertBox.className = 'qb-alert ' + (ok ? 'ok' : 'err');
        alertBox.textContent = msg || '';
    }

    function busy(on, text) {
        if (progress) {
            progress.hidden = !on;
        }
        if (progressText && text) {
            progressText.textContent = text;
        }
        if (saveBtn) {
            saveBtn.disabled = !!on;
        }
    }

    function post(payload, extraFile) {
        const body = new FormData();
        Object.keys(payload).forEach(function (k) {
            if (payload[k] !== undefined && payload[k] !== null) {
                body.append(k, payload[k]);
            }
        });
        if (classId) {
            body.append('class_id', String(classId));
        }
        if (extraFile) {
            body.append('file', extraFile);
        }
        return fetch(ajax, { method: 'POST', body: body, credentials: 'same-origin' })
            .then(function (r) {
                const ct = r.headers.get('content-type') || '';
                if (ct.indexOf('json') === -1) {
                    throw new Error('نشست منقضی شده. صفحه را رفرش کنید.');
                }
                return r.json().then(function (data) {
                    data._http = r.status;
                    return data;
                });
            })
            .then(function (data) {
                if (!data.success) {
                    throw new Error(data.message || 'خطا');
                }
                return data;
            });
    }

    function counts() {
        const map = {};
        for (let i = 1; i <= sessionMax; i++) {
            map[i] = 0;
        }
        items.forEach(function (it) {
            const n = parseInt(it.session_no, 10);
            if (map[n] !== undefined) {
                map[n] += 1;
            }
        });
        return map;
    }

    function renderSessions() {
        if (!sessionsEl) {
            return;
        }
        const map = counts();
        let html = '<button type="button" class="asg-chip' + (filterSession === 0 ? ' is-on' : '') + '" data-s="0">همه جلسات</button>';
        for (let i = 1; i <= sessionMax; i++) {
            const n = map[i] || 0;
            html += '<button type="button" class="asg-chip' + (filterSession === i ? ' is-on' : '') + (n ? ' has-item' : '') + '" data-s="' + i + '">S.' + i + (n ? ' · ' + n : '') + '</button>';
        }
        sessionsEl.innerHTML = html;
        sessionsEl.querySelectorAll('.asg-chip').forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterSession = parseInt(btn.getAttribute('data-s') || '0', 10);
                if (filterSession > 0 && sessionEl) {
                    sessionEl.value = String(filterSession);
                }
                renderSessions();
                renderList();
            });
        });
    }

    function upsert(row) {
        const id = parseInt(row.id, 10);
        let found = false;
        items = items.map(function (it) {
            if (parseInt(it.id, 10) === id) {
                found = true;
                return row;
            }
            return it;
        });
        if (!found) {
            items.unshift(row);
        }
        items.sort(function (a, b) {
            const ds = parseInt(a.session_no, 10) - parseInt(b.session_no, 10);
            return ds !== 0 ? ds : parseInt(b.id, 10) - parseInt(a.id, 10);
        });
    }

    function fillForm(row) {
        currentId = row ? parseInt(row.id, 10) : 0;
        if (idEl) {
            idEl.value = String(currentId);
        }
        if (sessionEl) {
            sessionEl.value = String(row ? row.session_no : (filterSession || 1));
        }
        if (titleEl) {
            titleEl.value = row ? (row.title || '') : '';
        }
        if (dueEl) {
            dueEl.value = row && row.due_at ? row.due_at : '';
        }
        if (bodyEl) {
            bodyEl.value = row ? (row.body || '') : '';
        }
        if (fileEl) {
            fileEl.value = '';
        }
        if (resetBtn) {
            resetBtn.hidden = !row;
        }
        if (removeFileBtn) {
            removeFileBtn.hidden = !(row && row.has_teacher_file);
        }
        if (saveBtn) {
            saveBtn.textContent = row ? 'ذخیره تغییرات' : 'انتشار برای کلاس';
        }
        if (fileHint) {
            if (row && row.has_teacher_file) {
                fileHint.textContent = 'پیوست فعلی: ' + row.teacher_file_name + ' (' + row.teacher_file_size_label + '). فایل جدید جایگزین می‌شود. سقف ' + (boot.max_label || '') + '.';
            } else {
                fileHint.textContent = 'پیوست اختیاری است. PDF، تصویر، ویس، Word یا ZIP — حداکثر ' + (boot.max_label || '۲۰ مگابایت') + '.';
            }
        }
    }

    function renderList() {
        if (!listEl) {
            return;
        }
        const shown = items.filter(function (it) {
            return !filterSession || parseInt(it.session_no, 10) === filterSession;
        });
        if (!shown.length) {
            const s = filterSession ? ('جلسه ' + filterSession) : 'این کلاس';
            listEl.innerHTML = '<div class="qb-empty"><h5>برای ' + esc(s) + ' تکلیفی نیست</h5><p>عنوان را بنویسید و انتشار را بزنید.</p></div>';
            return;
        }
        listEl.innerHTML = shown.map(function (it) {
            const due = it.due_label ? ('مهلت ' + it.due_label) : 'بدون مهلت';
            const file = it.has_teacher_file ? ('پیوست: ' + it.teacher_file_name) : 'بدون پیوست';
            const sub = (it.submission_count || 0) + ' / ' + (it.student_count || 0) + ' تحویل';
            const graded = (it.graded_count || 0) + ' بررسی‌شده';
            return '<article class="qb-exam-item asg-item" data-id="' + it.id + '">'
                + '<div><h3>' + esc(it.title) + '</h3>'
                + '<div class="qb-card-meta"><span class="qb-pill qb-pill-ready">S.' + esc(it.session_no) + '</span>'
                + '<span>' + esc(due) + '</span><span>' + esc(file) + '</span><span>' + esc(sub) + '</span><span>' + esc(graded) + '</span></div>'
                + (it.body ? '<p class="qb-hint">' + esc(it.body).slice(0, 180) + '</p>' : '')
                + '</div><div class="asg-item-actions">'
                + '<button type="button" class="qb-btn" data-act="subs">تحویل‌ها</button>'
                + '<button type="button" class="qb-btn" data-act="edit">ویرایش</button>'
                + (it.has_teacher_file ? '<a class="qb-btn" href="' + fileBase + '?kind=teacher&id=' + it.id + '">دانلود پیوست</a>' : '')
                + '<button type="button" class="qb-btn qb-btn-danger" data-act="del">حذف</button>'
                + '</div></article>';
        }).join('');
    }

    function renderSubFilesCell(r) {
        if (!r.submitted) {
            return '—';
        }
        const files = Array.isArray(r.files) && r.files.length
            ? r.files
            : [{ id: 0, kind: 'doc', can_inline: false, name: r.file_name || 'file', size_label: r.file_size_label || '' }];

        return '<div class="asg-sub-files">' + files.map(function (f) {
            const fid = parseInt(f.id || '0', 10);
            const dlUrl = fid > 0
                ? (fileBase + '?kind=submission_file&id=' + fid)
                : (fileBase + '?kind=submission&id=' + r.submission_id);
            const inlineUrl = dlUrl + '&inline=1';

            if (f.kind === 'image' && f.can_inline) {
                return '<div class="asg-sub-file-item asg-sub-img-item">'
                    + '<a href="' + inlineUrl + '" target="_blank" rel="noopener" class="asg-sub-thumb-link" title="مشاهده تصویر">'
                    + '<img src="' + inlineUrl + '" alt="' + esc(f.name) + '" loading="lazy" class="asg-sub-thumb">'
                    + '</a>'
                    + '<div class="asg-sub-file-info">'
                    + '<a href="' + inlineUrl + '" target="_blank" rel="noopener">' + esc(f.name) + '</a>'
                    + ' <span>' + esc(f.size_label || '') + '</span>'
                    + ' · <a href="' + dlUrl + '">دانلود</a>'
                    + '</div></div>';
            }
            if (f.kind === 'audio' && f.can_inline) {
                return '<div class="asg-sub-file-item asg-sub-aud-item">'
                    + '<audio class="asg-audio-player asg-audio-compact" controls preload="none" src="' + inlineUrl + '"></audio>'
                    + '<div class="asg-sub-file-info">'
                    + '<a href="' + dlUrl + '">🎙️ دانلود ' + esc(f.name) + '</a>'
                    + ' <span>' + esc(f.size_label || '') + '</span>'
                    + '</div></div>';
            }
            return '<div class="asg-sub-file-item">'
                + '<a href="' + dlUrl + '">📄 ' + esc(f.name) + '</a> <span>' + esc(f.size_label || '') + '</span>'
                + '</div>';
        }).join('') + '</div>';
    }

    function paintSubs(data) {
        const asg = data.assignment || {};
        const rows = data.submissions || [];
        currentId = parseInt(asg.id || currentId || '0', 10);
        let html = '<h2>' + esc(asg.title || 'تحویل‌ها') + '</h2>';
        html += '<p class="qb-hint">S.' + esc(asg.session_no) + ' · '
            + (data.submitted || 0) + ' از ' + (data.total || 0) + ' نفر فایل فرستاده‌اند'
            + ' · ' + (data.graded || asg.graded_count || 0) + ' بررسی‌شده (فقط ۳ حالت کامل، ناقص و عدم تحویل).</p>';
        if (asg.body) {
            html += '<div class="asg-body">' + esc(asg.body) + '</div>';
        }
        if (asg.has_teacher_file && asg.teacher_file_kind === 'audio' && asg.teacher_file_inline) {
            html += '<div class="asg-teacher-att"><audio class="asg-audio-player" controls preload="none" src="'
                + fileBase + '?kind=teacher&id=' + asg.id + '&inline=1"></audio></div>';
        }
        if (!rows.length) {
            html += '<p>هنوز دانش‌آموزی در این کلاس نیست.</p>';
        } else {
            html += '<table><thead><tr><th>دانش‌آموز</th><th>وضعیت تحویل</th><th>فایل‌ها / ویس</th><th>زمان</th><th>وضعیت تکلیف</th></tr></thead><tbody>';
            rows.forEach(function (r) {
                const pill = r.status_key === 'submitted' ? 'ready' : (r.status_key === 'late' ? 'draft' : (r.status_key === 'overdue' ? 'failed' : 'pending'));
                const file = renderSubFilesCell(r);
                const curStatus = r.review_status || '';
                const stKey = r.student_id;
                const grade = '<div class="asg-grade asg-review-wrap">'
                    + '<div class="asg-review-btns">'
                    + '<button type="button" class="asg-review-btn is-complete' + (curStatus === 'complete' ? ' active' : '') + '" data-act="review" data-status="complete" data-student="' + stKey + '" data-sid="' + (r.submission_id || 0) + '">کامل</button>'
                    + '<button type="button" class="asg-review-btn is-incomplete' + (curStatus === 'incomplete' ? ' active' : '') + '" data-act="review" data-status="incomplete" data-student="' + stKey + '" data-sid="' + (r.submission_id || 0) + '">ناقص</button>'
                    + '<button type="button" class="asg-review-btn is-not-submitted' + (curStatus === 'not_submitted' ? ' active' : '') + '" data-act="review" data-status="not_submitted" data-student="' + stKey + '" data-sid="' + (r.submission_id || 0) + '">عدم تحویل</button>'
                    + '</div>'
                    + '<input type="text" maxlength="400" class="asg-feedback-input" data-student="' + stKey + '" value="' + esc(r.feedback || '') + '" placeholder="توضیح (اختیاری)">'
                    + '</div>';
                html += '<tr data-sid="' + (r.submission_id || 0) + '" data-student="' + stKey + '"><td><span class="asg-name">' + esc(r.full_name) + '</span><div class="qb-hint">' + esc(r.username) + '</div></td>'
                    + '<td><span class="qb-pill qb-pill-' + pill + '">' + esc(r.status_label) + '</span></td>'
                    + '<td>' + file + '</td><td>' + esc(r.submitted_label || '—') + '</td><td>' + grade + '</td></tr>';
            });
            html += '</tbody></table>';
        }
        if (detailEl) {
            detailEl.hidden = false;
            detailEl.innerHTML = html;
        }
        if (asg && asg.id) {
            upsert(asg);
            renderSessions();
            renderList();
        }
    }

    function loadSubs(id) {
        busy(true, 'در حال خواندن تحویل‌ها…');
        post({ action: 'submissions', id: String(id) })
            .then(function (data) {
                paintSubs(data);
                if (detailEl) {
                    detailEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(function (err) {
                showAlert(err.message, false);
                toast(err.message);
            })
            .then(function () {
                busy(false);
            });
    }

    if (fileHint) {
        fileHint.textContent = 'پیوست اختیاری است. PDF، تصویر، ویس، Word یا ZIP — حداکثر ' + (boot.max_label || '۲۰ مگابایت') + '.';
    }

    renderSessions();
    renderList();

    if (detailEl) {
        detailEl.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-act="review"]');
            if (!btn) {
                return;
            }
            const sid = parseInt(btn.getAttribute('data-sid') || '0', 10);
            const studentId = parseInt(btn.getAttribute('data-student') || '0', 10);
            if (!studentId || !currentId) {
                return;
            }
            const isAlreadyActive = btn.classList.contains('active');
            const nextStatus = isAlreadyActive ? '' : (btn.getAttribute('data-status') || '');
            const noteEl = detailEl.querySelector('.asg-feedback-input[data-student="' + studentId + '"]');
            busy(true, 'در حال ثبت وضعیت…');
            post({
                action: 'grade',
                assignment_id: String(currentId),
                student_id: String(studentId),
                submission_id: String(sid),
                review_status: nextStatus,
                feedback: noteEl ? noteEl.value : ''
            })
                .then(function (data) {
                    paintSubs(data);
                    toast(data.message || 'وضعیت تکلیف ثبت شد.');
                    showAlert(data.message, true);
                })
                .catch(function (err) {
                    showAlert(err.message, false);
                    toast(err.message);
                })
                .then(function () {
                    busy(false);
                });
        });
    }

    if (listEl) {
        listEl.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-act]');
            if (!btn) {
                return;
            }
            const card = btn.closest('[data-id]');
            const id = card ? parseInt(card.getAttribute('data-id'), 10) : 0;
            const row = items.filter(function (it) { return parseInt(it.id, 10) === id; })[0];
            const act = btn.getAttribute('data-act');
            if (act === 'edit' && row) {
                fillForm(row);
                if (form) {
                    form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
            if (act === 'subs') {
                loadSubs(id);
            }
            if (act === 'del') {
                if (!window.confirm('این تکلیف و فایل‌های تحویل‌شده حذف شود؟')) {
                    return;
                }
                busy(true, 'در حال حذف…');
                post({ action: 'delete', id: String(id) })
                    .then(function (data) {
                        items = items.filter(function (it) { return parseInt(it.id, 10) !== id; });
                        if (currentId === id) {
                            fillForm(null);
                        }
                        if (detailEl) {
                            detailEl.hidden = true;
                        }
                        renderSessions();
                        renderList();
                        toast(data.message || 'حذف شد.');
                        showAlert(data.message, true);
                    })
                    .catch(function (err) {
                        showAlert(err.message, false);
                        toast(err.message);
                    })
                    .then(function () {
                        busy(false);
                    });
            }
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', function () {
            fillForm(null);
            showAlert('', true);
        });
    }

    if (removeFileBtn) {
        removeFileBtn.addEventListener('click', function () {
            if (!currentId) {
                return;
            }
            busy(true, 'حذف پیوست…');
            post({ action: 'remove_file', id: String(currentId) })
                .then(function (data) {
                    upsert(data.assignment);
                    fillForm(data.assignment);
                    renderSessions();
                    renderList();
                    toast(data.message);
                    showAlert(data.message, true);
                })
                .catch(function (err) {
                    showAlert(err.message, false);
                    toast(err.message);
                })
                .then(function () {
                    busy(false);
                });
        });
    }

    if (form) {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const title = (titleEl && titleEl.value || '').trim();
            if (title.length < 2) {
                showAlert('عنوان تکلیف را بنویسید.', false);
                return;
            }
            const file = fileEl && fileEl.files && fileEl.files[0] ? fileEl.files[0] : null;
            if (file && boot.max_bytes && file.size > boot.max_bytes) {
                showAlert('حجم فایل حداکثر ' + boot.max_label + ' است.', false);
                return;
            }
            busy(true, 'در حال انتشار…');
            post({
                action: 'save',
                id: idEl ? idEl.value : '0',
                session_no: sessionEl ? sessionEl.value : '1',
                title: title,
                body: bodyEl ? bodyEl.value : '',
                due_at: dueEl ? dueEl.value : ''
            }, file)
                .then(function (data) {
                    upsert(data.assignment);
                    filterSession = parseInt(data.assignment.session_no, 10) || filterSession;
                    fillForm(null);
                    if (sessionEl) {
                        sessionEl.value = String(filterSession || 1);
                    }
                    renderSessions();
                    renderList();
                    toast(data.message);
                    showAlert(data.message, true);
                })
                .catch(function (err) {
                    showAlert(err.message, false);
                    toast(err.message);
                })
                .then(function () {
                    busy(false);
                });
        });
    }
})();

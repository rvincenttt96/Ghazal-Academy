(function () {
    const filterInput = document.getElementById('asgClassFilter');
    const cardsWrap = document.getElementById('asgClassCards');
    const emptyEl = document.getElementById('asgClassEmpty');
    if (filterInput && cardsWrap) {
        filterInput.addEventListener('input', function () {
            const q = (filterInput.value || '').trim().toLowerCase();
            let shown = 0;
            cardsWrap.querySelectorAll('[data-hay]').forEach(function (card) {
                const hay = (card.getAttribute('data-hay') || '');
                const on = !q || hay.indexOf(q) !== -1;
                card.hidden = !on;
                if (on) {
                    shown += 1;
                }
            });
            if (emptyEl) {
                emptyEl.hidden = shown > 0;
            }
        });
    }

    const root = document.querySelector('[data-asg-supervisor]');
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
    const sessionMax = parseInt(boot.session_max || '14', 10);
    let items = boot.assignments || [];
    let filterSession = 0;

    const listEl = document.getElementById('asgList');
    const sessionsEl = document.getElementById('asgSessions');
    const detailEl = document.getElementById('asgDetail');

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

    function post(payload) {
        const body = new FormData();
        Object.keys(payload).forEach(function (k) {
            if (payload[k] !== undefined && payload[k] !== null) {
                body.append(k, payload[k]);
            }
        });
        if (boot.class_id) {
            body.append('class_id', String(boot.class_id));
        }
        return fetch(ajax, { method: 'POST', body: body, credentials: 'same-origin' })
            .then(function (r) {
                const ct = r.headers.get('content-type') || '';
                if (ct.indexOf('json') === -1) {
                    throw new Error('نشست منقضی شده. صفحه را رفرش کنید.');
                }
                return r.json();
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
                renderSessions();
                renderList();
            });
        });
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
            listEl.innerHTML = '<div class="qb-empty"><h5>برای ' + esc(s) + ' تکلیفی نیست</h5><p>وقتی معلم Assignment بگذارد، عنوان، فایل پیوست، پاسخ‌ها و وضعیت تکلیف اینجا می‌آید.</p></div>';
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
                + '<button type="button" class="qb-btn" data-act="subs">تحویل و وضعیت</button>'
                + (it.has_teacher_file ? '<a class="qb-btn" href="' + fileBase + '?kind=teacher&id=' + it.id + '">دانلود پیوست معلم</a>' : '')
                + '</div></article>';
        }).join('');
    }

    function renderSubFilesCell(r) {
        if (!r.submitted) {
            return 'تحویل نشده';
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

    function loadSubs(id) {
        post({ action: 'submissions', id: String(id) })
            .then(function (data) {
                const asg = data.assignment || {};
                const rows = data.submissions || [];
                let html = '<h2>' + esc(asg.title || 'تحویل‌ها') + '</h2>';
                html += '<p class="qb-hint">معلم: ' + esc(asg.teacher_name || '—')
                    + ' · S.' + esc(asg.session_no)
                    + ' · ' + (data.submitted || 0) + ' از ' + (data.total || 0) + ' نفر فایل فرستاده‌اند'
                    + ' · ' + (data.graded || 0) + ' بررسی‌شده.</p>';
                if (asg.due_label) {
                    html += '<p class="qb-hint">مهلت ' + esc(asg.due_label) + '</p>';
                }
                if (asg.body) {
                    html += '<div class="asg-body">' + esc(asg.body) + '</div>';
                }
                if (asg.has_teacher_file) {
                    html += '<div class="asg-teacher-att">';
                    if (asg.teacher_file_kind === 'audio' && asg.teacher_file_inline) {
                        html += '<audio class="asg-audio-player" controls preload="none" src="'
                            + fileBase + '?kind=teacher&id=' + asg.id + '&inline=1"></audio>';
                    }
                    html += '<p><a class="qb-btn" href="' + fileBase + '?kind=teacher&id=' + asg.id + '">پیوست معلم · '
                        + esc(asg.teacher_file_name) + '</a></p></div>';
                }
                if (!rows.length) {
                    html += '<p>هنوز دانش‌آموزی در این کلاس نیست.</p>';
                } else {
                    html += '<table><thead><tr><th>دانش‌آموز</th><th>وضعیت تحویل</th><th>فایل‌ها / ویس پاسخ</th><th>زمان</th><th>وضعیت تکلیف</th><th>توضیح معلم</th></tr></thead><tbody>';
                    rows.forEach(function (r) {
                        const pill = r.status_key === 'submitted' ? 'ready' : (r.status_key === 'late' ? 'draft' : (r.status_key === 'overdue' ? 'failed' : 'pending'));
                        const file = renderSubFilesCell(r);
                        const revPill = r.review_status === 'complete' ? 'ready' : (r.review_status === 'incomplete' ? 'draft' : (r.review_status === 'not_submitted' ? 'failed' : 'pending'));
                        const score = r.graded
                            ? '<span class="qb-pill qb-pill-' + revPill + '">' + esc(r.review_label || r.score_label) + '</span>'
                            : '<span class="qb-pill qb-pill-pending">بررسی نشده</span>';
                        html += '<tr><td><span class="asg-name">' + esc(r.full_name) + '</span><div class="qb-hint">' + esc(r.username) + '</div></td>'
                            + '<td><span class="qb-pill qb-pill-' + pill + '">' + esc(r.status_label) + '</span></td>'
                            + '<td>' + file + '</td><td>' + esc(r.submitted_label || '—') + '</td>'
                            + '<td>' + score + '</td><td>' + esc(r.feedback || '—') + '</td></tr>';
                    });
                    html += '</tbody></table>';
                }
                if (detailEl) {
                    detailEl.hidden = false;
                    detailEl.innerHTML = html;
                    detailEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(function (err) {
                toast(err.message);
            });
    }

    renderSessions();
    renderList();

    if (listEl) {
        listEl.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-act="subs"]');
            if (!btn) {
                return;
            }
            const card = btn.closest('[data-id]');
            const id = card ? parseInt(card.getAttribute('data-id'), 10) : 0;
            if (id) {
                loadSubs(id);
            }
        });
    }
})();

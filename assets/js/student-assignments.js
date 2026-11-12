(function () {
    const root = document.querySelector('[data-asg-student]');
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
    const id = parseInt(boot.id || '0', 10);
    const maxImages = parseInt(boot.max_images || '5', 10);
    const maxAudio = parseInt(boot.max_audio || '5', 10);
    const maxFiles = parseInt(boot.max_files || '8', 10);
    const imageExts = boot.image_exts || ['jpg', 'jpeg', 'png', 'webp', 'gif', 'heic', 'heif'];
    const audioExts = boot.audio_exts || ['mp3', 'm4a', 'wav', 'ogg', 'oga', 'opus', 'webm', 'aac', 'amr', '3gp', 'flac'];
    let state = boot.assignment || null;

    const form = document.getElementById('asgUploadForm');
    const fileEl = document.getElementById('asgFile');
    const selectedHint = document.getElementById('asgSelectedHint');
    const btn = document.getElementById('asgUploadBtn');
    const alertBox = document.getElementById('asgAlert');
    const progress = document.getElementById('asgProgress');
    const progressText = document.getElementById('asgProgressText');
    const current = document.getElementById('asgCurrent');
    const imgQuotaEl = document.getElementById('asgImgQuota');
    const audQuotaEl = document.getElementById('asgAudioQuota');
    const statusPillEl = document.getElementById('asgStatusPill');
    const gradeBoxEl = document.getElementById('asgGradeBox');

    const recBox = document.getElementById('asgRecorder');
    const recStartBtn = document.getElementById('asgRecStartBtn');
    const recStopBtn = document.getElementById('asgRecStopBtn');
    const recCancelBtn = document.getElementById('asgRecCancelBtn');
    const recLive = document.getElementById('asgRecLive');
    const recTimer = document.getElementById('asgRecTimer');
    const recPreview = document.getElementById('asgRecPreview');
    const recAudio = document.getElementById('asgRecAudio');
    const recSendBtn = document.getElementById('asgRecSendBtn');
    const recDiscardBtn = document.getElementById('asgRecDiscardBtn');

    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function extOf(name) {
        const s = String(name || '').toLowerCase();
        const idx = s.lastIndexOf('.');
        if (idx === -1) {
            return '';
        }
        return s.slice(idx + 1).replace(/[^a-z0-9]/g, '');
    }

    function kindOfFile(file) {
        const ext = extOf(file && file.name);
        if (imageExts.indexOf(ext) !== -1) {
            return 'image';
        }
        if (audioExts.indexOf(ext) !== -1) {
            return 'audio';
        }
        const mime = String((file && file.type) || '').toLowerCase();
        if (mime.indexOf('image/') === 0) {
            return 'image';
        }
        if (mime.indexOf('audio/') === 0) {
            return 'audio';
        }
        return 'doc';
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
        if (progressText) {
            progressText.textContent = on ? (text || 'در حال آپلود…') : '';
        }
        if (btn) {
            btn.disabled = !!on;
        }
        if (recStartBtn) {
            recStartBtn.disabled = !!on;
        }
        if (recSendBtn) {
            recSendBtn.disabled = !!on;
        }
        if (current) {
            current.querySelectorAll('button[data-del-fid]').forEach(function (b) {
                b.disabled = !!on;
            });
        }
    }

    function statusPillClass(key) {
        if (key === 'submitted') {
            return 'ready';
        }
        if (key === 'late') {
            return 'draft';
        }
        if (key === 'overdue') {
            return 'failed';
        }
        return 'pending';
    }

    function renderState() {
        const asg = state || {};
        const files = Array.isArray(asg.files) ? asg.files : [];
        const imgCount = parseInt(asg.image_count || '0', 10);
        const audCount = parseInt(asg.audio_count || '0', 10);

        if (imgQuotaEl) {
            imgQuotaEl.textContent = 'عکس‌ها: ' + imgCount + ' از ' + maxImages;
            imgQuotaEl.className = 'qb-pill qb-pill-' + (imgCount >= maxImages ? 'draft' : 'ready');
        }
        if (audQuotaEl) {
            audQuotaEl.textContent = 'ویس: ' + audCount;
            audQuotaEl.className = 'qb-pill qb-pill-' + (audCount > 0 ? 'ready' : 'pending');
        }
        if (statusPillEl && asg.status_label) {
            statusPillEl.textContent = asg.status_label;
            statusPillEl.className = 'qb-pill qb-pill-' + statusPillClass(asg.status_key);
        }
        if (gradeBoxEl) {
            if (asg.graded) {
                gradeBoxEl.hidden = false;
                gradeBoxEl.innerHTML = '<strong>وضعیت تکلیف: ' + esc(asg.review_label || asg.score_label) + '</strong>'
                    + (asg.graded_label ? '<span>' + esc(asg.graded_label) + '</span>' : '')
                    + (asg.feedback ? '<p>' + esc(asg.feedback).replace(/\n/g, '<br>') + '</p>' : '');
            } else {
                gradeBoxEl.hidden = true;
                gradeBoxEl.innerHTML = '';
            }
        }
        if (btn) {
            btn.textContent = files.length > 0 ? 'افزودن فایل به تحویل' : 'آپلود و تحویل';
        }

        if (!current) {
            return;
        }
        if (!files.length) {
            current.hidden = true;
            current.innerHTML = '';
            return;
        }

        current.hidden = false;
        let html = '<div class="asg-files-head">'
            + '<strong>فایل‌های ارسال‌شده شما (' + files.length + ')</strong>'
            + (asg.submitted_label ? '<span class="qb-hint">آخرین بروزرسانی: ' + esc(asg.submitted_label) + '</span>' : '')
            + '</div><div class="asg-files-grid">';

        files.forEach(function (f) {
            const fid = parseInt(f.id || '0', 10);
            const dlUrl = fid > 0
                ? (fileBase + '?kind=submission_file&id=' + fid)
                : (fileBase + '?kind=submission&id=' + id);
            const inlineUrl = dlUrl + '&inline=1';

            html += '<div class="asg-file-card asg-kind-' + esc(f.kind || 'doc') + '">';
            if (f.kind === 'image' && f.can_inline) {
                html += '<a class="asg-thumb-wrap" href="' + inlineUrl + '" target="_blank" rel="noopener" title="مشاهده تصویر کامل">'
                    + '<img class="asg-file-thumb" src="' + inlineUrl + '" alt="' + esc(f.name) + '" loading="lazy">'
                    + '</a>';
            }
            html += '<div class="asg-file-body">'
                + '<div class="asg-file-meta">'
                + '<span class="asg-file-badge">' + (f.kind === 'image' ? '🖼️ عکس' : (f.kind === 'audio' ? '🎙️ ویس' : '📄 فایل')) + '</span>'
                + '<span class="asg-file-name" title="' + esc(f.name) + '">' + esc(f.name) + '</span>'
                + '<span class="qb-hint">' + esc(f.size_label || '') + '</span>'
                + '</div>';
            if (f.kind === 'audio' && f.can_inline) {
                html += '<audio class="asg-audio-player" controls preload="metadata" src="' + inlineUrl + '"></audio>';
            }
            html += '<div class="asg-file-actions">'
                + (f.kind === 'image' && f.can_inline ? '<a class="qb-btn qb-btn-ghost asg-mini-btn" href="' + inlineUrl + '" target="_blank" rel="noopener">بزرگ‌نمایی</a>' : '')
                + '<a class="qb-btn qb-btn-ghost asg-mini-btn" href="' + dlUrl + '">دانلود</a>'
                + '<button type="button" class="qb-btn qb-btn-danger asg-mini-btn" data-del-fid="' + fid + '">حذف</button>'
                + '</div></div></div>';
        });

        html += '</div>';
        current.innerHTML = html;
    }

    function postFormData(body) {
        return fetch(ajax, { method: 'POST', body: body, credentials: 'same-origin' })
            .then(function (r) {
                const ct = r.headers.get('content-type') || '';
                if (ct.indexOf('json') === -1) {
                    throw new Error('نشست منقضی شده یا حجم فایل بیش از حد مجاز است. صفحه را رفرش کنید.');
                }
                return r.json();
            })
            .then(function (data) {
                if (!data.success) {
                    throw new Error(data.message || 'عملیات انجام نشد.');
                }
                return data;
            });
    }

    function uploadFilesSequential(files) {
        let index = 0;
        const total = files.length;

        function next() {
            if (index >= total) {
                return Promise.resolve();
            }
            const f = files[index];
            const stepLabel = total > 1
                ? ('در حال آپلود فایل ' + (index + 1) + ' از ' + total + ' (' + f.name + ')…')
                : ('در حال آپلود ' + f.name + '…');
            busy(true, stepLabel);
            const body = new FormData();
            body.append('action', 'upload');
            body.append('id', String(id));
            body.append('file', f);
            return postFormData(body).then(function (data) {
                if (data.assignment) {
                    state = data.assignment;
                    renderState();
                }
                index += 1;
                return next();
            });
        }

        return next();
    }

    function validateNewFiles(files) {
        if (!files.length) {
            return 'یک عکس، ویس یا فایل انتخاب کنید.';
        }
        const curFiles = (state && Array.isArray(state.files)) ? state.files : [];
        const curImg = parseInt((state && state.image_count) || '0', 10);
        const curAud = parseInt((state && state.audio_count) || '0', 10);

        let addImg = 0;
        let addAud = 0;
        for (let i = 0; i < files.length; i++) {
            const f = files[i];
            if (boot.max_bytes && f.size > boot.max_bytes) {
                return 'حجم فایل «' + f.name + '» بیشتر از سقف مجاز (' + boot.max_label + ') است.';
            }
            const k = kindOfFile(f);
            if (k === 'image') {
                addImg += 1;
            } else if (k === 'audio') {
                addAud += 1;
            }
        }

        if (curImg + addImg > maxImages) {
            if (curImg >= maxImages) {
                return 'سقف ' + maxImages + ' عکس تکمیل شده است. برای آپلود عکس جدید ابتدا یکی از عکس‌های قبلی را حذف کنید.';
            }
            return 'حداکثر ' + maxImages + ' عکس مجاز است (الان می‌توانید حداکثر ' + (maxImages - curImg) + ' عکس دیگر انتخاب کنید).';
        }
        if (curAud + addAud > maxAudio) {
            return 'حداکثر ' + maxAudio + ' فایل ویس برای هر تکلیف مجاز است.';
        }
        if (curFiles.length + files.length > maxFiles) {
            return 'حداکثر ' + maxFiles + ' فایل پیوست برای هر تکلیف مجاز است.';
        }
        return '';
    }

    if (!form || !id) {
        return;
    }

    renderState();

    if (fileEl && selectedHint) {
        fileEl.addEventListener('change', function () {
            const list = fileEl.files ? Array.prototype.slice.call(fileEl.files) : [];
            if (!list.length) {
                selectedHint.hidden = true;
                selectedHint.textContent = '';
                return;
            }
            const names = list.map(function (f) { return f.name; }).join(' ، ');
            selectedHint.hidden = false;
            selectedHint.textContent = list.length + ' فایل آماده ارسال: ' + names;
            const err = validateNewFiles(list);
            if (err) {
                showAlert(err, false);
            } else {
                showAlert('', true);
            }
        });
    }

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const list = fileEl && fileEl.files ? Array.prototype.slice.call(fileEl.files) : [];
        const err = validateNewFiles(list);
        if (err) {
            showAlert(err, false);
            return;
        }
        showAlert('', true);
        uploadFilesSequential(list)
            .then(function () {
                if (fileEl) {
                    fileEl.value = '';
                }
                if (selectedHint) {
                    selectedHint.hidden = true;
                    selectedHint.textContent = '';
                }
                showAlert(list.length > 1 ? 'همه فایل‌ها با موفقیت تحویل شدند.' : 'فایل با موفقیت تحویل شد.', true);
            })
            .catch(function (uploadErr) {
                showAlert(uploadErr.message, false);
            })
            .then(function () {
                busy(false);
            });
    });

    if (current) {
        current.addEventListener('click', function (e) {
            const delBtn = e.target.closest('button[data-del-fid]');
            if (!delBtn) {
                return;
            }
            const fid = parseInt(delBtn.getAttribute('data-del-fid') || '0', 10);
            if (!window.confirm('این فایل از تحویل شما حذف شود؟')) {
                return;
            }
            busy(true, 'در حال حذف فایل…');
            const body = new FormData();
            body.append('action', 'delete_file');
            body.append('id', String(id));
            body.append('file_id', String(fid));
            postFormData(body)
                .then(function (data) {
                    if (data.assignment) {
                        state = data.assignment;
                        renderState();
                    }
                    showAlert(data.message || 'فایل حذف شد.', true);
                })
                .catch(function (delErr) {
                    showAlert(delErr.message, false);
                })
                .then(function () {
                    busy(false);
                });
        });
    }

    // Built-in Voice Recorder
    const canRecord = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia && window.MediaRecorder);
    if (!canRecord) {
        if (recBox) {
            recBox.hidden = true;
        }
        return;
    }

    let mediaRecorder = null;
    let recStream = null;
    let recChunks = [];
    let recBlob = null;
    let recUrl = '';
    let recExt = 'webm';
    let recMime = 'audio/webm';
    let recSec = 0;
    let recTick = null;
    let recCancelled = false;

    function pickRecorderMime() {
        const candidates = [
            { mime: 'audio/webm;codecs=opus', ext: 'webm', clean: 'audio/webm' },
            { mime: 'audio/webm', ext: 'webm', clean: 'audio/webm' },
            { mime: 'audio/mp4', ext: 'm4a', clean: 'audio/mp4' },
            { mime: 'audio/ogg;codecs=opus', ext: 'ogg', clean: 'audio/ogg' },
            { mime: 'audio/ogg', ext: 'ogg', clean: 'audio/ogg' }
        ];
        if (typeof MediaRecorder.isTypeSupported === 'function') {
            for (let i = 0; i < candidates.length; i++) {
                if (MediaRecorder.isTypeSupported(candidates[i].mime)) {
                    return candidates[i];
                }
            }
        }
        return { mime: '', ext: 'webm', clean: 'audio/webm' };
    }

    function fmtTime(sec) {
        const m = Math.floor(sec / 60);
        const s = sec % 60;
        return (m < 10 ? '0' + m : String(m)) + ':' + (s < 10 ? '0' + s : String(s));
    }

    function stopTracks() {
        if (recStream) {
            recStream.getTracks().forEach(function (t) {
                try { t.stop(); } catch (e) {}
            });
            recStream = null;
        }
        if (recTick) {
            clearInterval(recTick);
            recTick = null;
        }
    }

    function clearRecPreview() {
        recBlob = null;
        if (recUrl) {
            try { URL.revokeObjectURL(recUrl); } catch (e) {}
            recUrl = '';
        }
        if (recAudio) {
            recAudio.removeAttribute('src');
            recAudio.load();
        }
        if (recPreview) {
            recPreview.hidden = true;
        }
    }

    function resetRecorderUI() {
        if (recStartBtn) recStartBtn.hidden = false;
        if (recStopBtn) recStopBtn.hidden = true;
        if (recCancelBtn) recCancelBtn.hidden = true;
        if (recLive) recLive.hidden = true;
        if (recTimer) recTimer.textContent = '00:00';
    }

    if (recStartBtn) {
        recStartBtn.addEventListener('click', function () {
            const curAud = parseInt((state && state.audio_count) || '0', 10);
            const curTotal = (state && Array.isArray(state.files)) ? state.files.length : 0;
            if (curAud >= maxAudio) {
                showAlert('حداکثر ' + maxAudio + ' فایل ویس مجاز است. ابتدا یکی از ویس‌های قبلی را حذف کنید.', false);
                return;
            }
            if (curTotal >= maxFiles) {
                showAlert('سقف تعداد فایل‌های پیوست تکمیل شده است.', false);
                return;
            }
            clearRecPreview();
            showAlert('', true);

            navigator.mediaDevices.getUserMedia({ audio: true })
                .then(function (stream) {
                    recStream = stream;
                    recChunks = [];
                    recCancelled = false;
                    const chosen = pickRecorderMime();
                    recExt = chosen.ext;
                    recMime = chosen.clean;
                    mediaRecorder = chosen.mime
                        ? new MediaRecorder(stream, { mimeType: chosen.mime })
                        : new MediaRecorder(stream);

                    mediaRecorder.addEventListener('dataavailable', function (ev) {
                        if (ev.data && ev.data.size > 0) {
                            recChunks.push(ev.data);
                        }
                    });

                    mediaRecorder.addEventListener('stop', function () {
                        stopTracks();
                        resetRecorderUI();
                        if (recCancelled || !recChunks.length) {
                            return;
                        }
                        const actualType = (mediaRecorder && mediaRecorder.mimeType) || recMime;
                        if (actualType.indexOf('mp4') !== -1 || actualType.indexOf('aac') !== -1) {
                            recExt = 'm4a';
                            recMime = 'audio/mp4';
                        } else if (actualType.indexOf('ogg') !== -1) {
                            recExt = 'ogg';
                            recMime = 'audio/ogg';
                        }
                        recBlob = new Blob(recChunks, { type: recMime });
                        recUrl = URL.createObjectURL(recBlob);
                        if (recAudio) {
                            recAudio.src = recUrl;
                        }
                        if (recPreview) {
                            recPreview.hidden = false;
                        }
                    });

                    mediaRecorder.start(250);
                    recSec = 0;
                    if (recStartBtn) recStartBtn.hidden = true;
                    if (recStopBtn) recStopBtn.hidden = false;
                    if (recCancelBtn) recCancelBtn.hidden = false;
                    if (recLive) recLive.hidden = false;
                    if (recTimer) recTimer.textContent = '00:00';
                    recTick = setInterval(function () {
                        recSec += 1;
                        if (recTimer) {
                            recTimer.textContent = fmtTime(recSec);
                        }
                        if (recSec >= 600 && mediaRecorder && mediaRecorder.state === 'recording') {
                            mediaRecorder.stop();
                        }
                    }, 1000);
                })
                .catch(function () {
                    stopTracks();
                    resetRecorderUI();
                    showAlert('دسترسی به میکروفون داده نشد. می‌توانید فایل ویس آماده را از بخش انتخاب فایل بالا آپلود کنید.', false);
                });
        });
    }

    if (recStopBtn) {
        recStopBtn.addEventListener('click', function () {
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            }
        });
    }

    if (recCancelBtn) {
        recCancelBtn.addEventListener('click', function () {
            recCancelled = true;
            if (mediaRecorder && mediaRecorder.state === 'recording') {
                mediaRecorder.stop();
            } else {
                stopTracks();
                resetRecorderUI();
            }
            clearRecPreview();
        });
    }

    if (recDiscardBtn) {
        recDiscardBtn.addEventListener('click', function () {
            clearRecPreview();
        });
    }

    if (recSendBtn) {
        recSendBtn.addEventListener('click', function () {
            if (!recBlob) {
                return;
            }
            const nextIdx = (parseInt((state && state.audio_count) || '0', 10) + 1);
            const fname = 'voice-' + nextIdx + '.' + recExt;
            let voiceFile;
            try {
                voiceFile = new File([recBlob], fname, { type: recMime });
            } catch (err) {
                recBlob.name = fname;
                voiceFile = recBlob;
            }
            const err = validateNewFiles([voiceFile]);
            if (err) {
                showAlert(err, false);
                return;
            }
            busy(true, 'در حال ارسال ویس…');
            const body = new FormData();
            body.append('action', 'upload');
            body.append('id', String(id));
            body.append('file', voiceFile, fname);
            postFormData(body)
                .then(function (data) {
                    if (data.assignment) {
                        state = data.assignment;
                        renderState();
                    }
                    clearRecPreview();
                    showAlert('ویس با موفقیت ارسال شد.', true);
                })
                .catch(function (sendErr) {
                    showAlert(sendErr.message, false);
                })
                .then(function () {
                    busy(false);
                });
        });
    }
})();

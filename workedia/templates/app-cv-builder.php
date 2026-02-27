<?php if (!defined('ABSPATH')) exit; ?>
<?php
if (!defined('ABSPATH')) exit;
$user_id = get_current_user_id();
$existing_cvs = Workedia_CVBuilder::get_cvs($user_id);
$cv = !empty($existing_cvs) ? $existing_cvs[0] : null;
$cv_content = $cv ? $cv->content : 'null';
$cv_settings = $cv ? $cv->settings : 'null';
$cv_id = $cv ? $cv->id : 0;
?>
<div class="workedia-app-container cv-builder-app" dir="rtl">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">منشئ السيرة الذاتية الاحترافي</h2>
        <div style="display: flex; gap: 10px;">
            <button onclick="workediaSaveCV()" class="workedia-btn" style="width: auto;"><span class="dashicons dashicons-saved"></span> حفظ التغييرات</button>
            <button onclick="window.print()" class="workedia-btn workedia-btn-outline" style="width: auto;"><span class="dashicons dashicons-pdf"></span> تصدير PDF</button>
        </div>
    </div>

    <div class="cv-builder-layout" style="display: grid; grid-template-columns: 450px 1fr; gap: 30px;">
        <!-- Left: Editor Panel -->
        <div class="cv-editor-panel" style="background: #fff; border-radius: 20px; border: 1px solid var(--workedia-border-color); box-shadow: var(--workedia-shadow); overflow: hidden; display: flex; flex-direction: column; max-height: 85vh;">
            <div class="editor-tabs" style="display: flex; background: #f8fafc; border-bottom: 1px solid #eee;">
                <button class="editor-tab-btn active" onclick="switchEditorTab('basic', this)">البيانات</button>
                <button class="editor-tab-btn" onclick="switchEditorTab('sections', this)">الأقسام</button>
                <button class="editor-tab-btn" onclick="switchEditorTab('design', this)">التصميم</button>
            </div>

            <div class="editor-content" style="padding: 25px; overflow-y: auto; flex: 1;">
                <!-- Basic Tab -->
                <div id="tab-basic" class="editor-section-content active">
                    <div class="workedia-form-group">
                        <label class="workedia-label">لغة السيرة الذاتية:</label>
                        <select id="cv-lang" class="workedia-select" onchange="updateCVLanguage(this.value)">
                            <option value="ar">العربية (RTL)</option>
                            <option value="en">الإنجليزية (LTR)</option>
                        </select>
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label">الاسم الكامل:</label>
                        <input type="text" id="cv-name" class="workedia-input" oninput="liveUpdate('name', this.value)" placeholder="أدخل اسمك الكامل">
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label">المسمى الوظيفي:</label>
                        <input type="text" id="cv-job" class="workedia-input" oninput="liveUpdate('job_title', this.value)" placeholder="مثال: مطور برمجيات أول">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="workedia-form-group"><label class="workedia-label">البريد:</label><input type="email" id="cv-email" class="workedia-input" oninput="liveUpdate('email', this.value)"></div>
                        <div class="workedia-form-group"><label class="workedia-label">الهاتف:</label><input type="text" id="cv-phone" class="workedia-input" oninput="liveUpdate('phone', this.value)"></div>
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label">نبذة مهنية (ATS Optimized):</label>
                        <textarea id="cv-summary" class="workedia-textarea" rows="4" oninput="liveUpdate('summary', this.value)"></textarea>
                    </div>
                </div>

                <!-- Sections Tab -->
                <div id="tab-sections" class="editor-section-content">
                    <div id="cv-sections-list" class="sortable-list">
                        <!-- Dynamic sections like Experience, Education -->
                    </div>
                    <button onclick="addCVSection()" class="workedia-btn workedia-btn-outline" style="width: 100%; margin-top: 15px;">+ إضافة قسم جديد</button>
                </div>

                <!-- Design Tab -->
                <div id="tab-design" class="editor-section-content">
                    <div class="workedia-form-group">
                        <label class="workedia-label">اختر القالب:</label>
                        <div class="template-grid" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div class="tpl-card active" onclick="selectTemplate('modern', this)">Modern</div>
                            <div class="tpl-card" onclick="selectTemplate('classic', this)">Classic</div>
                            <div class="tpl-card" onclick="selectTemplate('executive', this)">Executive</div>
                        </div>
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label">لون السمة:</label>
                        <input type="color" id="cv-theme-color" class="workedia-input" style="height: 40px;" onchange="liveUpdateSettings('color', this.value)">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Live Preview Panel -->
        <div class="cv-preview-panel" style="background: #525659; border-radius: 20px; padding: 40px; overflow-y: auto; max-height: 85vh; display: flex; justify-content: center;">
            <div id="cv-preview-page" class="cv-page a4-size" style="background: #fff; width: 210mm; min-height: 297mm; box-shadow: 0 15px 35px rgba(0,0,0,0.2); padding: 20mm; position: relative;">
                <!-- Rendered Template Content -->
            </div>
        </div>
    </div>
</div>

<style>
.editor-tab-btn { flex: 1; padding: 15px; border: none; background: transparent; cursor: pointer; font-weight: 700; color: #64748b; transition: 0.2s; }
.editor-tab-btn.active { background: #fff; color: var(--workedia-primary-color); border-top: 3px solid var(--workedia-primary-color); }
.editor-section-content { display: none; }
.editor-section-content.active { display: block; }

.tpl-card { border: 2px solid #eee; border-radius: 10px; padding: 15px; text-align: center; cursor: pointer; transition: 0.2s; font-weight: 700; }
.tpl-card:hover, .tpl-card.active { border-color: var(--workedia-primary-color); background: var(--workedia-pastel-red); }

@media print {
    .workedia-sidebar, .workedia-main-header, .cv-editor-panel, .cv-builder-app h2, .cv-builder-app .workedia-btn { display: none !important; }
    body, .workedia-admin-dashboard, .workedia-main-panel { background: white !important; padding: 0 !important; margin: 0 !important; }
    .cv-preview-panel { background: white !important; padding: 0 !important; overflow: visible !important; max-height: none !important; }
    .cv-page { box-shadow: none !important; margin: 0 !important; width: 100% !important; }
}

.cv-page[dir="rtl"] { text-align: right; }
.cv-page[dir="ltr"] { text-align: left; }
</style>

<script>
let cvId = <?php echo $cv_id; ?>;
let cvData = <?php echo $cv_content; ?> || {
    name: '',
    job_title: '',
    email: '',
    phone: '',
    summary: '',
    sections: []
};

let cvSettings = <?php echo $cv_settings; ?> || {
    lang: 'ar',
    template: 'modern',
    color: '#F63049'
};

// Sync UI with loaded data
window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('cv-lang').value = cvSettings.lang;
    document.getElementById('cv-name').value = cvData.name || '';
    document.getElementById('cv-job').value = cvData.job_title || '';
    document.getElementById('cv-email').value = cvData.email || '';
    document.getElementById('cv-phone').value = cvData.phone || '';
    document.getElementById('cv-summary').value = cvData.summary || '';
    document.getElementById('cv-theme-color').value = cvSettings.color || '#F63049';

    document.querySelectorAll('.tpl-card').forEach(c => {
        if (c.innerText.toLowerCase() === cvSettings.template) c.classList.add('active');
        else c.classList.remove('active');
    });

    renderSectionsEditor();
    renderPreview();
    initSortable();
});

function switchEditorTab(tab, btn) {
    document.querySelectorAll('.editor-tab-btn').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.editor-section-content').forEach(s => s.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-' + tab).classList.add('active');
}

function liveUpdate(key, val) {
    cvData[key] = val;
    renderPreview();
}

function liveUpdateSettings(key, val) {
    cvSettings[key] = val;
    renderPreview();
}

function updateCVLanguage(lang) {
    cvSettings.lang = lang;
    const preview = document.getElementById('cv-preview-page');
    preview.dir = lang === 'ar' ? 'rtl' : 'ltr';
    renderPreview();
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderPreview() {
    const preview = document.getElementById('cv-preview-page');
    const dir = cvSettings.lang === 'ar' ? 'rtl' : 'ltr';
    preview.setAttribute('dir', dir);

    let sectionsHtml = '';
    cvData.sections.forEach(s => {
        sectionsHtml += `
            <div style="margin-bottom: 25px;">
                <h3 style="color: ${cvSettings.color}; border-bottom: 2px solid ${cvSettings.color}33; padding-bottom: 5px; margin-bottom: 10px; font-size: 14pt;">${escapeHtml(s.title)}</h3>
                <div style="font-size: 10pt; line-height: 1.5; white-space: pre-wrap;">${escapeHtml(s.content)}</div>
            </div>
        `;
    });

    preview.innerHTML = `
        <div style="border-bottom: 4px solid ${cvSettings.color}; padding-bottom: 20px; margin-bottom: 30px;">
            <h1 style="margin: 0; color: ${cvSettings.color}; font-size: 28pt; font-weight: 800;">${escapeHtml(cvData.name) || (cvSettings.lang === 'ar' ? 'الاسم الكامل' : 'Full Name')}</h1>
            <h2 style="margin: 5px 0 0 0; color: #4a5568; font-size: 16pt; font-weight: 600;">${escapeHtml(cvData.job_title) || (cvSettings.lang === 'ar' ? 'المسمى الوظيفي' : 'Job Title')}</h2>
            <div style="margin-top: 15px; display: flex; gap: 20px; font-size: 10pt; color: #718096; font-weight: 500;">
                ${cvData.email ? `<span><span class="dashicons dashicons-email" style="font-size:14px; width:14px; height:14px;"></span> ${escapeHtml(cvData.email)}</span>` : ''}
                ${cvData.phone ? `<span><span class="dashicons dashicons-phone" style="font-size:14px; width:14px; height:14px;"></span> ${escapeHtml(cvData.phone)}</span>` : ''}
            </div>
        </div>
        <div style="margin-bottom: 30px;">
            <h3 style="color: ${cvSettings.color}; border-bottom: 2px solid ${cvSettings.color}33; padding-bottom: 5px; margin-bottom: 10px; font-size: 14pt;">${cvSettings.lang === 'ar' ? 'الخلاصة المهنية' : 'Professional Summary'}</h3>
            <p style="line-height: 1.6; font-size: 10.5pt; color: #2d3748;">${escapeHtml(cvData.summary)}</p>
        </div>
        ${sectionsHtml}
    `;
}

function renderSectionsEditor() {
    const list = document.getElementById('cv-sections-list');
    if (!list) return;
    list.innerHTML = cvData.sections.map((s, idx) => `
        <div class="cv-section-item" data-id="${s.id}" style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; margin-bottom: 15px; position: relative;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span class="section-drag-handle" style="cursor: move; color: #94a3b8;"><span class="dashicons dashicons-move"></span></span>
                <button onclick="removeCVSection(${s.id})" style="background:none; border:none; color:#e53e3e; cursor:pointer;"><span class="dashicons dashicons-trash"></span></button>
            </div>
            <input type="text" class="workedia-input" value="${escapeHtml(s.title)}" oninput="updateSection(${s.id}, 'title', this.value)" style="margin-bottom: 10px; font-weight: 800;" placeholder="عنوان القسم (مثلاً: الخبرات)">
            <textarea class="workedia-textarea" rows="4" oninput="updateSection(${s.id}, 'content', this.value)" placeholder="محتوى القسم...">${escapeHtml(s.content)}</textarea>
        </div>
    `).join('');
}

function addCVSection() {
    const id = Date.now();
    cvData.sections.push({ id, title: '', content: '' });
    renderSectionsEditor();
    renderPreview();
    initSortable();
}

function removeCVSection(id) {
    cvData.sections = cvData.sections.filter(s => s.id !== id);
    renderSectionsEditor();
    renderPreview();
    initSortable();
}

function updateSection(id, key, val) {
    const s = cvData.sections.find(sec => sec.id === id);
    if (s) s[key] = val;
    renderPreview();
}

function selectTemplate(tpl, el) {
    cvSettings.template = tpl;
    document.querySelectorAll('.tpl-card').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    renderPreview();
}

function workediaSaveCV() {
    const fd = new FormData();
    fd.append('action', 'workedia_save_cv');
    if (cvId) fd.append('id', cvId);
    fd.append('title', cvData.name || 'My CV');
    fd.append('language', cvSettings.lang);
    fd.append('template', cvSettings.template);
    fd.append('content', JSON.stringify(cvData));
    fd.append('settings', JSON.stringify(cvSettings));

    workediaShowNotification('جاري حفظ السيرة الذاتية...');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حفظ السيرة الذاتية بنجاح');
            cvId = res.data;
        } else alert(res.data);
    });
}

function initSortable() {
    const el = document.getElementById('cv-sections-list');
    Sortable.create(el, {
        animation: 150,
        handle: '.section-drag-handle',
        onEnd: function() {
            // Update cvData.sections order
            const newOrder = Array.from(el.querySelectorAll('.cv-section-item')).map(item => item.dataset.id);
            cvData.sections.sort((a, b) => newOrder.indexOf(a.id) - newOrder.indexOf(b.id));
            renderPreview();
        }
    });
}

</script>

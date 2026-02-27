<?php if (!defined('ABSPATH')) exit; ?>
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
                            <div class="tpl-card active" onclick="selectTemplate('modern')">Modern</div>
                            <div class="tpl-card" onclick="selectTemplate('classic')">Classic</div>
                            <div class="tpl-card" onclick="selectTemplate('executive')">Executive</div>
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
let cvData = {
    name: '',
    job_title: '',
    email: '',
    phone: '',
    summary: '',
    sections: []
};

let cvSettings = {
    lang: 'ar',
    template: 'modern',
    color: '#F63049'
};

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

function renderPreview() {
    // Ajax call to render template or client-side rendering
    // For now, let's simulate a basic structure
    const preview = document.getElementById('cv-preview-page');
    const dir = cvSettings.lang === 'ar' ? 'rtl' : 'ltr';
    preview.setAttribute('dir', dir);

    preview.innerHTML = `
        <div style="border-bottom: 3px solid ${cvSettings.color}; padding-bottom: 20px; margin-bottom: 30px;">
            <h1 style="margin: 0; color: ${cvSettings.color}; font-size: 32pt;">${cvData.name || 'الاسم الكامل'}</h1>
            <h2 style="margin: 5px 0 0 0; color: #444; font-size: 18pt;">${cvData.job_title || 'المسمى الوظيفي'}</h2>
            <div style="margin-top: 15px; display: flex; gap: 20px; font-size: 10pt; color: #666;">
                <span>${cvData.email}</span>
                <span>${cvData.phone}</span>
            </div>
        </div>
        <div style="margin-bottom: 30px;">
            <h3 style="color: ${cvSettings.color}; border-bottom: 1px solid #eee; padding-bottom: 5px;">${cvSettings.lang === 'ar' ? 'الخلاصة المهنية' : 'Professional Summary'}</h3>
            <p style="line-height: 1.6; font-size: 11pt;">${cvData.summary}</p>
        </div>
    `;
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

// Initial render
renderPreview();
window.onload = initSortable;
</script>

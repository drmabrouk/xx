<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container form-builder-app">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">منشئ النماذج الذكي</h2>
        <button onclick="workediaOpenFormCreator()" class="workedia-btn" style="width: auto;">+ إنشاء نموذج جديد</button>
    </div>

    <div id="forms-dashboard" class="workedia-card-grid">
        <?php
        $forms = Workedia_FormBuilder::get_forms(get_current_user_id());
        if (empty($forms)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px; background: #fff; border: 2px dashed #e2e8f0; border-radius: 24px; color: #94a3b8;">
                <span class="dashicons dashicons-forms" style="font-size: 64px; width: 64px; height: 64px; opacity: 0.2;"></span>
                <p style="font-size: 1.2em; font-weight: 600; margin-top: 20px;">لم تقم بإنشاء أي نماذج بعد.</p>
                <button onclick="workediaOpenFormCreator()" class="workedia-btn workedia-btn-outline" style="width: auto; margin-top: 20px;">ابدأ الآن</button>
            </div>
        <?php else: foreach ($forms as $form): ?>
            <div class="note-modern-card form-card" style="text-align: right; position: relative; overflow: hidden; animation: workediaSlideUp 0.3s ease-out;">
                <div class="note-card-body">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                    <div style="background: var(--workedia-pastel-pink); width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: var(--workedia-primary-color);">
                        <span class="dashicons dashicons-forms" style="font-size:24px; width:24px; height:24px;"></span>
                    </div>
                    <div class="workedia-actions-dropdown">
                        <button class="workedia-actions-trigger">...</button>
                        <div class="workedia-actions-content">
                            <a href="javascript:void(0)" onclick='workediaViewSubmissions(<?php echo $form->id; ?>, "<?php echo esc_js($form->title); ?>")' class="workedia-action-item"><span class="dashicons dashicons-list-view"></span> عرض الردود</a>
                            <a href="javascript:void(0)" onclick='workediaCopyFormLink("<?php echo esc_js($form->public_token); ?>")' class="workedia-action-item"><span class="dashicons dashicons-admin-links"></span> نسخ الرابط</a>
                            <a href="javascript:void(0)" onclick="workediaDuplicateForm(<?php echo $form->id; ?>)" class="workedia-action-item"><span class="dashicons dashicons-admin-page"></span> تكرار النموذج</a>
                            <a href="javascript:void(0)" onclick="workediaDeleteForm(<?php echo $form->id; ?>)" class="workedia-action-item" style="color: #e53e3e !important;"><span class="dashicons dashicons-trash"></span> حذف</a>
                        </div>
                    </div>
                </div>
                <h3 class="note-card-title" style="margin-bottom: 5px;"><?php echo esc_html($form->title); ?></h3>
                <p style="color: #64748b; font-size: 12px; line-height: 1.5; min-height: 36px;"><?php echo esc_html($form->description); ?></p>
                <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; font-weight: 800; color: var(--workedia-primary-color);"><?php echo $form->response_count; ?> ردود</span>
                    <span style="font-size: 11px; color: #94a3b8;"><?php echo date('Y-m-d', strtotime($form->created_at)); ?></span>
                </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<!-- Form Creation Modal -->
<div id="form-creation-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 900px; padding: 0;">
        <div class="workedia-modal-header" style="padding: 25px 40px; background: #fafafa; border-bottom: 1px solid #eee; margin: 0;">
            <h3>تصميم نموذج جديد</h3>
            <button class="workedia-modal-close" onclick="document.getElementById('form-creation-modal').style.display='none'">&times;</button>
        </div>

        <div style="padding: 40px; display: grid; grid-template-columns: 300px 1fr; gap: 40px;">
            <!-- Left: Config -->
            <div>
                <div class="workedia-form-group">
                    <label class="workedia-label">عنوان النموذج:</label>
                    <input type="text" id="form-title" class="workedia-input" placeholder="مثال: استبيان رضا العملاء">
                </div>
                <div class="workedia-form-group">
                    <label class="workedia-label">وصف النموذج:</label>
                    <textarea id="form-description" class="workedia-textarea" rows="3" placeholder="اكتب نبذة قصيرة للمستخدمين..."></textarea>
                </div>
                <div class="workedia-form-group">
                    <label class="workedia-label">رسالة النجاح المخصصة:</label>
                    <input type="text" id="form-success-msg" class="workedia-input" placeholder="مثال: شكراً لك! تم استلام ردك.">
                </div>

                <h4 style="margin: 30px 0 15px 0; font-size: 14px; color: #1a202c; border-bottom: 1px solid #eee; padding-bottom: 8px;">إضافة حقول</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <button class="workedia-btn workedia-btn-outline" onclick="addFormField('text')" style="font-size: 11px; padding: 8px;"><span class="dashicons dashicons-editor-textcolor"></span> نص قصير</button>
                    <button class="workedia-btn workedia-btn-outline" onclick="addFormField('textarea')" style="font-size: 11px; padding: 8px;"><span class="dashicons dashicons-editor-paragraph"></span> نص طويل</button>
                    <button class="workedia-btn workedia-btn-outline" onclick="addFormField('number')" style="font-size: 11px; padding: 8px;"><span class="dashicons dashicons-editor-ol"></span> رقم</button>
                    <button class="workedia-btn workedia-btn-outline" onclick="addFormField('email')" style="font-size: 11px; padding: 8px;"><span class="dashicons dashicons-email"></span> بريد إلكتروني</button>
                    <button class="workedia-btn workedia-btn-outline" onclick="addFormField('date')" style="font-size: 11px; padding: 8px;"><span class="dashicons dashicons-calendar-alt"></span> تاريخ</button>
                    <button class="workedia-btn workedia-btn-outline" onclick="addFormField('select')" style="font-size: 11px; padding: 8px;"><span class="dashicons dashicons-menu"></span> قائمة منسدلة</button>
                </div>
            </div>

            <!-- Right: Preview/Builder -->
            <div style="background: #f8fafc; border-radius: 16px; border: 1px solid #edf2f7; padding: 25px; min-height: 400px;">
                <div id="form-builder-preview">
                    <div style="text-align: center; color: #94a3b8; margin-top: 100px;">
                        <span class="dashicons dashicons-layout" style="font-size: 40px; width: 40px; height: 40px; opacity: 0.3;"></span>
                        <p>تظهر الحقول المضافة هنا للمعاينة</p>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 20px 40px; background: #fafafa; border-top: 1px solid #eee; text-align: left;">
            <button onclick="saveForm()" class="workedia-btn" style="width: auto; padding: 0 40px; height: 45px; font-weight: 800;">حفظ ونشر النموذج</button>
        </div>
    </div>
</div>

<!-- Submissions Modal -->
<div id="submissions-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 1000px;">
        <div class="workedia-modal-header">
            <h3 id="sub-modal-title">الردود المستلمة</h3>
            <button class="workedia-modal-close" onclick="document.getElementById('submissions-modal').style.display='none'">&times;</button>
        </div>
        <div id="submissions-container" style="padding: 20px;">
            <!-- Loaded via AJAX -->
        </div>
    </div>
</div>

<script>
let currentFormFields = [];

function workediaOpenFormCreator() {
    currentFormFields = [];
    document.getElementById('form-title').value = '';
    document.getElementById('form-description').value = '';
    document.getElementById('form-success-msg').value = '';
    renderBuilder();
    document.getElementById('form-creation-modal').style.display = 'flex';
}

function addFormField(type) {
    const label = prompt('أدخل اسم الحقل (مثال: الاسم بالكامل):');
    if (!label) return;

    let options = [];
    if (type === 'select') {
        const optsStr = prompt('أدخل الخيارات مفصولة بفاصلة (مثال: خيار 1, خيار 2):');
        if (optsStr) options = optsStr.split(',').map(s => s.trim());
    }

    currentFormFields.push({
        id: Date.now(),
        type,
        label,
        required: false,
        options: options
    });
    renderBuilder();
}

function toggleRequired(id) {
    const field = currentFormFields.find(f => f.id === id);
    if (field) field.required = !field.required;
    renderBuilder();
}

function removeFormField(id) {
    currentFormFields = currentFormFields.filter(f => f.id !== id);
    renderBuilder();
}

function renderBuilder() {
    const preview = document.getElementById('form-builder-preview');
    if (currentFormFields.length === 0) {
        preview.innerHTML = '<div style="text-align: center; color: #94a3b8; margin-top: 100px;"><span class="dashicons dashicons-layout" style="font-size: 40px; width: 40px; height: 40px; opacity: 0.3;"></span><p>تظهر الحقول المضافة هنا للمعاينة</p></div>';
        return;
    }

    preview.innerHTML = currentFormFields.map(f => `
        <div class="builder-item" data-id="${f.id}" style="background: white; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); cursor: move;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <span style="font-size: 10px; font-weight: 800; color: var(--workedia-primary-color); text-transform: uppercase;"><span class="dashicons dashicons-move" style="font-size: 14px; width: 14px; height: 14px; margin-left: 5px;"></span> ${f.type} ${f.required ? '<span style="color:#e53e3e;">* مطلوب</span>' : ''}</span>
                <div style="display: flex; gap: 10px;">
                    <button onclick="toggleRequired(${f.id})" style="background:none; border:none; cursor:pointer; color:#94a3b8;" title="تغيير حالة الإلزام"><span class="dashicons dashicons-flag"></span></button>
                    <button onclick="removeFormField(${f.id})" style="background:none; border:none; cursor:pointer; color:#e53e3e;" title="حذف"><span class="dashicons dashicons-trash"></span></button>
                </div>
            </div>
            <label style="display:block; margin-bottom:8px; font-weight:700;">${f.label}</label>
            ${renderFieldPreview(f)}
        </div>
    `).join('');

    Sortable.create(preview, {
        animation: 150,
        onEnd: function() {
            const newOrder = [];
            preview.querySelectorAll('.builder-item').forEach(el => {
                const id = parseInt(el.getAttribute('data-id'));
                newOrder.push(currentFormFields.find(f => f.id === id));
            });
            currentFormFields = newOrder;
        }
    });
}

function renderFieldPreview(f) {
    switch(f.type) {
        case 'textarea': return '<textarea class="workedia-textarea" disabled rows="2"></textarea>';
        case 'select': return `<select class="workedia-select" disabled><option>-- اختر --</option>${f.options.map(o => `<option>${o}</option>`).join('')}</select>`;
        case 'date': return '<input type="date" class="workedia-input" disabled>';
        default: return '<input type="text" class="workedia-input" disabled>';
    }
}

function saveForm() {
    const title = document.getElementById('form-title').value;
    const description = document.getElementById('form-description').value;
    const successMsg = document.getElementById('form-success-msg').value;
    if (!title || currentFormFields.length === 0) return alert('يرجى إدخال عنوان وإضافة حقل واحد على الأقل.');

    const fd = new FormData();
    fd.append('action', 'workedia_save_form');
    fd.append('title', title);
    fd.append('description', description);
    fd.append('fields', JSON.stringify(currentFormFields));
    fd.append('settings', JSON.stringify({ success_message: successMsg }));
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_formbuilder_action"); ?>');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حفظ ونشر النموذج بنجاح');
            location.reload();
        } else alert(res.data);
    });
}

function workediaDeleteForm(id) {
    if (!confirm('هل أنت متأكد من حذف هذا النموذج وكافة الردود الخاصة به؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_form');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_formbuilder_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}

function workediaCopyFormLink(token) {
    const link = `<?php echo home_url('/forms?f='); ?>${token}`;
    navigator.clipboard.writeText(link).then(() => {
        workediaShowNotification('تم نسخ رابط النموذج بنجاح');
    });
}

function workediaDuplicateForm(id) {
    if (!confirm('هل تريد إنشاء نسخة مطابقة من هذا النموذج؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_duplicate_form');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_formbuilder_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم تكرار النموذج بنجاح');
            location.reload();
        } else alert(res.data);
    });
}

function workediaViewSubmissions(id, title) {
    document.getElementById('sub-modal-title').innerText = 'الردود المستلمة: ' + title;
    const container = document.getElementById('submissions-container');
    container.innerHTML = '<div style="text-align: center; padding: 50px;"><div class="workedia-loader-mini"></div></div>';
    document.getElementById('submissions-modal').style.display = 'flex';

    const fd = new FormData();
    fd.append('action', 'workedia_get_submissions');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_formbuilder_action"); ?>');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success && res.data.length > 0) {
            window.currentSubmissions = res.data;
            renderSubmissions(res.data);
        } else {
            container.innerHTML = '<div style="text-align: center; padding: 50px; color: #94a3b8;">لا توجد ردود بعد.</div>';
        }
    });
}

function renderSubmissions(data) {
    const container = document.getElementById('submissions-container');
    let csvHeader = "Time";
    const sampleData = data[0] ? JSON.parse(data[0].submission_data) : {};
    for (let k in sampleData) csvHeader += `,${k}`;

    let csvData = csvHeader + "\n";
    let html = `
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <input type="text" placeholder="بحث في الردود..." class="workedia-input" style="width: 300px; height: 35px; font-size: 12px;" oninput="filterSubmissions(this.value)">
            <button onclick="exportSubmissionsToCSV()" class="workedia-btn" style="width: auto; height: 35px; font-size: 12px; background: #27ae60;">تصدير إلى CSV</button>
        </div>
        <table class="workedia-table" id="subs-table">
        <thead><tr><th>الوقت</th><th>بيانات الرد</th></tr></thead><tbody>`;

    data.forEach(s => {
        const rowData = JSON.parse(s.submission_data);
        let dataHtml = '';
        let csvRow = `"${s.submitted_at}"`;
        for (let k in rowData) {
            dataHtml += `<div><span style="color:#94a3b8; font-size:11px;">${k}:</span> <strong>${rowData[k]}</strong></div>`;
            csvRow += `,"${rowData[k].toString().replace(/"/g, '""')}"`;
        }
        html += `<tr><td style="font-size:11px; color:#94a3b8; width:150px;">${s.submitted_at}</td><td>${dataHtml}</td></tr>`;
        csvData += csvRow + "\n";
    });
    html += '</tbody></table>';
    container.innerHTML = html;
    window.lastSubmissionCSV = csvData;
}

function filterSubmissions(val) {
    const filtered = window.currentSubmissions.filter(s => s.submission_data.toLowerCase().includes(val.toLowerCase()));
    renderSubmissions(filtered);
}

function exportSubmissionsToCSV() {
    const blob = new Blob([window.lastSubmissionCSV], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement("a");
    const url = URL.createObjectURL(blob);
    link.setAttribute("href", url);
    link.setAttribute("download", "form_submissions.csv");
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<?php if (!defined('ABSPATH')) exit;
$project_id = intval($_GET['project_id'] ?? 0);
$project = Workedia_ReferenceManager::get_project($project_id);
if (!$project) { echo 'Unauthorized'; return; }
?>
<div class="workedia-app-container reference-library-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div style="display: flex; align-items: center; gap: 15px;">
            <a href="<?php echo add_query_arg('workedia_tab', 'reference-manager'); ?>" class="workedia-header-circle-icon"><span class="dashicons dashicons-arrow-right-alt2"></span></a>
            <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">مكتبة المراجع: <?php echo esc_html($project->title); ?></h2>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="workediaOpenRefModal()" class="workedia-btn" style="width: auto;">+ إضافة مرجع يدوي</button>
            <button onclick="workediaOpenSmartSearch()" class="workedia-btn" style="width: auto; background: #3182ce;"><span class="dashicons dashicons-search"></span> بحث ذكي</button>
        </div>
    </div>

    <div class="docs-filters" style="background: #fff; padding: 20px; border-radius: 16px; border: 1px solid var(--workedia-border-color); margin-bottom: 30px; display: flex; gap: 20px; align-items: center;">
        <div style="flex: 1; position: relative;">
            <input type="text" id="ref-search" class="workedia-input" placeholder="بحث في المراجع..." oninput="workediaFilterRefs(this.value)" style="padding-left: 35px; border-radius: 50px;">
            <span class="dashicons dashicons-search" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></span>
        </div>
        <select id="ref-filter-type" class="workedia-select" onchange="workediaFilterRefs()" style="width: 200px; border-radius: 50px;">
            <option value="">كل أنواع المراجع</option>
            <option value="book">كتب</option>
            <option value="article">مقالات علمية</option>
            <option value="thesis">رسائل علمية</option>
            <option value="website">مواقع إلكترونية</option>
            <option value="report">تقارير حكومية</option>
        </select>
    </div>

    <div id="refs-list-container">
        <?php
        $references = Workedia_ReferenceManager::get_references($project_id);
        include WORKEDIA_PLUGIN_DIR . 'templates/app-reference-library-list.php';
        ?>
    </div>
</div>

<!-- Manual Ref Modal -->
<div id="ref-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 650px;">
        <div class="workedia-modal-header">
            <h3 id="ref-modal-title">إضافة مرجع جديد</h3>
            <button class="workedia-modal-close" onclick="document.getElementById('ref-modal').style.display='none'">&times;</button>
        </div>
        <form id="ref-form" style="padding: 20px;">
            <input type="hidden" name="id" id="ref-id">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="workedia-form-group">
                    <label class="workedia-label">نوع المرجع:</label>
                    <select name="ref_type" id="ref-type" class="workedia-select" onchange="workediaAdaptRefForm(this.value)">
                        <option value="book">كتاب (Book)</option>
                        <option value="article">مقال علمي (Article)</option>
                        <option value="website">موقع إلكتروني (Website)</option>
                        <option value="thesis">رسالة علمية (Thesis)</option>
                        <option value="report">تقرير (Report)</option>
                    </select>
                </div>
                <div class="workedia-form-group">
                    <label class="workedia-label">سنة النشر:</label>
                    <input type="text" name="year" id="ref-year" class="workedia-input" required placeholder="مثال: 2023">
                </div>
            </div>

            <div class="workedia-form-group">
                <label class="workedia-label">العنوان:</label>
                <input type="text" name="title" id="ref-title" class="workedia-input" required>
            </div>

            <div class="workedia-form-group">
                <label class="workedia-label">المؤلفون (مفصولين بفاصلة):</label>
                <input type="text" name="authors" id="ref-authors" class="workedia-input" required placeholder="مثال: أحمد محمد, علي خالد">
            </div>

            <div id="dynamic-fields-container">
                <!-- Populated based on type -->
            </div>

            <div class="workedia-form-group">
                <label class="workedia-label">ملاحظات إضافية:</label>
                <textarea name="notes" id="ref-notes" class="workedia-textarea" rows="2"></textarea>
            </div>

            <button type="submit" class="workedia-btn" style="width: 100%; margin-top: 10px;">حفظ المرجع</button>
        </form>
    </div>
</div>

<!-- Smart Search Modal -->
<div id="smart-search-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 700px;">
        <div class="workedia-modal-header">
            <h3>البحث الذكي في المكتبات الرقمية</h3>
            <button class="workedia-modal-close" onclick="document.getElementById('smart-search-modal').style.display='none'">&times;</button>
        </div>
        <div style="padding: 20px;">
            <div style="display:flex; gap:10px; margin-bottom:20px;">
                <input type="text" id="smart-query" class="workedia-input" placeholder="أدخل عنوان البحث أو DOI أو اسم المؤلف...">
                <button onclick="workediaRunSmartSearch()" class="workedia-btn" style="width:auto;">بحث</button>
            </div>
            <div id="smart-results" style="max-height:400px; overflow-y:auto; background:#f8fafc; border-radius:12px; padding:15px;">
                <p style="text-align:center; color:#94a3b8; padding:30px;">سيتم عرض نتائج البحث هنا...</p>
            </div>
        </div>
    </div>
</div>

<script>
function workediaOpenRefModal() {
    document.getElementById('ref-form').reset();
    document.getElementById('ref-id').value = '';
    workediaAdaptRefForm('book');
    document.getElementById('ref-modal-title').innerText = 'إضافة مرجع جديد';
    document.getElementById('ref-modal').style.display = 'flex';
}

function workediaAdaptRefForm(type) {
    const container = document.getElementById('dynamic-fields-container');
    let html = '';
    if (type === 'book') {
        html = `<div class="workedia-form-group"><label class="workedia-label">الناشر (Publisher):</label><input type="text" name="publisher" class="workedia-input"></div>`;
    } else if (type === 'article') {
        html = `
            <div class="workedia-form-group"><label class="workedia-label">اسم المجلة (Source Title):</label><input type="text" name="source_title" class="workedia-input" required></div>
            <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:15px;">
                <div class="workedia-form-group"><label class="workedia-label">Volume:</label><input type="text" name="volume" class="workedia-input"></div>
                <div class="workedia-form-group"><label class="workedia-label">Issue:</label><input type="text" name="issue" class="workedia-input"></div>
                <div class="workedia-form-group"><label class="workedia-label">Pages:</label><input type="text" name="pages" class="workedia-input"></div>
            </div>
        `;
    } else if (type === 'website') {
        html = `<div class="workedia-form-group"><label class="workedia-label">رابط الموقع (URL):</label><input type="url" name="url" class="workedia-input" required></div>`;
    }
    container.innerHTML = html;
}

document.getElementById('ref-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', 'workedia_save_reference');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حفظ المرجع بنجاح');
            document.getElementById('ref-modal').style.display = 'none';
            location.reload();
        } else alert(res.data);
    });
});

function workediaOpenSmartSearch() {
    document.getElementById('smart-results').innerHTML = '<p style="text-align:center; color:#94a3b8; padding:30px;">سيتم عرض نتائج البحث هنا...</p>';
    document.getElementById('smart-search-modal').style.display = 'flex';
}

function workediaRunSmartSearch() {
    const q = document.getElementById('smart-query').value;
    if (!q) return;
    const resultsDiv = document.getElementById('smart-results');
    resultsDiv.innerHTML = '<div style="text-align:center; padding:30px;"><div class="workedia-loader-mini"></div></div>';

    const fd = new FormData();
    fd.append('action', 'workedia_smart_search_refs');
    fd.append('query', q);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success && res.data.length > 0) {
            let html = '';
            res.data.forEach((r, i) => {
                html += `
                    <div style="background:white; padding:15px; border-radius:10px; border:1px solid #e2e8f0; margin-bottom:10px; display:flex; justify-content:space-between; align-items:center;">
                        <div style="flex:1;">
                            <div style="font-weight:700; color:#111F35;">${r.title}</div>
                            <div style="font-size:11px; color:#64748b;">${r.authors} (${r.year})</div>
                        </div>
                        <button onclick='workediaImportRef(${JSON.stringify(r)})' class="workedia-btn" style="width:auto; font-size:11px; padding:5px 12px; background:#27ae60;">استيراد</button>
                    </div>
                `;
            });
            resultsDiv.innerHTML = html;
        } else {
            resultsDiv.innerHTML = '<p style="text-align:center; color:#e53e3e; padding:30px;">لم يتم العثور على نتائج.</p>';
        }
    });
}

function workediaImportRef(ref) {
    const fd = new FormData();
    fd.append('action', 'workedia_save_reference');
    fd.append('project_id', '<?php echo $project_id; ?>');
    for (let k in ref) fd.append(k, ref[k]);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم استيراد المرجع بنجاح');
            location.reload();
        }
    });
}

function workediaDeleteRef(id) {
    if (!confirm('هل تريد حذف هذا المرجع؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_reference');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}
</script>

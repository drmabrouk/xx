<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container documents-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">أرشيف الوثائق</h2>
        <button onclick="workediaOpenUploadModal()" class="workedia-btn" style="width: auto;">+ رفع وثيقة جديدة</button>
    </div>

    <div class="docs-filters" style="background: #fff; padding: 20px; border-radius: 16px; border: 1px solid var(--workedia-border-color); margin-bottom: 30px; display: flex; gap: 20px; align-items: center;">
        <div style="flex: 1; position: relative;">
            <input type="text" id="docs-search" class="workedia-input" placeholder="بحث في عناوين الوثائق..." oninput="workediaSearchDocs(this.value)" style="padding-left: 35px; border-radius: 50px;">
            <span class="dashicons dashicons-search" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></span>
        </div>
        <select id="docs-filter-cat" class="workedia-select" onchange="workediaRefreshDocs()" style="width: 200px; border-radius: 50px;">
            <option value="">كل الفئات</option>
            <option value="هوية">وثائق هوية</option>
            <option value="مالي">سجلات مالية</option>
            <option value="عقود">عقود واتفاقيات</option>
            <option value="عام">عام</option>
        </select>
    </div>

    <div id="workedia-docs-list">
        <?php
        $docs = Workedia_Documents::get_documents(get_current_user_id());
        include WORKEDIA_PLUGIN_DIR . 'templates/app-document-archive-list.php';
        ?>
    </div>
</div>

<!-- Upload Modal -->
<div id="upload-doc-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 500px;">
        <div class="workedia-modal-header">
            <h3>رفع وثيقة جديدة</h3>
            <button class="workedia-modal-close" onclick="document.getElementById('upload-doc-modal').style.display='none'">&times;</button>
        </div>
        <form id="upload-doc-form" style="padding: 20px;">
            <div class="workedia-form-group">
                <label class="workedia-label">عنوان الوثيقة:</label>
                <input type="text" name="title" class="workedia-input" required placeholder="مثال: صورة الهوية الشخصية">
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">الفئة:</label>
                <select name="category" class="workedia-select">
                    <option value="عام">عام</option>
                    <option value="هوية">وثائق هوية</option>
                    <option value="مالي">سجلات مالية</option>
                    <option value="عقود">عقود واتفاقيات</option>
                </select>
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">الملف (Images, PDF, Word):</label>
                <input type="file" name="doc_file" class="workedia-input" required accept=".pdf,.doc,.docx,image/*">
            </div>
            <button type="submit" class="workedia-btn" style="width: 100%; margin-top: 10px; height: 45px; font-weight: 800;">بدء الرفع</button>
        </form>
    </div>
</div>

<script>
function workediaOpenUploadModal() {
    document.getElementById('upload-doc-form').reset();
    document.getElementById('upload-doc-modal').style.display = 'flex';
}

function workediaRefreshDocs() {
    const search = document.getElementById('docs-search').value;
    const cat = document.getElementById('docs-filter-cat').value;
    fetch(ajaxurl + `?action=workedia_get_docs_list_ajax&search=${encodeURIComponent(search)}&category=${encodeURIComponent(cat)}`)
    .then(r => r.text())
    .then(html => {
        document.getElementById('workedia-docs-list').innerHTML = html;
    });
}

let docsSearchTimeout;
function workediaSearchDocs(val) {
    clearTimeout(docsSearchTimeout);
    docsSearchTimeout = setTimeout(workediaRefreshDocs, 300);
}

document.getElementById('upload-doc-form').onsubmit = function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerText = 'جاري الرفع...';

    const fd = new FormData(this);
    fd.append('action', 'workedia_upload_doc');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_docs_action"); ?>');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        btn.disabled = false;
        btn.innerText = 'بدء الرفع';
        if (res.success) {
            workediaShowNotification('تم رفع الوثيقة بنجاح');
            document.getElementById('upload-doc-modal').style.display = 'none';
            workediaRefreshDocs();
        } else alert(res.data);
    });
};

function workediaDeleteDoc(id) {
    if (!confirm('هل أنت متأكد من حذف هذه الوثيقة نهائياً؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_doc');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_docs_action"); ?>');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حذف الوثيقة');
            workediaRefreshDocs();
        }
    });
}
</script>

<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container notebook-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);"><span class="dashicons dashicons-edit"></span> دفتر الملاحظات (Notebook)</h2>
        <button onclick="workediaOpenNoteModal()" class="workedia-btn" style="width: auto;">+ ملاحظة جديدة</button>
    </div>

    <div id="workedia-notebook-grid" class="notebook-grid">
        <?php
        $notes = Workedia_Notebook::get_notes(get_current_user_id());
        include WORKEDIA_PLUGIN_DIR . 'templates/app-notebook-grid.php';
        ?>
    </div>
</div>

<!-- Share Modal -->
<div id="workedia-share-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 400px;">
        <div class="workedia-modal-header">
            <h3>مشاركة الملاحظة</h3>
            <button class="workedia-modal-close" onclick="document.getElementById('workedia-share-modal').style.display='none'">&times;</button>
        </div>
        <div style="padding: 20px;">
            <p style="font-size: 13px; color: #64748b; margin-bottom: 15px;">اختر العضو الذي ترغب في مشاركة هذه الملاحظة معه:</p>
            <input type="hidden" id="share-note-id">
            <select id="share-user-id" class="workedia-select">
                <option value="">-- اختر عضواً --</option>
                <?php
                $staff = Workedia_DB::get_staff(['number' => -1]);
                foreach ($staff as $s) {
                    if ($s->ID != get_current_user_id()) {
                        echo '<option value="' . $s->ID . '">' . esc_html($s->display_name) . '</option>';
                    }
                }
                ?>
            </select>
            <button onclick="workediaShareNote()" class="workedia-btn" style="width: 100%; margin-top: 15px;">تأكيد المشاركة</button>
        </div>
    </div>
</div>

<!-- Note Modal -->
<div id="workedia-note-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 500px;">
        <div class="workedia-modal-header">
            <h3><span id="note-modal-title">إضافة ملاحظة</span></h3>
            <button class="workedia-modal-close" onclick="document.getElementById('workedia-note-modal').style.display='none'">&times;</button>
        </div>
        <form id="workedia-note-form" style="padding: 20px;">
            <input type="hidden" name="id" id="note-id">
            <div class="workedia-form-group">
                <label class="workedia-label">عنوان الملاحظة:</label>
                <input type="text" name="title" class="workedia-input" required>
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">المحتوى:</label>
                <textarea name="content" class="workedia-textarea" rows="5" required></textarea>
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">صورة الملاحظة (URL):</label>
                <div style="display:flex; gap:10px;">
                    <input type="text" name="image_url" id="note_image_url" class="workedia-input">
                    <button type="button" onclick="workediaOpenMediaUploader('note_image_url')" class="workedia-btn" style="width:auto; font-size:12px; background:#4a5568;">رفع</button>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div class="workedia-form-group">
                    <label class="workedia-label">اللون:</label>
                    <input type="color" name="color" value="#ffffff" class="workedia-input" style="height: 40px; padding: 5px;">
                </div>
                <div class="workedia-form-group">
                    <label class="workedia-label">الوسوم (مفصولة بفواصل):</label>
                    <input type="text" name="tags" class="workedia-input" placeholder="عمل, فكرة, عاجل">
                </div>
            </div>
            <button type="submit" class="workedia-btn" style="width: 100%; margin-top: 10px;">حفظ الملاحظة</button>
        </form>
    </div>
</div>

<script>
function workediaOpenNoteModal() {
    document.getElementById('workedia-note-form').reset();
    document.getElementById('note-id').value = '';
    document.getElementById('note-modal-title').innerText = 'إضافة ملاحظة جديدة';
    document.getElementById('workedia-note-modal').style.display = 'flex';
}

function workediaEditNote(note) {
    const f = document.getElementById('workedia-note-form');
    document.getElementById('note-id').value = note.id;
    f.title.value = note.title;
    f.content.value = note.content;
    f.color.value = note.color;
    f.tags.value = note.tags;
    f.image_url.value = note.image_url || '';
    document.getElementById('note-modal-title').innerText = 'تعديل الملاحظة';
    document.getElementById('workedia-note-modal').style.display = 'flex';
}

function workediaRefreshNotebook() {
    fetch(ajaxurl + '?action=workedia_get_notebook_grid_ajax')
    .then(r => r.text())
    .then(html => {
        document.getElementById('workedia-notebook-grid').innerHTML = html;
    });
}

function workediaDeleteNote(id) {
    if (!confirm('هل أنت متأكد من حذف هذه الملاحظة؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_note');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_notebook_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حذف الملاحظة');
            workediaRefreshNotebook();
        }
    });
}

function workediaOpenShareModal(noteId) {
    document.getElementById('share-note-id').value = noteId;
    document.getElementById('workedia-share-modal').style.display = 'flex';
}

function workediaShareNote() {
    const noteId = document.getElementById('share-note-id').value;
    const userId = document.getElementById('share-user-id').value;
    if (!userId) return;

    const fd = new FormData();
    fd.append('action', 'workedia_share_note');
    fd.append('note_id', noteId);
    fd.append('user_id', userId);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_notebook_action"); ?>');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تمت مشاركة الملاحظة بنجاح');
            document.getElementById('workedia-share-modal').style.display = 'none';
        } else alert(res.data);
    });
}

document.getElementById('workedia-note-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', 'workedia_save_note');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_notebook_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حفظ الملاحظة');
            document.getElementById('workedia-note-modal').style.display = 'none';
            workediaRefreshNotebook();
        } else alert(res.data);
    });
});
</script>

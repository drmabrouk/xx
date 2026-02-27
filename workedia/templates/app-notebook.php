<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container notebook-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">دفتر الملاحظات</h2>
        <div class="notebook-search-wrapper" style="position: relative; width: 300px;">
            <input type="text" id="notebook-search" class="workedia-input" placeholder="بحث في الملاحظات أو الوسوم..." oninput="workediaSearchNotes(this.value)" style="padding-left: 35px; border-radius: 50px; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.02);">
            <span class="dashicons dashicons-search" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></span>
        </div>
    </div>

    <!-- Sophisticated Quick Note Creation -->
    <div class="quick-note-creator" style="background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 40px; border: 1px solid #f1f5f9; overflow: hidden; transition: 0.3s;" id="note-creator-box">
        <div id="note-collapsed-state" onclick="workediaExpandNoteCreator()" style="padding: 15px 25px; cursor: pointer; color: #94a3b8; font-weight: 600; display: flex; align-items: center; justify-content: space-between;">
            <span>ابدأ بتدوين فكرة جديدة هنا...</span>
            <span class="dashicons dashicons-plus-alt" style="color: var(--workedia-primary-color);"></span>
        </div>
        <div id="note-expanded-state" style="display: none; padding: 25px;">
            <form id="workedia-quick-note-form">
                <input type="text" name="title" class="workedia-input" placeholder="عنوان الملاحظة" style="border: none; font-size: 1.2em; font-weight: 800; padding: 0 0 15px 0; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; border-radius: 0;">
                <textarea name="content" class="workedia-textarea" rows="4" placeholder="اكتب محتوى ملاحظتك هنا بالتفصيل..." style="border: none; padding: 0; resize: none; font-size: 1.05em; line-height: 1.6;"></textarea>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 25px; padding-top: 20px; border-top: 1px solid #f1f5f9;">
                    <div style="display: flex; gap: 15px; align-items: center;">
                        <div class="color-picker-simple" style="display: flex; gap: 8px;">
                            <label class="color-dot" title="أبيض" style="background: #ffffff; width: 28px; height: 28px; border-radius: 50%; border: 2px solid #e2e8f0; cursor: pointer; display: block; position: relative;"><input type="radio" name="color" value="#ffffff" checked style="display:none;"></label>
                            <label class="color-dot" title="وردي باستيل" style="background: #fff5f5; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: block; position: relative; border: 1px solid #fee2e2;"><input type="radio" name="color" value="#fff5f5" style="display:none;"></label>
                            <label class="color-dot" title="أخضر باستيل" style="background: #f0fff4; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: block; position: relative; border: 1px solid #dcfce7;"><input type="radio" name="color" value="#f0fff4" style="display:none;"></label>
                            <label class="color-dot" title="أصفر باستيل" style="background: #fffaf0; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: block; position: relative; border: 1px solid #fef3c7;"><input type="radio" name="color" value="#fffaf0" style="display:none;"></label>
                            <label class="color-dot" title="أزرق باستيل" style="background: #ebf8ff; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: block; position: relative; border: 1px solid #dbeafe;"><input type="radio" name="color" value="#ebf8ff" style="display:none;"></label>
                            <label class="color-dot" title="بنفسجي باستيل" style="background: #f5f3ff; width: 28px; height: 28px; border-radius: 50%; cursor: pointer; display: block; position: relative; border: 1px solid #ede9fe;"><input type="radio" name="color" value="#f5f3ff" style="display:none;"></label>
                        </div>
                        <div style="width: 1px; height: 20px; background: #eee;"></div>
                        <button type="button" onclick="workediaOpenMediaUploader('note_quick_img')" class="auth-btn-link" style="color: #000; text-decoration: none; font-size: 20px; padding: 0; display: flex; align-items: center;" title="إدراج صورة"><span class="dashicons dashicons-format-image" style="font-size: 24px; width: 24px; height: 24px;"></span></button>
                        <input type="hidden" name="image_url" id="note_quick_img">
                    </div>
                    <div style="display: flex; gap: 10px;">
                        <button type="button" onclick="workediaCollapseNoteCreator()" class="workedia-btn workedia-btn-outline" style="width: auto; background: transparent;">إلغاء</button>
                        <button type="submit" class="workedia-btn" style="width: auto; padding: 0 30px;">حفظ الملاحظة</button>
                    </div>
                </div>
            </form>
        </div>
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
function workediaExpandNoteCreator() {
    document.getElementById('note-collapsed-state').style.display = 'none';
    document.getElementById('note-expanded-state').style.display = 'block';
    document.getElementById('note-creator-box').style.boxShadow = '0 20px 40px rgba(0,0,0,0.1)';
    document.querySelector('#workedia-quick-note-form [name="title"]').focus();
}

function workediaCollapseNoteCreator() {
    document.getElementById('note-collapsed-state').style.display = 'flex';
    document.getElementById('note-expanded-state').style.display = 'none';
    document.getElementById('note-creator-box').style.boxShadow = '0 10px 30px rgba(0,0,0,0.05)';
}

document.getElementById('workedia-quick-note-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', 'workedia_save_note');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_notebook_action"); ?>');

    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerText = 'جاري الحفظ...';

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        btn.disabled = false;
        btn.innerText = 'حفظ الملاحظة';
        if (res.success) {
            workediaShowNotification('تم إضافة الملاحظة بنجاح');
            this.reset();
            workediaCollapseNoteCreator();
            workediaRefreshNotebook();
        } else alert(res.data);
    });
});

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

function workediaRefreshNotebook(search = '') {
    fetch(ajaxurl + `?action=workedia_get_notebook_grid_ajax&search=${encodeURIComponent(search)}`)
    .then(r => r.text())
    .then(html => {
        document.getElementById('workedia-notebook-grid').innerHTML = html;
    });
}

let searchTimeout;
function workediaSearchNotes(val) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        workediaRefreshNotebook(val);
    }, 300);
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

<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container notebook-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);"><span class="dashicons dashicons-edit"></span> دفتر الملاحظات (Notebook)</h2>
        <button onclick="workediaOpenNoteModal()" class="workedia-btn" style="width: auto;">+ ملاحظة جديدة</button>
    </div>

    <div class="notebook-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px;">
        <?php
        $notes = Workedia_Notebook::get_notes(get_current_user_id());
        if (empty($notes)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: #f8fafc; border: 2px dashed #cbd5e0; border-radius: 12px; color: #718096;">
                <span class="dashicons dashicons-sticky" style="font-size: 48px; width: 48px; height: 48px; opacity: 0.5;"></span>
                <p>لا توجد ملاحظات حالياً. ابدأ بتدوين أفكارك الآن!</p>
            </div>
        <?php else: foreach ($notes as $note): ?>
            <div class="note-sticky" style="background: <?php echo esc_attr($note->color); ?>; padding: 20px; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); position: relative; border: 1px solid rgba(0,0,0,0.05);">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span style="font-size: 11px; font-weight: 700; color: rgba(0,0,0,0.4);"><?php echo date('Y-m-d', strtotime($note->updated_at)); ?></span>
                    <div class="note-actions">
                        <button onclick='workediaEditNote(<?php echo json_encode($note); ?>)' style="background: none; border: none; cursor: pointer; color: rgba(0,0,0,0.5);"><span class="dashicons dashicons-edit" style="font-size: 16px;"></span></button>
                        <button onclick="workediaDeleteNote(<?php echo $note->id; ?>)" style="background: none; border: none; cursor: pointer; color: #e53e3e;"><span class="dashicons dashicons-trash" style="font-size: 16px;"></span></button>
                    </div>
                </div>
                <?php if ($note->image_url): ?>
                    <img src="<?php echo esc_url($note->image_url); ?>" style="width: 100%; border-radius: 8px; margin-bottom: 10px; max-height: 150px; object-fit: cover;">
                <?php endif; ?>
                <h3 style="margin: 0 0 10px 0; font-size: 16px; font-weight: 800;"><?php echo esc_html($note->title); ?></h3>
                <div style="font-size: 14px; line-height: 1.6; margin-bottom: 15px;"><?php echo wp_kses_post($note->content); ?></div>
                <?php if ($note->tags): ?>
                    <div style="display: flex; flex-wrap: wrap; gap: 5px;">
                        <?php foreach (explode(',', $note->tags) as $tag): ?>
                            <span style="background: rgba(0,0,0,0.05); padding: 2px 8px; border-radius: 4px; font-size: 10px; font-weight: 600;"><?php echo esc_html(trim($tag)); ?></span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; endif; ?>
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

function workediaDeleteNote(id) {
    if (!confirm('هل أنت متأكد من حذف هذه الملاحظة؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_note');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_notebook_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
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
            location.reload();
        } else alert(res.data);
    });
});
</script>

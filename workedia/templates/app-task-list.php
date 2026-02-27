<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container tasklist-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);"><span class="dashicons dashicons-editor-ul"></span> مدير المهام (Task List)</h2>
        <div style="display: flex; gap: 10px;">
            <button onclick="workediaSyncGoogle()" class="workedia-btn workedia-btn-outline" style="width: auto; border-color: #4285F4; color: #4285F4 !important;"><span class="dashicons dashicons-google"></span> مزامنة Google</button>
            <button onclick="workediaOpenTaskModal()" class="workedia-btn" style="width: auto;">+ مهمة جديدة</button>
        </div>
    </div>

    <div class="task-list-container" style="background: white; border: 1px solid var(--workedia-border-color); border-radius: 12px; overflow: hidden;">
        <?php
        $tasks = Workedia_TaskList::get_tasks(get_current_user_id());
        if (empty($tasks)): ?>
            <div style="text-align: center; padding: 50px; color: #94a3b8;">
                <span class="dashicons dashicons-saved" style="font-size: 48px; width: 48px; height: 48px; opacity: 0.3;"></span>
                <p>لا توجد مهام حالياً. استمتع بيومك!</p>
            </div>
        <?php else: foreach ($tasks as $task): ?>
            <div class="task-item" style="padding: 20px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 15px; align-items: flex-start; transition: 0.2s;">
                <div class="task-checkbox" style="padding-top: 5px;">
                    <input type="checkbox" <?php echo $task->status === 'completed' ? 'checked' : ''; ?> onchange="workediaToggleTask(<?php echo $task->id; ?>, this.checked)" style="width: 20px; height: 20px; cursor: pointer;">
                </div>
                <div style="flex: 1;">
                    <div style="display: flex; justify-content: space-between;">
                        <h4 style="margin: 0; font-weight: 700; color: <?php echo $task->status === 'completed' ? '#94a3b8; text-decoration: line-through;' : 'var(--workedia-dark-color)'; ?>;"><?php echo esc_html($task->title); ?></h4>
                        <div class="task-meta" style="font-size: 11px; display: flex; gap: 15px; align-items: center;">
                            <?php if ($task->deadline): ?>
                                <span style="color: <?php echo (strtotime($task->deadline) < time() && $task->status !== 'completed') ? '#e53e3e' : '#718096'; ?>; font-weight: 700;">
                                    <span class="dashicons dashicons-calendar-alt" style="font-size: 14px;"></span> <?php echo date('j M', strtotime($task->deadline)); ?>
                                </span>
                            <?php endif; ?>
                            <button onclick='workediaEditTask(<?php echo json_encode($task); ?>)' style="background:none; border:none; cursor:pointer; color:#94a3b8;"><span class="dashicons dashicons-edit" style="font-size:14px;"></span></button>
                            <button onclick="workediaDeleteTask(<?php echo $task->id; ?>)" style="background:none; border:none; cursor:pointer; color:#feb2b2;"><span class="dashicons dashicons-trash" style="font-size:14px;"></span></button>
                        </div>
                    </div>
                    <?php if ($task->description): ?>
                        <p style="margin: 5px 0 0 0; font-size: 13px; color: #64748b;"><?php echo esc_html($task->description); ?></p>
                    <?php endif; ?>

                    <div class="subtasks-area" style="margin-top: 15px; padding-right: 20px; border-right: 2px solid #f1f5f9;">
                        <?php
                        $subtasks = Workedia_TaskList::get_subtasks($task->id);
                        foreach ($subtasks as $sub): ?>
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px; font-size: 12px;">
                                <input type="checkbox" <?php echo $sub->is_completed ? 'checked' : ''; ?> onchange="workediaToggleSubtask(<?php echo $sub->id; ?>, this.checked)">
                                <span style="<?php echo $sub->is_completed ? 'color:#94a3b8; text-decoration:line-through;' : ''; ?>"><?php echo esc_html($sub->title); ?></span>
                            </div>
                        <?php endforeach; ?>
                        <div style="margin-top: 8px;">
                            <input type="text" placeholder="+ إضافة خطوة فرعية..." style="border: none; background: #f8fafc; padding: 5px 10px; border-radius: 4px; font-size: 11px; width: 200px;" onkeypress="if(event.key === 'Enter') workediaAddSubtask(<?php echo $task->id; ?>, this.value)">
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<!-- Task Modal -->
<div id="workedia-task-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 500px;">
        <div class="workedia-modal-header">
            <h3><span id="task-modal-title">إضافة مهمة جديدة</span></h3>
            <button class="workedia-modal-close" onclick="document.getElementById('workedia-task-modal').style.display='none'">&times;</button>
        </div>
        <form id="workedia-task-form" style="padding: 20px;">
            <input type="hidden" name="id" id="task-id">
            <div class="workedia-form-group">
                <label class="workedia-label">عنوان المهمة:</label>
                <input type="text" name="title" class="workedia-input" required>
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">الوصف التفصيلي:</label>
                <textarea name="description" class="workedia-textarea" rows="3"></textarea>
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">تاريخ الاستحقاق (Deadline):</label>
                <input type="datetime-local" name="deadline" class="workedia-input">
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">تنبيه (Reminder):</label>
                <input type="datetime-local" name="reminder_at" class="workedia-input">
            </div>
            <button type="submit" class="workedia-btn" style="width: 100%; margin-top: 10px;">حفظ المهمة</button>
        </form>
    </div>
</div>

<script>
function workediaOpenTaskModal() {
    document.getElementById('workedia-task-form').reset();
    document.getElementById('task-id').value = '';
    document.getElementById('task-modal-title').innerText = 'إضافة مهمة جديدة';
    document.getElementById('workedia-task-modal').style.display = 'flex';
}

function workediaEditTask(task) {
    const f = document.getElementById('workedia-task-form');
    document.getElementById('task-id').value = task.id;
    f.title.value = task.title;
    f.description.value = task.description;
    if (task.deadline) f.deadline.value = task.deadline.replace(' ', 'T').substring(0, 16);
    if (task.reminder_at) f.reminder_at.value = task.reminder_at.replace(' ', 'T').substring(0, 16);
    document.getElementById('task-modal-title').innerText = 'تعديل المهمة';
    document.getElementById('workedia-task-modal').style.display = 'flex';
}

function workediaToggleTask(id, completed) {
    const fd = new FormData();
    fd.append('action', 'workedia_toggle_task');
    fd.append('id', id);
    fd.append('status', completed ? 'completed' : 'pending');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}

function workediaDeleteTask(id) {
    if (!confirm('هل أنت متأكد من حذف هذه المهمة؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_task');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}

function workediaAddSubtask(taskId, title) {
    if (!title) return;
    const fd = new FormData();
    fd.append('action', 'workedia_add_subtask');
    fd.append('task_id', taskId);
    fd.append('title', title);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}

function workediaToggleSubtask(id, completed) {
    const fd = new FormData();
    fd.append('action', 'workedia_toggle_subtask');
    fd.append('id', id);
    fd.append('is_completed', completed ? 1 : 0);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (!res.success) alert(res.data);
    });
}

function workediaSyncGoogle() {
    alert('سيتم توجيهك الآن لربط حساب Google وتفعيل مزامنة التقويم والمهام اليومية.');
}

document.getElementById('workedia-task-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', 'workedia_save_task');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حفظ المهمة بنجاح');
            location.reload();
        } else alert(res.data);
    });
});
</script>

<style>
.task-item:hover { background: #fcfcfc; }
.task-checkbox input[type="checkbox"] {
    accent-color: var(--workedia-primary-color);
}
</style>

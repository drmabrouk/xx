<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container tasklist-app">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">مدير المهام</h2>
        <div style="display: flex; gap: 10px;">
            <button onclick="workediaSyncGoogle()" class="workedia-btn workedia-btn-outline" style="width: auto; border-color: #4285F4; color: #4285F4 !important; background:white;"><span class="dashicons dashicons-google"></span> مزامنة Google</button>
        </div>
    </div>

    <!-- Sophisticated Task Creator -->
    <div class="quick-task-creator" style="background: #fff; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); margin-bottom: 40px; border: 1px solid #f1f5f9; padding: 15px; transition: 0.3s;">
        <form id="workedia-quick-task-form" style="display: flex; gap: 15px; align-items: center;">
            <div style="flex: 1; position: relative;">
                <input type="text" name="title" class="workedia-input" placeholder="ما هي المهمة التالية؟" style="border: none; padding: 10px 0; font-size: 1.1em; font-weight: 600; border-radius: 0;" required>
                <input type="hidden" name="task_date" value="<?php echo date('Y-m-d H:i:s'); ?>">
                <div class="task-creation-options" style="display: flex; gap: 20px; margin-top: 10px;">
                    <div style="display:flex; align-items:center; gap:5px; color: #64748b; font-size: 12px;" title="تاريخ المهمة">
                        <span class="dashicons dashicons-clock" style="font-size:16px;"></span>
                        <span><?php echo date('Y-m-d H:i'); ?></span>
                    </div>
                    <div style="display:flex; align-items:center; gap:5px; color: #64748b; font-size: 12px;" title="تاريخ الاستحقاق">
                        <span class="dashicons dashicons-calendar-alt" style="font-size:16px;"></span>
                        <input type="datetime-local" name="deadline" id="quick-task-deadline" value="<?php echo date('Y-m-d\TH:i'); ?>" style="border:none; color:inherit; font-size:inherit; padding:0; cursor:pointer; background:transparent;">
                    </div>
                    <div style="display:flex; align-items:center; gap:5px; color: #64748b; font-size: 12px;" title="تنبيه">
                        <span class="dashicons dashicons-bell" style="font-size:16px;"></span>
                        <input type="datetime-local" name="reminder_at" id="quick-task-reminder" style="border:none; color:inherit; font-size:inherit; padding:0; cursor:pointer; background:transparent;">
                    </div>
                </div>
            </div>
            <button type="submit" class="workedia-btn" style="width: 50px; height: 50px; border-radius: 12px; padding: 0;">
                <span class="dashicons dashicons-plus" style="font-size: 24px; width:24px; height:24px;"></span>
            </button>
        </form>
    </div>

    <div id="workedia-tasklist-items" class="task-list-container" style="background: white; border: 1px solid var(--workedia-border-color); border-radius: 16px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.03);">
        <?php
        $tasks = Workedia_TaskList::get_tasks(get_current_user_id());
        include WORKEDIA_PLUGIN_DIR . 'templates/app-task-list-items.php';
        ?>
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

function workediaInitTaskSorting() {
    const el = document.getElementById('workedia-tasklist-items');
    if (!el) return;
    Sortable.create(el, {
        animation: 150,
        handle: '.task-drag-handle',
        ghostClass: 'task-ghost',
        onEnd: function() {
            const ids = [];
            document.querySelectorAll('.task-item').forEach(item => {
                ids.push(item.getAttribute('data-id'));
            });
            const fd = new FormData();
            fd.append('action', 'workedia_update_task_order');
            fd.append('ids', ids.join(','));
            fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
            fetch(ajaxurl, { method: 'POST', body: fd });
        }
    });
}

function workediaRefreshTaskList() {
    fetch(ajaxurl + '?action=workedia_get_tasklist_items_ajax')
    .then(r => r.text())
    .then(html => {
        document.getElementById('workedia-tasklist-items').innerHTML = html;
        workediaInitTaskSorting();
        workediaCheckDueTasks();
    });
}

function workediaCheckDueTasks() {
    const now = new Date();
    document.querySelectorAll('.task-item').forEach(item => {
        const deadline = item.getAttribute('data-deadline');
        const status = item.getAttribute('data-status');
        const title = item.querySelector('h4').innerText;

        if (deadline && status !== 'completed') {
            const dueDate = new Date(deadline);
            const diff = dueDate - now;

            // If due in next 5 minutes and not notified
            if (diff > 0 && diff < 300000 && !item.hasAttribute('data-notified')) {
                workediaShowNotification(`مهمة عاجلة: ${title}`, true);
                item.setAttribute('data-notified', 'true');
            }
        }
    });
}
setInterval(workediaCheckDueTasks, 60000);

window.addEventListener('load', workediaInitTaskSorting);

function workediaToggleTask(id, completed) {
    const fd = new FormData();
    fd.append('action', 'workedia_toggle_task');
    fd.append('id', id);
    fd.append('status', completed ? 'completed' : 'pending');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            if (completed) workediaShowNotification('تم إنجاز المهمة بنجاح! أحسنت عملًا.');
            workediaRefreshTaskList();
        }
    });
}

function workediaDeleteTask(id) {
    if (!confirm('هل أنت متأكد من حذف هذه المهمة؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_task');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) workediaRefreshTaskList();
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
        if (res.success) workediaRefreshTaskList();
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

document.getElementById('workedia-quick-task-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const deadline = document.getElementById('quick-task-deadline').value;
    const reminder = document.getElementById('quick-task-reminder').value;

    if (reminder && new Date(reminder) < new Date()) {
        alert('تنبيه: لا يمكن تعيين وقت التذكير في الماضي.');
        return;
    }

    const fd = new FormData(this);
    fd.append('action', 'workedia_save_task');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');

    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        btn.disabled = false;
        if (res.success) {
            workediaShowNotification('تم إضافة المهمة بنجاح');
            this.reset();
            workediaRefreshTaskList();
        } else alert(res.data);
    });
});

document.getElementById('workedia-task-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const deadline = this.elements['deadline'].value;
    const reminder = this.elements['reminder_at'].value;
    if (reminder && new Date(reminder) < new Date()) {
        alert('تنبيه: لا يمكن تعيين وقت التذكير في الماضي.');
        return;
    }
    const fd = new FormData(this);
    fd.append('action', 'workedia_save_task');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_tasklist_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حفظ المهمة بنجاح');
            document.getElementById('workedia-task-modal').style.display = 'none';
            workediaRefreshTaskList();
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

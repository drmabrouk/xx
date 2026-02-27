<?php if (!defined('ABSPATH')) exit; ?>
<?php if (empty($tasks)): ?>
    <div style="text-align: center; padding: 50px; color: #94a3b8;">
        <span class="dashicons dashicons-saved" style="font-size: 48px; width: 48px; height: 48px; opacity: 0.3;"></span>
        <p>لا توجد مهام حالياً. استمتع بيومك!</p>
    </div>
<?php else: foreach ($tasks as $task):
    $is_overdue = (strtotime($task->deadline) < time() && $task->status !== 'completed');
?>
    <div class="task-item" style="padding: 20px; border-bottom: 1px solid #f1f5f9; display: flex; gap: 15px; align-items: flex-start; transition: 0.2s; background: <?php echo $is_overdue ? '#fff5f5' : 'transparent'; ?>;">
        <div class="task-checkbox" style="padding-top: 5px;">
            <input type="checkbox" <?php echo $task->status === 'completed' ? 'checked' : ''; ?> onchange="workediaToggleTask(<?php echo $task->id; ?>, this.checked)" style="width: 20px; height: 20px; cursor: pointer;">
        </div>
        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between;">
                <h4 style="margin: 0; font-weight: 700; color: <?php echo $task->status === 'completed' ? '#94a3b8; text-decoration: line-through;' : 'var(--workedia-dark-color)'; ?>;">
                    <?php echo esc_html($task->title); ?>
                    <?php if ($is_overdue): ?>
                        <span class="workedia-badge workedia-badge-urgent" style="margin-right: 10px; font-size: 9px;">متأخر</span>
                    <?php endif; ?>
                </h4>
                <div class="task-meta" style="font-size: 11px; display: flex; gap: 15px; align-items: center;">
                    <?php if ($task->reminder_at): ?>
                        <span title="تنبيه" style="color: #f59e0b;"><span class="dashicons dashicons-bell" style="font-size: 14px;"></span> <?php echo date('j M H:i', strtotime($task->reminder_at)); ?></span>
                    <?php endif; ?>
                    <?php if ($task->deadline): ?>
                        <span style="color: <?php echo $is_overdue ? '#e53e3e' : '#718096'; ?>; font-weight: 700;">
                            <span class="dashicons dashicons-calendar-alt" style="font-size: 14px;"></span> <?php echo date('j M', strtotime($task->deadline)); ?>
                        </span>
                    <?php endif; ?>
                    <button onclick='workediaEditTask(<?php echo esc_attr(json_encode($task)); ?>)' style="background:none; border:none; cursor:pointer; color:#94a3b8;"><span class="dashicons dashicons-edit" style="font-size:14px;"></span></button>
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
                    <input type="text" placeholder="+ إضافة خطوة فرعية..." style="border: 1px solid #eee; background: #f8fafc; padding: 5px 10px; border-radius: 4px; font-size: 11px; width: 200px;" onkeypress="if(event.key === 'Enter') { workediaAddSubtask(<?php echo $task->id; ?>, this.value); this.value=''; }">
                </div>
            </div>
        </div>
    </div>
<?php endforeach; endif; ?>

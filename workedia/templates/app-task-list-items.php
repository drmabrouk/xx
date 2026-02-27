<?php if (!defined('ABSPATH')) exit; ?>
<?php if (empty($tasks)): ?>
    <div style="text-align: center; padding: 60px 20px; color: #94a3b8; background: white; border-radius: 16px;">
        <div style="background: #f1f5f9; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <span class="dashicons dashicons-saved" style="font-size: 40px; width: 40px; height: 40px; opacity: 0.5;"></span>
        </div>
        <h3 style="margin: 0; color: #475569;">لا توجد مهام حالياً</h3>
        <p style="margin: 10px 0 0 0; font-size: 14px;">استمتع بيومك الهادئ أو ابدأ بإضافة مهام جديدة!</p>
    </div>
<?php else: foreach ($tasks as $task):
    $is_overdue = (strtotime($task->deadline) < time() && $task->status !== 'completed');
    $status_color = $task->status === 'completed' ? '#38a169' : ($is_overdue ? '#e53e3e' : '#3182CE');
    $status_bg = $task->status === 'completed' ? '#f0fff4' : ($is_overdue ? '#fff5f5' : '#ebf8ff');
    $status_label = $task->status === 'completed' ? 'مكتملة' : ($is_overdue ? 'متأخرة' : 'قيد التنفيذ');
?>
    <div class="task-card-modern" data-id="<?php echo $task->id; ?>" data-deadline="<?php echo $task->deadline; ?>" data-status="<?php echo $task->status; ?>" style="background: #white; border-radius: 16px; border: 1px solid #f1f5f9; padding: 25px; margin-bottom: 20px; display: flex; gap: 20px; align-items: flex-start; transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1); box-shadow: 0 4px 6px rgba(0,0,0,0.02); position: relative; overflow: hidden;">
        <?php if ($is_overdue): ?>
            <div style="position: absolute; top: 0; right: 0; width: 4px; height: 100%; background: #e53e3e;"></div>
        <?php endif; ?>

        <div class="task-drag-handle" style="cursor: grab; color: #cbd5e0; padding-top: 5px;">
            <span class="dashicons dashicons-menu"></span>
        </div>

        <div class="task-checkbox" style="padding-top: 5px;">
            <input type="checkbox" <?php echo $task->status === 'completed' ? 'checked' : ''; ?> onchange="workediaToggleTask(<?php echo $task->id; ?>, this.checked)" style="width: 22px; height: 22px; cursor: pointer; accent-color: var(--workedia-primary-color);">
        </div>

        <div style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px;">
                <div>
                    <h4 style="margin: 0; font-size: 17px; font-weight: 800; color: <?php echo $task->status === 'completed' ? '#94a3b8; text-decoration: line-through;' : 'var(--workedia-dark-color)'; ?>;">
                        <?php echo esc_html($task->title); ?>
                    </h4>
                    <div style="display: flex; gap: 10px; margin-top: 8px;">
                        <span class="workedia-badge" style="background: <?php echo $status_bg; ?>; color: <?php echo $status_color; ?>; font-size: 10px; padding: 4px 10px;"><?php echo $status_label; ?></span>
                        <?php if ($task->deadline): ?>
                            <span class="workedia-badge" style="background: #f8fafc; color: #64748b; font-size: 10px; padding: 4px 10px; display: flex; align-items: center; gap: 4px;">
                                <span class="dashicons dashicons-calendar-alt" style="font-size: 12px; width: 12px; height: 12px;"></span>
                                <?php echo date_i18n('j M', strtotime($task->deadline)); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($task->reminder_at): ?>
                            <span class="workedia-badge" style="background: #fffaf0; color: #d69e2e; font-size: 10px; padding: 4px 10px; display: flex; align-items: center; gap: 4px;">
                                <span class="dashicons dashicons-bell" style="font-size: 12px; width: 12px; height: 12px;"></span>
                                <?php echo date_i18n('H:i', strtotime($task->reminder_at)); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="task-actions" style="display: flex; gap: 5px;">
                    <button onclick='workediaEditTask(<?php echo esc_attr(json_encode($task)); ?>)' class="workedia-btn workedia-btn-outline" style="width: 32px; height: 32px; padding: 0; border-radius: 50%; border: none; background: #f8fafc;"><span class="dashicons dashicons-edit" style="font-size: 16px; width: 16px; height: 16px; color: #94a3b8;"></span></button>
                    <button onclick="workediaDeleteTask(<?php echo $task->id; ?>)" class="workedia-btn workedia-btn-outline" style="width: 32px; height: 32px; padding: 0; border-radius: 50%; border: none; background: #fff5f5;"><span class="dashicons dashicons-trash" style="font-size: 16px; width: 16px; height: 16px; color: #e53e3e;"></span></button>
                </div>
            </div>

            <?php if ($task->description): ?>
                <p style="margin: 0 0 15px 0; font-size: 14px; line-height: 1.6; color: #64748b;"><?php echo esc_html($task->description); ?></p>
            <?php endif; ?>

            <div class="subtasks-container" style="background: #fcfcfc; border-radius: 12px; padding: 15px; border: 1px solid #f1f5f9;">
                <div id="subtasks-list-<?php echo $task->id; ?>">
                    <?php
                    $subtasks = Workedia_TaskList::get_subtasks($task->id);
                    foreach ($subtasks as $sub): ?>
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            <input type="checkbox" <?php echo $sub->is_completed ? 'checked' : ''; ?> onchange="workediaToggleSubtask(<?php echo $sub->id; ?>, this.checked)" style="width: 16px; height: 16px; accent-color: #94a3b8;">
                            <span style="font-size: 13px; <?php echo $sub->is_completed ? 'color:#94a3b8; text-decoration:line-through;' : 'color: #475569;'; ?>"><?php echo esc_html($sub->title); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="margin-top: 10px; display: flex; gap: 10px;">
                    <input type="text" placeholder="+ إضافة خطوة فرعية للمهمة..." style="flex: 1; border: 1px solid #e2e8f0; background: #fff; padding: 8px 12px; border-radius: 8px; font-size: 12px;" onkeypress="if(event.key === 'Enter') { workediaAddSubtask(<?php echo $task->id; ?>, this.value); this.value=''; }">
                </div>
            </div>
        </div>
    </div>
<?php endforeach; endif; ?>

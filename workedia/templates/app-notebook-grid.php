<?php if (!defined('ABSPATH')) exit; ?>
<?php if (empty($notes)): ?>
    <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: #f8fafc; border: 2px dashed #cbd5e0; border-radius: 12px; color: #718096;">
        <span class="dashicons dashicons-sticky" style="font-size: 48px; width: 48px; height: 48px; opacity: 0.5;"></span>
        <p>لا توجد ملاحظات حالياً. ابدأ بتدوين أفكارك الآن!</p>
    </div>
<?php else: foreach ($notes as $note): ?>
    <div class="note-modern-card" style="background: <?php echo esc_attr($note->color ?: '#ffffff'); ?>; animation: workediaSlideUp 0.3s ease-out;">
        <?php if ($note->image_url): ?>
            <div class="note-card-image" style="background-image: url('<?php echo esc_url($note->image_url); ?>');"></div>
        <?php endif; ?>

        <div class="note-card-body">
            <div class="note-card-header">
                <span class="note-card-date"><?php echo date_i18n('j M Y', strtotime($note->updated_at)); ?></span>
                <div class="note-actions">
                    <?php if ($note->user_id == get_current_user_id()): ?>
                        <button onclick='workediaOpenShareModal(<?php echo $note->id; ?>)' title="مشاركة"><span class="dashicons dashicons-share"></span></button>
                        <button onclick='workediaEditNote(<?php echo esc_attr(json_encode($note)); ?>)' title="تعديل"><span class="dashicons dashicons-edit"></span></button>
                        <button onclick="workediaDeleteNote(<?php echo $note->id; ?>)" class="delete" title="حذف"><span class="dashicons dashicons-trash"></span></button>
                    <?php else: ?>
                        <span class="dashicons dashicons-groups shared-icon" title="ملاحظة مشتركة"></span>
                    <?php endif; ?>
                </div>
            </div>

            <h3 class="note-card-title"><?php echo esc_html($note->title ?: 'بدون عنوان'); ?></h3>
            <div class="note-card-content"><?php echo wp_kses_post($note->content); ?></div>

            <?php if ($note->tags): ?>
                <div class="note-card-tags">
                    <?php foreach (explode(',', $note->tags) as $tag): ?>
                        <span class="note-pill"><span class="dashicons dashicons-tag"></span><?php echo esc_html(trim($tag)); ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; endif; ?>

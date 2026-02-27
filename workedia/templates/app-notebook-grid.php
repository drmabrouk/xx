<?php if (!defined('ABSPATH')) exit; ?>
<?php if (empty($notes)): ?>
    <div style="grid-column: 1/-1; text-align: center; padding: 50px; background: #f8fafc; border: 2px dashed #cbd5e0; border-radius: 12px; color: #718096;">
        <span class="dashicons dashicons-sticky" style="font-size: 48px; width: 48px; height: 48px; opacity: 0.5;"></span>
        <p>لا توجد ملاحظات حالياً. ابدأ بتدوين أفكارك الآن!</p>
    </div>
<?php else: foreach ($notes as $note): ?>
    <div class="note-sticky" style="background: <?php echo esc_attr($note->color); ?>; padding: 20px; border-radius: 12px; box-shadow: var(--workedia-shadow); position: relative; border: 1px solid rgba(0,0,0,0.05); transition: 0.3s; animation: workediaFadeIn 0.3s ease-out;">
        <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
            <span style="font-size: 11px; font-weight: 700; color: rgba(0,0,0,0.4);"><?php echo date_i18n('j M Y', strtotime($note->updated_at)); ?></span>
            <div class="note-actions">
                <?php if ($note->user_id == get_current_user_id()): ?>
                    <button onclick='workediaOpenShareModal(<?php echo $note->id; ?>)' style="background: none; border: none; cursor: pointer; color: rgba(0,0,0,0.5);" title="مشاركة"><span class="dashicons dashicons-share" style="font-size: 16px;"></span></button>
                <button onclick='workediaEditNote(<?php echo esc_attr(json_encode($note)); ?>)' style="background: none; border: none; cursor: pointer; color: rgba(0,0,0,0.5);"><span class="dashicons dashicons-edit" style="font-size: 16px;"></span></button>
                    <button onclick="workediaDeleteNote(<?php echo $note->id; ?>)" style="background: none; border: none; cursor: pointer; color: #e53e3e;"><span class="dashicons dashicons-trash" style="font-size: 16px;"></span></button>
                <?php else: ?>
                    <span class="dashicons dashicons-groups" style="font-size: 16px; color: rgba(0,0,0,0.3);" title="ملاحظة مشتركة"></span>
                <?php endif; ?>
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
                    <span class="note-tag" style="background: rgba(0,0,0,0.06); padding: 3px 10px; border-radius: 50px; font-size: 10px; font-weight: 800; border: 1px solid rgba(0,0,0,0.05);"><span class="dashicons dashicons-tag" style="font-size: 12px; width:12px; height:12px; margin-left:4px;"></span><?php echo esc_html(trim($tag)); ?></span>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; endif; ?>

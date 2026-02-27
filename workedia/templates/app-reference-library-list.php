<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-table-container">
    <table class="workedia-table">
        <thead>
            <tr>
                <th>المؤلف</th>
                <th>العام</th>
                <th>العنوان</th>
                <th>النوع</th>
                <th style="text-align: center;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($references)): ?>
                <tr><td colspan="5" style="text-align:center; padding:50px; color:#94a3b8;">لا توجد مراجع في هذه المكتبة بعد.</td></tr>
            <?php else: foreach ($references as $ref):
                $type_map = ['book' => 'كتاب', 'article' => 'مقال', 'website' => 'موقع', 'thesis' => 'رسالة', 'report' => 'تقرير'];
            ?>
                <tr>
                    <td style="font-weight:700;"><?php echo esc_html($ref->authors); ?></td>
                    <td><?php echo esc_html($ref->year); ?></td>
                    <td style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo esc_html($ref->title); ?></td>
                    <td><span class="workedia-badge workedia-badge-low" style="border-radius:50px;"><?php echo $type_map[$ref->ref_type] ?? $ref->ref_type; ?></span></td>
                    <td style="text-align:center;">
                        <div style="display:flex; gap:10px; justify-content:center;">
                            <button onclick="workediaDeleteRef(<?php echo $ref->id; ?>)" class="workedia-btn" style="width:32px; height:32px; padding:0; background:#e53e3e;"><span class="dashicons dashicons-trash" style="font-size:16px;"></span></button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

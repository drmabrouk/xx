<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-table-container">
    <table class="workedia-table">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>الوزن</th>
                <th>الطول</th>
                <th>المؤشر (BMI)</th>
                <th>الحالة</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($history)): ?>
                <tr><td colspan="6" style="text-align: center; padding: 20px;">لا توجد سجلات مسبقة.</td></tr>
            <?php else: foreach ($history as $h):
                $units = json_decode($h->units, true);
                $w_unit = $units['w'] ?? 'kg';
                $h_unit = $units['h'] ?? 'cm';
            ?>
                <tr>
                    <td><?php echo date('Y-m-d', strtotime($h->created_at)); ?></td>
                    <td><?php echo $h->weight . ' ' . $w_unit; ?></td>
                    <td><?php echo $h->height . ' ' . $h_unit; ?></td>
                    <td><strong><?php echo $h->bmi; ?></strong></td>
                    <td><?php echo esc_html($h->classification); ?></td>
                    <td>
                        <button onclick="workediaDeleteBMI(<?php echo $h->id; ?>)" style="background:none; border:none; color:#e53e3e; cursor:pointer;" title="حذف">
                            <span class="dashicons dashicons-trash"></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

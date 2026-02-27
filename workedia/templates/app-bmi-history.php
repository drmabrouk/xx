<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-table-container" style="box-shadow: none; border: 1px solid #f1f5f9; border-radius: 12px; margin-bottom: 0;">
    <table class="workedia-table" style="font-size: 13px;">
        <thead>
            <tr>
                <th style="padding: 10px 15px; background: #f8fafc; font-size: 11px;">التاريخ</th>
                <th style="padding: 10px 15px; background: #f8fafc; font-size: 11px;">العمر</th>
                <th style="padding: 10px 15px; background: #f8fafc; font-size: 11px;">الوزن</th>
                <th style="padding: 10px 15px; background: #f8fafc; font-size: 11px;">الطول</th>
                <th style="padding: 10px 15px; background: #f8fafc; font-size: 11px;">المؤشر</th>
                <th style="padding: 10px 15px; background: #f8fafc; font-size: 11px;">الحالة</th>
                <th style="padding: 10px 15px; background: #f8fafc; font-size: 11px; text-align: center;"></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($history)): ?>
                <tr><td colspan="7" style="text-align: center; padding: 20px; color: #94a3b8;">لا توجد سجلات مسبقة.</td></tr>
            <?php else: foreach ($history as $h):
                $units = json_decode($h->units, true);
                $age = $units['age'] ?? '-';
                $w_unit = $units['w'] ?? 'kg';
                $h_unit = $units['h'] ?? 'cm';
                $status_color = '#94a3b8';
                if ($h->bmi < 18.5) $status_color = '#3182ce';
                elseif ($h->bmi < 25) $status_color = '#38a169';
                elseif ($h->bmi < 30) $status_color = '#d69e2e';
                else $status_color = '#e53e3e';
            ?>
                <tr>
                    <td style="padding: 10px 15px; color: #94a3b8;"><?php echo date('Y-m-d', strtotime($h->created_at)); ?></td>
                    <td style="padding: 10px 15px; color: #64748b;"><?php echo $age; ?></td>
                    <td style="padding: 10px 15px; font-weight: 600;"><?php echo $h->weight . ' ' . $w_unit; ?></td>
                    <td style="padding: 10px 15px;"><?php echo $h->height . ' ' . $h_unit; ?></td>
                    <td style="padding: 10px 15px;"><strong style="color: <?php echo $status_color; ?>;"><?php echo $h->bmi; ?></strong></td>
                    <td style="padding: 10px 15px;"><span style="font-size: 11px; font-weight: 700; color: <?php echo $status_color; ?>;"><?php echo esc_html($h->classification); ?></span></td>
                    <td style="padding: 10px 15px; text-align: center;">
                        <button onclick="workediaDeleteBMI(<?php echo $h->id; ?>)" style="background:none; border:none; color: #feb2b2; cursor:pointer; padding: 0; transition: 0.2s;" onmouseover="this.style.color='#e53e3e'" onmouseout="this.style.color='#feb2b2'" title="حذف">
                            <span class="dashicons dashicons-trash" style="font-size: 16px;"></span>
                        </button>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

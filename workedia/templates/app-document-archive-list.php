<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-table-container">
    <table class="workedia-table">
        <thead>
            <tr>
                <th>اسم الوثيقة</th>
                <th>الفئة</th>
                <th>النوع</th>
                <th>الحجم</th>
                <th>تاريخ الرفع</th>
                <th style="text-align: center;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($docs)): ?>
                <tr><td colspan="6" style="text-align: center; padding: 50px; color: #94a3b8;">لا توجد وثائق مؤرشفة حالياً.</td></tr>
            <?php else: foreach ($docs as $doc):
                $ext = pathinfo($doc->file_url, PATHINFO_EXTENSION);
                $type_color = '#F1F5F9';
                $type_text = '#475569';
                if ($ext == 'pdf') { $type_color = '#FFF5F5'; $type_text = '#E53E3E'; }
                elseif (in_array($ext, ['doc', 'docx'])) { $type_color = '#EBF8FF'; $type_text = '#3182CE'; }
                elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) { $type_color = '#F0FFF4'; $type_text = '#38A169'; }
            ?>
                <tr>
                    <td><strong><?php echo esc_html($doc->title); ?></strong></td>
                    <td><span style="font-size: 11px; color: #64748b;"><?php echo esc_html($doc->category); ?></span></td>
                    <td><span class="workedia-badge" style="background: <?php echo $type_color; ?>; color: <?php echo $type_text; ?>; border-radius: 50px; text-transform: uppercase;"><?php echo $ext; ?></span></td>
                    <td><span style="font-size: 11px; color: #94a3b8;"><?php echo round($doc->file_size / 1024 / 1024, 2); ?> MB</span></td>
                    <td><span style="font-size: 11px; color: #94a3b8;"><?php echo date('Y-m-d', strtotime($doc->created_at)); ?></span></td>
                    <td style="text-align: center;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="<?php echo esc_url($doc->file_url); ?>" target="_blank" class="workedia-btn" style="width: auto; height: 32px; padding: 0 12px; background: #4a5568;"><span class="dashicons dashicons-visibility" style="font-size: 16px;"></span></a>
                            <button onclick="workediaDeleteDoc(<?php echo $doc->id; ?>)" class="workedia-btn" style="width: auto; height: 32px; padding: 0 12px; background: #e53e3e;"><span class="dashicons dashicons-trash" style="font-size: 16px;"></span></button>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

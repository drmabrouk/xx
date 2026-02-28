<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-table-container">
    <table class="workedia-table">
        <thead>
            <tr>
                <th>رقم الفاتورة</th>
                <th>العميل</th>
                <th>تاريخ الإصدار</th>
                <th>تاريخ الاستحقاق</th>
                <th>الإجمالي</th>
                <th>الحالة</th>
                <th style="text-align: center;">إجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)): ?>
                <tr><td colspan="7" style="text-align: center; padding: 40px; color: #94a3b8;">لا توجد فواتير حالياً.</td></tr>
            <?php else: foreach ($invoices as $inv):
                $status_map = [
                    'draft' => ['label' => 'مسودة', 'color' => '#718096', 'bg' => '#f8fafc'],
                    'pending' => ['label' => 'قيد الانتظار', 'color' => '#d69e2e', 'bg' => '#fffaf0'],
                    'paid' => ['label' => 'مدفوعة', 'color' => '#38a169', 'bg' => '#f0fff4'],
                    'cancelled' => ['label' => 'ملغاة', 'color' => '#e53e3e', 'bg' => '#fff5f5'],
                    'archived' => ['label' => 'مؤرشفة', 'color' => '#4a5568', 'bg' => '#edf2f7']
                ];
                $s = $status_map[$inv->status] ?? $status_map['draft'];
            ?>
                <tr>
                    <td><strong>#<?php echo esc_html($inv->invoice_number); ?></strong></td>
                    <td><?php echo esc_html($inv->client_name); ?></td>
                    <td style="color: #64748b;"><?php echo $inv->issue_date; ?></td>
                    <td style="color: #e53e3e; font-weight: 600;"><?php echo $inv->due_date ?: '-'; ?></td>
                    <td style="font-weight: 800; font-family: monospace; font-size: 15px;"><?php echo number_format($inv->total_amount, 2) . ' ' . $inv->currency; ?></td>
                    <td><span class="workedia-badge" style="background: <?php echo $s['bg']; ?>; color: <?php echo $s['color']; ?>;"><?php echo $s['label']; ?></span></td>
                    <td style="text-align: center;">
                        <div class="workedia-actions-dropdown">
                            <button class="workedia-actions-trigger">...</button>
                            <div class="workedia-actions-content">
                                <a href="javascript:void(0)" onclick="workediaViewInvoice(<?php echo $inv->id; ?>)" class="workedia-action-item"><span class="dashicons dashicons-visibility"></span> عرض وتفاصيل</a>
                                <a href="javascript:void(0)" onclick="workediaOpenInvoiceModal(<?php echo $inv->id; ?>)" class="workedia-action-item"><span class="dashicons dashicons-edit"></span> تعديل</a>
                                <a href="javascript:void(0)" onclick="workediaDuplicateInvoice(<?php echo $inv->id; ?>)" class="workedia-action-item"><span class="dashicons dashicons-admin-page"></span> تكرار</a>
                                <a href="javascript:void(0)" onclick="workediaDeleteInvoice(<?php echo $inv->id; ?>)" class="workedia-action-item" style="color: #e53e3e !important;"><span class="dashicons dashicons-trash"></span> حذف</a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach; endif; ?>
        </tbody>
    </table>
</div>

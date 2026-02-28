<?php
if (!defined('ABSPATH')) exit;

$token = $_GET['token'] ?? '';
global $wpdb;
$invoice = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}workedia_invoices WHERE public_token = %s", $token));

if (!$invoice) {
    echo '<div style="text-align:center; padding:100px; font-family:\'Rubik\', sans-serif;"><h1>عذراً، هذه الفاتورة غير موجودة.</h1></div>';
    return;
}

$items = Workedia_Invoicing::get_invoice_items($invoice->id);
$workedia = Workedia_Settings::get_workedia_info();
?>

<div class="workedia-invoice-public-view" style="max-width: 850px; margin: 40px auto; background: white; padding: 60px; border-radius: 24px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); font-family: 'Rubik', sans-serif;" dir="rtl">
    <div style="display:flex; justify-content:space-between; margin-bottom:50px; align-items: flex-start;">
        <div>
            <h1 style="margin:0; font-weight:900; color:var(--workedia-primary-color); font-size:2.5em; letter-spacing:-1px;">فاتورة ضريبية</h1>
            <div style="color:#64748b; margin-top:10px; font-weight:700; font-size:1.1em;"># <?php echo $invoice->invoice_number; ?></div>
        </div>
        <div style="text-align:left;">
            <?php if ($workedia['workedia_logo']): ?>
                <img src="<?php echo esc_url($workedia['workedia_logo']); ?>" style="height: 60px; margin-bottom: 15px;">
            <?php endif; ?>
            <div style="font-weight:800; font-size:1.2em;"><?php echo $workedia['workedia_name']; ?></div>
            <div style="font-size:13px; color:#64748b; margin-top:5px;"><?php echo $workedia['address']; ?></div>
            <div style="font-size:13px; color:#64748b;"><?php echo $workedia['email']; ?></div>
        </div>
    </div>

    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:60px; margin-bottom:50px; padding:30px; background:#f8fafc; border-radius:20px;">
        <div>
            <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; margin-bottom:12px; letter-spacing:1px;">فاتورة إلى:</div>
            <div style="font-weight:800; font-size:1.3em; color:#111F35;"><?php echo esc_html($invoice->client_name); ?></div>
            <div style="font-size:14px; color:#64748b; margin-top:8px; white-space:pre-wrap; line-height:1.6;"><?php echo esc_html($invoice->client_details); ?></div>
            <?php if($invoice->client_email): ?>
                <div style="font-size:14px; color:#3182ce; margin-top:10px; font-weight:600;"><?php echo esc_html($invoice->client_email); ?></div>
            <?php endif; ?>
        </div>
        <div style="text-align:left;">
            <div style="display:flex; justify-content:flex-end; gap:30px; margin-bottom:12px;">
                <span style="color:#94a3b8; font-size:13px; font-weight:700;">تاريخ الإصدار:</span>
                <span style="font-weight:800; color:#111F35;"><?php echo $invoice->issue_date; ?></span>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:30px; margin-bottom:12px;">
                <span style="color:#94a3b8; font-size:13px; font-weight:700;">تاريخ الاستحقاق:</span>
                <span style="font-weight:800; color:#e53e3e;"><?php echo $invoice->due_date ?: 'عند الاستلام'; ?></span>
            </div>
            <div style="display:flex; justify-content:flex-end; gap:30px;">
                <span style="color:#94a3b8; font-size:13px; font-weight:700;">حالة الفاتورة:</span>
                <span style="background:<?php echo $invoice->status === 'paid' ? '#38a169' : '#d69e2e'; ?>; color:#fff; padding:4px 15px; border-radius:50px; font-size:12px; font-weight:800;"><?php echo strtoupper($invoice->status); ?></span>
            </div>
        </div>
    </div>

    <table style="width:100%; border-collapse:collapse; margin-bottom:40px;">
        <thead>
            <tr style="border-bottom:2px solid #111F35;">
                <th style="padding:15px; text-align:right; font-size:12px; color:#94a3b8; text-transform:uppercase;">البند والوصف</th>
                <th style="padding:15px; text-align:center; font-size:12px; color:#94a3b8; text-transform:uppercase;">الكمية</th>
                <th style="padding:15px; text-align:center; font-size:12px; color:#94a3b8; text-transform:uppercase;">سعر الوحدة</th>
                <th style="padding:15px; text-align:left; font-size:12px; color:#94a3b8; text-transform:uppercase;">الإجمالي</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
                <tr style="border-bottom:1px solid #edf2f7;">
                    <td style="padding:20px 15px; font-weight:700; color:#111F35;"><?php echo esc_html($item->description); ?></td>
                    <td style="padding:20px 15px; text-align:center; color:#64748b;"><?php echo $item->quantity; ?></td>
                    <td style="padding:20px 15px; text-align:center; color:#64748b;"><?php echo number_format($item->unit_price, 2); ?></td>
                    <td style="padding:20px 15px; text-align:left; font-weight:800; color:#111F35; font-family:monospace;"><?php echo number_format($item->quantity * $item->unit_price, 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div style="flex:1; max-width:400px; font-size:14px; color:#64748b; line-height:1.7;">
            <?php if($invoice->public_notes): ?>
                <div style="font-weight:800; color:#111F35; margin-bottom:10px;">ملاحظات وتعليمات:</div>
                <div><?php echo nl2br(esc_html($invoice->public_notes)); ?></div>
            <?php endif; ?>
        </div>
        <div style="width:300px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:15px; color:#64748b;">
                <span style="font-weight:700;">المجموع الفرعي:</span>
                <span style="font-family:monospace;"><?php echo number_format($invoice->subtotal, 2); ?> <?php echo $invoice->currency; ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; margin-bottom:15px; font-size:15px; color:#64748b;">
                <span style="font-weight:700;">إجمالي الضريبة:</span>
                <span style="font-family:monospace;"><?php echo number_format($invoice->tax_total, 2); ?> <?php echo $invoice->currency; ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:20px; background:#111F35; border-radius:12px; color:#fff; font-weight:900; font-size:1.4em; box-shadow:0 10px 20px rgba(17,31,53,0.15);">
                <span>الإجمالي:</span>
                <span style="font-family:monospace;"><?php echo number_format($invoice->total_amount, 2); ?> <?php echo $invoice->currency; ?></span>
            </div>
        </div>
    </div>

    <div style="margin-top:80px; padding-top:30px; border-top:1px solid #eee; text-align:center;">
        <div style="font-weight:800; color:#111F35; font-size:1.1em;"><?php echo $workedia['workedia_name']; ?></div>
        <div style="font-size:12px; color:#94a3b8; margin-top:5px;">هذه الفاتورة تم إنشاؤها آلياً عبر نظام Workedia.</div>
        <div style="margin-top:20px;">
            <button onclick="window.print()" class="workedia-btn" style="width:auto; height:40px; background:#4a5568; padding:0 30px;"><span class="dashicons dashicons-printer"></span> طباعة وتحميل PDF</button>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; }
    .workedia-invoice-public-view { box-shadow: none !important; margin: 0 !important; width: 100% !important; max-width: none !important; padding: 20px !important; }
    .workedia-btn { display:none !important; }
}
</style>

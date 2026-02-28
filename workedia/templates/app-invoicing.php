<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container invoicing-app">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <div class="workedia-app-header">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">نظام الفواتير والمبيعات</h2>
        <div style="display: flex; gap: 10px;">
            <button onclick="workediaOpenInvoiceModal()" class="workedia-btn" style="width: auto;">+ إنشاء فاتورة جديدة</button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="workedia-card-grid" style="margin-bottom: 30px;">
        <?php
        $stats = Workedia_Invoicing::get_statistics(get_current_user_id());
        ?>
        <div class="workedia-stat-card" style="border-right: 4px solid #38a169;">
            <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">إجمالي المبيعات المحصلة</div>
            <div style="font-size: 24px; font-weight: 900; color: #38a169; margin-top: 5px;"><?php echo number_format($stats['total_revenue'], 2); ?> <span style="font-size: 14px;">USD</span></div>
        </div>
        <div class="workedia-stat-card" style="border-right: 4px solid #d69e2e;">
            <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">مبالغ قيد الانتظار</div>
            <div style="font-size: 24px; font-weight: 900; color: #d69e2e; margin-top: 5px;"><?php echo number_format($stats['pending_amount'], 2); ?> <span style="font-size: 14px;">USD</span></div>
        </div>
        <div class="workedia-stat-card" style="border-right: 4px solid #e53e3e;">
            <div style="font-size: 11px; color: #64748b; font-weight: 800; text-transform: uppercase;">فواتير متأخرة</div>
            <div style="font-size: 24px; font-weight: 900; color: #e53e3e; margin-top: 5px;"><?php echo $stats['overdue_count']; ?> <span style="font-size: 14px;">فاتورة</span></div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="docs-filters" style="background: #fff; padding: 20px; border-radius: 16px; border: 1px solid var(--workedia-border-color); margin-bottom: 30px; display: flex; gap: 20px; align-items: center;">
        <div style="flex: 1; position: relative;">
            <input type="text" id="invoice-search" class="workedia-input" placeholder="بحث باسم العميل أو رقم الفاتورة..." oninput="workediaRefreshInvoices(this.value)" style="padding-left: 35px; border-radius: 50px;">
            <span class="dashicons dashicons-search" style="position: absolute; left: 12px; top: 12px; color: #94a3b8;"></span>
        </div>
        <button onclick="workediaExportInvoicesCSV()" class="workedia-btn workedia-btn-outline" style="width:auto; border-radius:50px;"><span class="dashicons dashicons-media-spreadsheet"></span> تصدير CSV</button>
        <select id="invoice-filter-status" class="workedia-select" onchange="workediaRefreshInvoices()" style="width: 150px; border-radius: 50px;">
            <option value="all">كل الحالات</option>
            <option value="draft">مسودة</option>
            <option value="pending">قيد الانتظار</option>
            <option value="paid">مدفوعة</option>
            <option value="cancelled">ملغاة</option>
            <option value="archived">مؤرشفة</option>
        </select>
    </div>

    <div id="workedia-invoices-list">
        <?php
        $invoices = Workedia_Invoicing::get_invoices(get_current_user_id());
        include WORKEDIA_PLUGIN_DIR . 'templates/app-invoicing-list.php';
        ?>
    </div>
</div>

<!-- Invoice Modal (Full Screen Layout) -->
<div id="invoice-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 1100px; padding: 0; background: #f8fafc;">
        <div class="workedia-modal-header" style="padding: 20px 40px; background: #fff; border-bottom: 1px solid #eee; margin: 0; position: sticky; top: 0; z-index: 10;">
            <h3 id="invoice-modal-title">فاتورة جديدة</h3>
            <div style="display:flex; gap:10px; align-items:center;">
                <button onclick="workediaSaveInvoice('draft')" class="workedia-btn workedia-btn-outline" style="width:auto; height:40px; font-size:13px;">حفظ كمسودة</button>
                <button onclick="workediaSaveInvoice('pending')" class="workedia-btn" style="width:auto; height:40px; font-size:13px;">حفظ وإصدار</button>
                <button class="workedia-modal-close" onclick="document.getElementById('invoice-modal').style.display='none'">&times;</button>
            </div>
        </div>

        <form id="invoice-form">
            <input type="hidden" name="id" id="invoice-id">
            <div style="padding: 40px; display: grid; grid-template-columns: 1fr 300px; gap: 30px;">
                <!-- Main Body -->
                <div>
                    <div style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #e2e8f0; margin-bottom: 30px;">
                        <h4 style="margin: 0 0 20px 0; font-weight: 800; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">بيانات العميل</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="workedia-form-group">
                                <label class="workedia-label">اسم العميل:</label>
                                <input type="text" id="inv-client-name" name="client_name" class="workedia-input" required>
                            </div>
                            <div class="workedia-form-group">
                                <label class="workedia-label">البريد الإلكتروني:</label>
                                <input type="email" id="inv-client-email" name="client_email" class="workedia-input">
                            </div>
                            <div class="workedia-form-group" style="grid-column: span 2;">
                                <label class="workedia-label">تفاصيل العنوان / العميل:</label>
                                <textarea id="inv-client-details" name="client_details" class="workedia-textarea" rows="2"></textarea>
                            </div>
                        </div>
                    </div>

                    <div style="background: #fff; padding: 30px; border-radius: 20px; border: 1px solid #e2e8f0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <h4 style="margin: 0; font-weight: 800;">بنود الفاتورة</h4>
                            <button type="button" onclick="addInvoiceItem()" class="workedia-btn workedia-btn-outline" style="width: auto; height: 32px; font-size: 11px;">+ إضافة بند</button>
                        </div>
                        <div class="workedia-table-container" style="box-shadow: none; border-color: #f1f5f9;">
                            <table class="workedia-table">
                                <thead>
                                    <tr>
                                        <th style="width: 40px;"></th>
                                        <th>الوصف</th>
                                        <th style="width: 100px;">الكمية</th>
                                        <th style="width: 120px;">السعر</th>
                                        <th style="width: 80px;">الضريبة%</th>
                                        <th style="width: 120px;">الإجمالي</th>
                                        <th style="width: 40px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="invoice-items-list">
                                    <!-- Items will be added here -->
                                </tbody>
                            </table>
                        </div>
                        <div style="margin-top: 20px; display: flex; justify-content: flex-end;">
                            <div style="width: 300px; background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #edf2f7;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; color: #64748b;">
                                    <span>المجموع الفرعي:</span>
                                    <span id="inv-subtotal">0.00</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 13px; color: #64748b;">
                                    <span>إجمالي الضريبة:</span>
                                    <span id="inv-tax-total">0.00</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; padding-top: 10px; border-top: 1px solid #e2e8f0; font-weight: 800; color: var(--workedia-dark-color);">
                                    <span>الإجمالي الكلي:</span>
                                    <span id="inv-total-amount">0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div style="display: grid; gap: 25px; align-self: start;">
                    <div style="background: #fff; padding: 25px; border-radius: 20px; border: 1px solid #e2e8f0;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 800;">إعدادات الفاتورة</h4>
                        <div class="workedia-form-group">
                            <label class="workedia-label">العملة:</label>
                            <select name="currency" id="inv-currency" class="workedia-select" onchange="updateCurrencyDisplay()">
                                <option value="USD">USD - Dollar</option>
                                <option value="EUR">EUR - Euro</option>
                                <option value="EGP">EGP - جنيه مصري</option>
                                <option value="SAR">SAR - ريال سعودي</option>
                            </select>
                        </div>
                        <div class="workedia-form-group">
                            <label class="workedia-label">تاريخ الإصدار:</label>
                            <input type="date" name="issue_date" id="inv-issue-date" class="workedia-input" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="workedia-form-group">
                            <label class="workedia-label">تاريخ الاستحقاق:</label>
                            <input type="date" name="due_date" id="inv-due-date" class="workedia-input">
                        </div>
                    </div>

                    <div style="background: #fff; padding: 25px; border-radius: 20px; border: 1px solid #e2e8f0;">
                        <h4 style="margin: 0 0 15px 0; font-size: 14px; font-weight: 800;">ملاحظات</h4>
                        <div class="workedia-form-group">
                            <label class="workedia-label">ملاحظات عامة (تظهر للعميل):</label>
                            <textarea name="public_notes" id="inv-public-notes" class="workedia-textarea" rows="2" placeholder="شكراً لتعاملكم معنا..."></textarea>
                        </div>
                        <div class="workedia-form-group" style="margin-bottom: 0;">
                            <label class="workedia-label">ملاحظات داخلية:</label>
                            <textarea name="notes" id="inv-private-notes" class="workedia-textarea" rows="2" placeholder="للمتابعة الداخلية فقط..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Invoice Details & View Modal -->
<div id="invoice-view-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 900px; padding: 0;">
        <div class="workedia-modal-header" style="padding: 20px 40px; margin: 0; background: #fafafa; border-bottom: 1px solid #eee;">
            <h3>تفاصيل الفاتورة <span id="view-inv-number" style="color:var(--workedia-primary-color); margin-right:10px;"></span></h3>
            <div style="display:flex; gap:10px;">
                <button onclick="workediaPrintInvoice()" class="workedia-btn" style="width:auto; background:#4a5568;"><span class="dashicons dashicons-printer"></span> طباعة / PDF</button>
                <button class="workedia-modal-close" onclick="document.getElementById('invoice-view-modal').style.display='none'">&times;</button>
            </div>
        </div>
        <div style="padding: 40px; display: grid; grid-template-columns: 1fr 300px; gap: 30px;">
            <div id="invoice-view-body" style="background:#fff; border:1px solid #eee; padding:40px; border-radius:12px; box-shadow:0 5px 15px rgba(0,0,0,0.02);">
                <!-- Rendered Invoice Content -->
            </div>
            <div style="display:grid; gap:20px; align-self: start;">
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <h4 style="margin:0 0 15px 0; font-size:13px; font-weight:800;">سجل النشاطات</h4>
                    <div id="invoice-activity-logs" style="font-size:11px; display:grid; gap:10px;">
                        <!-- Loaded via AJAX -->
                    </div>
                </div>
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <h4 style="margin:0 0 15px 0; font-size:13px; font-weight:800;">المرفقات</h4>
                    <div id="invoice-attachments-list" style="margin-bottom:15px;">
                        <!-- Loaded via AJAX -->
                    </div>
                    <form id="invoice-attachment-form">
                        <input type="file" id="inv-attach-file" style="display:none" onchange="workediaUploadInvoiceAttachment()">
                        <button type="button" onclick="document.getElementById('inv-attach-file').click()" class="workedia-btn workedia-btn-outline" style="width:100%; height:35px; font-size:12px;">+ إضافة مرفق</button>
                    </form>
                </div>
                <div style="background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0;">
                    <h4 style="margin:0 0 15px 0; font-size:13px; font-weight:800;">روابط سريعة</h4>
                    <button onclick="workediaCopyInvoiceLink()" class="workedia-btn workedia-btn-outline" style="width:100%; height:35px; font-size:12px; margin-bottom:10px;"><span class="dashicons dashicons-admin-links"></span> رابط المعاينة</button>
                    <button onclick="workediaSendInvoiceEmail()" class="workedia-btn workedia-btn-outline" style="width:100%; height:35px; font-size:12px;"><span class="dashicons dashicons-email"></span> إرسال للعميل</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let currentInvoiceItems = [];
let currentViewInvoice = null;

function workediaOpenInvoiceModal(id = null) {
    document.getElementById('invoice-form').reset();
    document.getElementById('invoice-id').value = id || '';
    document.getElementById('invoice-items-list').innerHTML = '';
    currentInvoiceItems = [];

    if (id) {
        document.getElementById('invoice-modal-title').innerText = 'تعديل فاتورة';
        // Load data via AJAX if editing
    } else {
        document.getElementById('invoice-modal-title').innerText = 'إنشاء فاتورة جديدة';
        addInvoiceItem(); // Add one empty item
    }

    document.getElementById('invoice-modal').style.display = 'flex';
    initItemsSorting();
    calculateTotals();
}

function addInvoiceItem(data = null) {
    const id = Date.now() + Math.random();
    const item = {
        id: id,
        description: data ? data.description : '',
        quantity: data ? data.quantity : 1,
        unit_price: data ? data.unit_price : 0,
        tax_rate: data ? data.tax_rate : 0,
        discount_amount: data ? data.discount_amount : 0
    };

    const row = document.createElement('tr');
    row.className = 'invoice-item-row';
    row.setAttribute('data-id', id);
    row.innerHTML = `
        <td class="item-drag-handle" style="cursor: grab; color: #cbd5e0; text-align: center;"><span class="dashicons dashicons-menu"></span></td>
        <td><input type="text" class="workedia-input item-desc" value="${item.description}" style="padding: 8px; font-size: 13px;" oninput="updateItemValue(${id}, 'description', this.value)"></td>
        <td><input type="number" class="workedia-input item-qty" value="${item.quantity}" step="0.01" style="padding: 8px; font-size: 13px;" oninput="updateItemValue(${id}, 'quantity', this.value)"></td>
        <td><input type="number" class="workedia-input item-price" value="${item.unit_price}" step="0.01" style="padding: 8px; font-size: 13px;" oninput="updateItemValue(${id}, 'unit_price', this.value)"></td>
        <td><input type="number" class="workedia-input item-tax" value="${item.tax_rate}" step="0.1" style="padding: 8px; font-size: 13px;" oninput="updateItemValue(${id}, 'tax_rate', this.value)"></td>
        <td style="font-weight: 700; color: var(--workedia-dark-color); font-family: monospace; font-size: 14px;" class="item-line-total">0.00</td>
        <td style="text-align: center;"><button type="button" onclick="removeInvoiceItem(${id})" style="background:none; border:none; color:#e53e3e; cursor:pointer;"><span class="dashicons dashicons-trash"></span></button></td>
    `;

    document.getElementById('invoice-items-list').appendChild(row);
    currentInvoiceItems.push(item);
    calculateTotals();
}

function updateItemValue(id, key, val) {
    const item = currentInvoiceItems.find(i => i.id == id);
    if (item) {
        item[key] = key === 'description' ? val : parseFloat(val) || 0;
        calculateTotals();
    }
}

function removeInvoiceItem(id) {
    if (currentInvoiceItems.length <= 1) return;
    currentInvoiceItems = currentInvoiceItems.filter(i => i.id != id);
    document.querySelector(`tr[data-id="${id}"]`).remove();
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    let taxTotal = 0;

    const currency = document.getElementById('inv-currency').value;

    currentInvoiceItems.forEach(item => {
        const lineTotal = item.quantity * item.unit_price;
        const lineTax = lineTotal * (item.tax_rate / 100);
        subtotal += lineTotal;
        taxTotal += lineTax;

        // Update line total in UI
        const row = document.querySelector(`tr[data-id="${item.id}"]`);
        if (row) {
            row.querySelector('.item-line-total').innerText = (lineTotal + lineTax).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
    });

    const total = subtotal + taxTotal;

    document.getElementById('inv-subtotal').innerText = subtotal.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ' + currency;
    document.getElementById('inv-tax-total').innerText = taxTotal.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ' + currency;
    document.getElementById('inv-total-amount').innerText = total.toLocaleString(undefined, {minimumFractionDigits: 2}) + ' ' + currency;
}

function initItemsSorting() {
    const el = document.getElementById('invoice-items-list');
    Sortable.create(el, {
        animation: 150,
        handle: '.item-drag-handle',
        onEnd: function() {
            const newOrder = [];
            el.querySelectorAll('.invoice-item-row').forEach(row => {
                const id = row.getAttribute('data-id');
                newOrder.push(currentInvoiceItems.find(i => i.id == id));
            });
            currentInvoiceItems = newOrder;
        }
    });
}

function workediaSaveInvoice(status) {
    const form = document.getElementById('invoice-form');
    const formData = new FormData(form);

    if (!document.getElementById('inv-client-name').value) {
        return alert('يرجى إدخال اسم العميل');
    }

    formData.append('action', 'workedia_save_invoice');
    formData.append('status', status);
    formData.append('items', JSON.stringify(currentInvoiceItems));
    formData.append('nonce', '<?php echo wp_create_nonce("workedia_invoicing_action"); ?>');

    workediaShowNotification('جاري حفظ الفاتورة...');

    fetch(ajaxurl, { method: 'POST', body: formData }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حفظ الفاتورة بنجاح');
            document.getElementById('invoice-modal').style.display = 'none';
            location.reload();
        } else alert(res.data);
    });
}

function workediaViewInvoice(id) {
    const nonce = '<?php echo wp_create_nonce("workedia_invoicing_action"); ?>';
    document.getElementById('invoice-view-modal').style.display = 'flex';
    document.getElementById('invoice-view-body').innerHTML = '<div style="text-align:center; padding:100px;"><div class="workedia-loader-mini"></div></div>';

    fetch(ajaxurl + `?action=workedia_get_invoice_details&id=${id}&nonce=${nonce}`)
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            currentViewInvoice = res.data.invoice;
            renderInvoiceView(res.data);
        } else alert(res.data);
    });
}

function renderInvoiceView(data) {
    const inv = data.invoice;
    const items = data.items;
    const logs = data.logs;
    const attachments = data.attachments;

    document.getElementById('view-inv-number').innerText = inv.invoice_number;

    let itemsHtml = items.map(item => `
        <tr>
            <td style="padding:12px; border-bottom:1px solid #f1f5f9;">${item.description}</td>
            <td style="padding:12px; border-bottom:1px solid #f1f5f9; text-align:center;">${item.quantity}</td>
            <td style="padding:12px; border-bottom:1px solid #f1f5f9; text-align:center;">${parseFloat(item.unit_price).toFixed(2)}</td>
            <td style="padding:12px; border-bottom:1px solid #f1f5f9; text-align:center;">${item.tax_rate}%</td>
            <td style="padding:12px; border-bottom:1px solid #f1f5f9; text-align:left; font-family:monospace;">${((item.quantity * item.unit_price) * (1 + item.tax_rate/100)).toFixed(2)}</td>
        </tr>
    `).join('');

    document.getElementById('invoice-view-body').innerHTML = `
        <div style="display:flex; justify-content:space-between; margin-bottom:40px;">
            <div>
                <h1 style="margin:0; font-weight:900; color:var(--workedia-primary-color); font-size:2em;">INVOICE</h1>
                <div style="color:#64748b; margin-top:5px; font-weight:700;"># ${inv.invoice_number}</div>
            </div>
            <div style="text-align:left;">
                <div style="font-weight:800;"><?php echo $workedia['workedia_name']; ?></div>
                <div style="font-size:12px; color:#64748b;"><?php echo $workedia['address']; ?></div>
                <div style="font-size:12px; color:#64748b;"><?php echo $workedia['email']; ?></div>
            </div>
        </div>

        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:40px; margin-bottom:40px;">
            <div>
                <div style="font-size:11px; font-weight:800; color:#94a3b8; text-transform:uppercase; margin-bottom:10px;">إلى العميل:</div>
                <div style="font-weight:800; font-size:1.1em;">${inv.client_name}</div>
                <div style="font-size:13px; color:#64748b; margin-top:5px; white-space:pre-wrap;">${inv.client_details}</div>
            </div>
            <div style="text-align:left;">
                <div style="display:flex; justify-content:flex-end; gap:20px; margin-bottom:8px;">
                    <span style="color:#94a3b8; font-size:12px;">تاريخ الإصدار:</span>
                    <span style="font-weight:700;">${inv.issue_date}</span>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:20px; margin-bottom:8px;">
                    <span style="color:#94a3b8; font-size:12px;">تاريخ الاستحقاق:</span>
                    <span style="font-weight:700; color:#e53e3e;">${inv.due_date}</span>
                </div>
                <div style="display:flex; justify-content:flex-end; gap:20px;">
                    <span style="color:#94a3b8; font-size:12px;">حالة الفاتورة:</span>
                    <span class="workedia-badge" style="background:#edf2f7; color:#4a5568; font-weight:800;">${inv.status.toUpperCase()}</span>
                </div>
            </div>
        </div>

        <table style="width:100%; border-collapse:collapse; margin-bottom:30px;">
            <thead>
                <tr style="background:#f8fafc;">
                    <th style="padding:12px; text-align:right; font-size:11px; color:#94a3b8;">الوصف</th>
                    <th style="padding:12px; text-align:center; font-size:11px; color:#94a3b8;">الكمية</th>
                    <th style="padding:12px; text-align:center; font-size:11px; color:#94a3b8;">السعر</th>
                    <th style="padding:12px; text-align:center; font-size:11px; color:#94a3b8;">الضريبة%</th>
                    <th style="padding:12px; text-align:left; font-size:11px; color:#94a3b8;">الإجمالي</th>
                </tr>
            </thead>
            <tbody>${itemsHtml}</tbody>
        </table>

        <div style="display:flex; justify-content:flex-end;">
            <div style="width:250px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:14px; color:#64748b;">
                    <span>المجموع:</span>
                    <span>${parseFloat(inv.subtotal).toFixed(2)} ${inv.currency}</span>
                </div>
                <div style="display:flex; justify-content:space-between; margin-bottom:10px; font-size:14px; color:#64748b;">
                    <span>الضريبة:</span>
                    <span>${parseFloat(inv.tax_total).toFixed(2)} ${inv.currency}</span>
                </div>
                <div style="display:flex; justify-content:space-between; padding-top:10px; border-top:2px solid #111F35; font-weight:900; font-size:1.2em;">
                    <span>الإجمالي:</span>
                    <span style="color:var(--workedia-primary-color);">${parseFloat(inv.total_amount).toFixed(2)} ${inv.currency}</span>
                </div>
            </div>
        </div>

        ${inv.public_notes ? `<div style="margin-top:50px; padding-top:20px; border-top:1px solid #eee; font-size:12px; color:#64748b;"><strong>ملاحظات:</strong><br>${inv.public_notes}</div>` : ''}
    `;

    // Render Logs
    document.getElementById('invoice-activity-logs').innerHTML = logs.map(l => `
        <div style="border-bottom:1px solid #eee; padding-bottom:5px;">
            <div style="font-weight:700;">${l.action}</div>
            <div style="color:#94a3b8;">بواسطة: ${l.display_name} - ${l.created_at}</div>
        </div>
    `).join('') || 'لا توجد نشاطات مسجلة.';

    // Render Attachments
    document.getElementById('invoice-attachments-list').innerHTML = attachments.map(a => `
        <div style="display:flex; justify-content:space-between; align-items:center; background:white; padding:8px 12px; border-radius:8px; border:1px solid #eee; margin-bottom:5px;">
            <a href="${a.file_url}" target="_blank" style="font-size:11px; color:#3182ce; text-decoration:none; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:180px;">${a.file_name}</a>
            <button onclick="workediaDeleteInvoiceAttachment(${a.id})" style="background:none; border:none; color:#e53e3e; cursor:pointer;"><span class="dashicons dashicons-no-alt" style="font-size:14px;"></span></button>
        </div>
    `).join('') || '<div style="text-align:center; color:#94a3b8; font-size:11px;">لا توجد مرفقات</div>';
}

function workediaUploadInvoiceAttachment() {
    const file = document.getElementById('inv-attach-file').files[0];
    if (!file) return;

    const fd = new FormData();
    fd.append('action', 'workedia_upload_invoice_attachment');
    fd.append('invoice_id', currentViewInvoice.id);
    fd.append('attachment', file);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_invoicing_action"); ?>');

    workediaShowNotification('جاري رفع الملف...');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaViewInvoice(currentViewInvoice.id);
            workediaShowNotification('تم إرفاق الملف بنجاح');
        } else alert(res.data);
    });
}

function workediaDeleteInvoice(id) {
    if (!confirm('هل أنت متأكد من حذف هذه الفاتورة وكافة بنودها ومرفقاتها؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_invoice');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_invoicing_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}

function workediaDuplicateInvoice(id) {
    const fd = new FormData();
    fd.append('action', 'workedia_duplicate_invoice');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_invoicing_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم تكرار الفاتورة بنجاح');
            workediaOpenInvoiceModal(res.data);
        }
    });
}

function workediaPrintInvoice() {
    const printWindow = window.open('', '_blank');
    const content = document.getElementById('invoice-view-body').innerHTML;
    printWindow.document.write(`
        <html dir="rtl">
        <head>
            <title>فاتورة - Workedia</title>
            <style>
                body { font-family: "Rubik", sans-serif; padding: 50px; color: #1a202c; line-height: 1.6; }
                table { width: 100%; border-collapse: collapse; }
                .workedia-badge { display:none; }
                @media print { .no-print { display:none; } }
            </style>
        </head>
        <body>
            ${content}
            <script>window.print();<\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

function workediaCopyInvoiceLink() {
    const link = `${workedia_home_url}/invoice?token=${currentViewInvoice.public_token}`;
    navigator.clipboard.writeText(link).then(() => {
        workediaShowNotification('تم نسخ رابط المعاينة بنجاح');
    });
}

function workediaRefreshInvoices(search = '') {
    const status = document.getElementById('invoice-filter-status').value;
    fetch(ajaxurl + `?action=workedia_get_invoices&search=${encodeURIComponent(search)}&status=${status}`)
    .then(r => r.text())
    .then(html => {
        document.getElementById('workedia-invoices-list').innerHTML = html;
    });
}

function updateCurrencyDisplay() {
    calculateTotals();
}

function workediaDeleteInvoiceAttachment(id) {
    if (!confirm('هل تريد حذف هذا المرفق؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_invoice_attachment');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_invoicing_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaViewInvoice(currentViewInvoice.id);
        }
    });
}

function workediaSendInvoiceEmail() {
    const subject = encodeURIComponent(`فاتورة جديدة من ${document.querySelector('.user-meta-header strong')?.innerText || 'Workedia'}`);
    const body = encodeURIComponent(`عزيزي العميل،\n\nيمكنكم معاينة فاتورتكم رقم ${currentViewInvoice.invoice_number} عبر الرابط التالي:\n${workedia_home_url}/invoice?token=${currentViewInvoice.public_token}\n\nشكراً لتعاملكم معنا.`);
    window.location.href = `mailto:${currentViewInvoice.client_email}?subject=${subject}&body=${body}`;
}

function workediaExportInvoicesCSV() {
    const table = document.querySelector('#workedia-invoices-list table');
    if (!table) return;

    let csv = [];
    const rows = table.querySelectorAll('tr');

    for (let i = 0; i < rows.length; i++) {
        const row = [], cols = rows[i].querySelectorAll('td, th');
        for (let j = 0; j < cols.length - 1; j++) {
            row.push('"' + cols[j].innerText.trim().replace(/"/g, '""') + '"');
        }
        csv.push(row.join(','));
    }

    const csvFile = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const downloadLink = document.createElement('a');
    downloadLink.download = 'invoices_export.csv';
    downloadLink.href = window.URL.createObjectURL(csvFile);
    downloadLink.style.display = 'none';
    document.body.appendChild(downloadLink);
    downloadLink.click();
}
</script>

<style>
.invoice-item-row:hover { background: #fafafa; }
.calc-btn { height: auto !important; }
</style>

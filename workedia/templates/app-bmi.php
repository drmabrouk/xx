<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container bmi-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">حاسبة مؤشر كتلة الجسم (BMI)</h2>
        <div style="display:flex; gap:10px;">
            <button onclick="window.print()" class="workedia-btn workedia-btn-outline" style="width:auto;"><span class="dashicons dashicons-printer"></span> طباعة</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 30px;">
        <!-- Left: Calculator -->
        <div style="background: #fff; border-radius: 24px; padding: 30px; border: 1px solid var(--workedia-border-color); box-shadow: var(--workedia-shadow);">
            <form id="bmi-form">
                <div class="workedia-form-group">
                    <label class="workedia-label">نظام القياس:</label>
                    <select id="bmi-unit-system" class="workedia-select" onchange="toggleBMISystem()">
                        <option value="metric">متري (كجم / سم)</option>
                        <option value="imperial">إمبراطوري (رطل / بوصة)</option>
                    </select>
                </div>
                <div class="workedia-form-group">
                    <label class="workedia-label" id="label-weight">الوزن (كجم):</label>
                    <input type="number" id="bmi-weight" class="workedia-input" step="0.1" required oninput="calculateBMI()">
                </div>
                <div class="workedia-form-group">
                    <label class="workedia-label" id="label-height">الطول (سم):</label>
                    <input type="number" id="bmi-height" class="workedia-input" step="0.1" required oninput="calculateBMI()">
                </div>

                <div id="bmi-result-box" style="margin-top: 30px; padding: 25px; border-radius: 20px; text-align: center; background: #f8fafc; border: 1px solid #edf2f7; transition: 0.3s;">
                    <div style="font-size: 14px; color: #64748b; margin-bottom: 5px;">مؤشر كتلة جسمك هو:</div>
                    <div id="bmi-value" style="font-size: 3em; font-weight: 800; color: var(--workedia-dark-color);">0.0</div>
                    <div id="bmi-status" style="font-size: 1.1em; font-weight: 700; margin-top: 10px; color: #94a3b8;">---</div>
                </div>

                <button type="submit" class="workedia-btn" style="width: 100%; margin-top: 25px; height: 50px; font-weight: 800;">حفظ في السجل</button>
            </form>
        </div>

        <!-- Right: History -->
        <div style="background: #fff; border-radius: 24px; padding: 30px; border: 1px solid var(--workedia-border-color); box-shadow: var(--workedia-shadow);">
            <h3 style="margin: 0 0 20px 0; border-bottom: 1px solid #f1f5f9; padding-bottom: 15px;">سجل القياسات</h3>
            <div id="bmi-history-table">
                <?php
                $history = Workedia_BMI::get_history(get_current_user_id());
                include WORKEDIA_PLUGIN_DIR . 'templates/app-bmi-history.php';
                ?>
            </div>

            <div style="margin-top: 40px;">
                <h4 style="margin: 0 0 15px 0;">نصيحة صحية</h4>
                <div id="bmi-tip" style="padding: 20px; background: var(--workedia-pastel-pink); border-radius: 15px; color: var(--workedia-primary-color); font-size: 14px; line-height: 1.6;">
                    أدخل بياناتك للحصول على نصيحة مخصصة بناءً على مؤشر كتلة جسمك.
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function calculateBMI() {
    const system = document.getElementById('bmi-unit-system').value;
    const w = parseFloat(document.getElementById('bmi-weight').value);
    const h = parseFloat(document.getElementById('bmi-height').value);

    if (!w || !h) return;

    let bmi = 0;
    if (system === 'metric') {
        bmi = w / ((h / 100) * (h / 100));
    } else {
        bmi = (w / (h * h)) * 703;
    }

    const valueEl = document.getElementById('bmi-value');
    const statusEl = document.getElementById('bmi-status');
    const boxEl = document.getElementById('bmi-result-box');
    const tipEl = document.getElementById('bmi-tip');

    valueEl.innerText = bmi.toFixed(1);

    let status = '';
    let color = '';
    let tip = '';

    if (bmi < 18.5) {
        status = 'نقص في الوزن';
        color = '#3182ce';
        tip = 'وزنك أقل من الطبيعي. يرجى مراجعة اخصائي تغذية للتأكد من حصولك على العناصر الغذائية الكافية.';
    } else if (bmi < 25) {
        status = 'وزن مثالي';
        color = '#38a169';
        tip = 'أنت في النطاق الصحي المثالي! استمر في الحفاظ على نمط حياتك المتوازن.';
    } else if (bmi < 30) {
        status = 'زيادة في الوزن';
        color = '#d69e2e';
        tip = 'لديك زيادة بسيطة في الوزن. ينصح بزيادة النشاط البدني ومراقبة السعرات الحرارية.';
    } else {
        status = 'سمنة مفرطة';
        color = '#e53e3e';
        tip = 'أنت في نطاق السمنة، مما قد يزيد من مخاطر الأمراض المزمنة. ننصح باستشارة طبيب لوضع خطة صحية.';
    }

    statusEl.innerText = status;
    statusEl.style.color = color;
    valueEl.style.color = color;
    boxEl.style.borderColor = color;
    tipEl.innerText = tip;
}

function toggleBMISystem() {
    const system = document.getElementById('bmi-unit-system').value;
    document.getElementById('label-weight').innerText = system === 'metric' ? 'الوزن (كجم):' : 'الوزن (رطل):';
    document.getElementById('label-height').innerText = system === 'metric' ? 'الطول (سم):' : 'الطول (بوصة):';
    calculateBMI();
}

document.getElementById('bmi-form').onsubmit = function(e) {
    e.preventDefault();
    const w = document.getElementById('bmi-weight').value;
    const h = document.getElementById('bmi-height').value;
    const bmi = document.getElementById('bmi-value').innerText;
    const status = document.getElementById('bmi-status').innerText;
    const system = document.getElementById('bmi-unit-system').value;

    const fd = new FormData();
    fd.append('action', 'workedia_save_bmi');
    fd.append('weight', w);
    fd.append('height', h);
    fd.append('bmi', bmi);
    fd.append('classification', status);
    fd.append('units', JSON.stringify({ w: system === 'metric' ? 'kg' : 'lb', h: system === 'metric' ? 'cm' : 'inch' }));
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_bmi_action"); ?>');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حفظ النتيجة في سجلك بنجاح');
            workediaRefreshBMIHistory();
        } else alert(res.data);
    });
};

function workediaRefreshBMIHistory() {
    fetch(ajaxurl + '?action=workedia_get_bmi_history')
    .then(r => r.text())
    .then(html => {
        document.getElementById('bmi-history-table').innerHTML = html;
    });
}

function workediaDeleteBMI(id) {
    if (!confirm('هل تريد حذف هذا السجل؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_bmi');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_bmi_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) {
            workediaShowNotification('تم حذف السجل');
            workediaRefreshBMIHistory();
        }
    });
}
</script>

<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container bmi-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">حاسبة مؤشر كتلة الجسم (BMI)</h2>
        <div style="display:flex; gap:10px;">
            <button onclick="workediaGenerateBMIReport()" class="workedia-btn" style="width:auto; background: #27ae60;"><span class="dashicons dashicons-media-document"></span> استخراج تقرير صحي</button>
            <button onclick="window.print()" class="workedia-btn workedia-btn-outline" style="width:auto;"><span class="dashicons dashicons-printer"></span> طباعة السجل</button>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 320px 1fr; gap: 20px; max-width: 1100px; margin: 0 auto;">
        <!-- Left: Calculator -->
        <div style="background: #fff; border-radius: 20px; padding: 25px; border: 1px solid var(--workedia-border-color); box-shadow: 0 10px 25px rgba(0,0,0,0.02); align-self: start;">
            <form id="bmi-form">
                <div class="workedia-form-group" style="margin-bottom: 15px;">
                    <label class="workedia-label" style="font-size: 12px;">نظام القياس:</label>
                    <select id="bmi-unit-system" class="workedia-select" onchange="toggleBMISystem()" style="height: 40px; font-size: 13px;">
                        <option value="metric">متري (كجم / سم)</option>
                        <option value="imperial">إمبراطوري (رطل / بوصة)</option>
                    </select>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                    <div class="workedia-form-group">
                        <label class="workedia-label" id="label-weight" style="font-size: 12px;">الوزن (كجم):</label>
                        <input type="number" id="bmi-weight" class="workedia-input" step="0.1" required oninput="calculateBMI()" style="height: 40px;">
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label" id="label-height" style="font-size: 12px;">الطول (سم):</label>
                        <input type="number" id="bmi-height" class="workedia-input" step="0.1" required oninput="calculateBMI()" style="height: 40px;">
                    </div>
                </div>
                <div class="workedia-form-group">
                    <label class="workedia-label" style="font-size: 12px;">العمر:</label>
                    <input type="number" id="bmi-age" class="workedia-input" min="1" max="120" oninput="calculateBMI()" style="height: 40px;" placeholder="أدخل عمرك">
                </div>

                <div id="bmi-result-box" style="margin-top: 20px; padding: 20px; border-radius: 16px; text-align: center; background: #fcfcfc; border: 1px solid #f1f5f9; transition: 0.3s; position: relative; overflow: hidden;">
                    <div style="font-size: 12px; color: #94a3b8; font-weight: 600; margin-bottom: 2px;">مؤشر كتلة الجسم</div>
                    <div id="bmi-value" style="font-size: 2.8em; font-weight: 900; color: var(--workedia-dark-color); line-height: 1;">0.0</div>
                    <div id="bmi-status" style="font-size: 13px; font-weight: 800; margin-top: 8px; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.5px;">---</div>
                </div>

                <button type="submit" class="workedia-btn" style="width: 100%; margin-top: 20px; height: 45px; font-weight: 800; font-size: 14px;">حفظ القياس</button>
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

            <div style="margin-top: 30px; background: #fafafa; border-radius: 20px; padding: 25px; border: 1px solid #f1f5f9;">
                <h4 style="margin: 0 0 15px 0; color: var(--workedia-dark-color); font-weight: 800; display: flex; align-items: center; gap: 8px;">
                    <span class="dashicons dashicons-heart" style="color: var(--workedia-primary-color);"></span> الإرشادات الصحية المخصصة
                </h4>
                <div id="bmi-tip" style="color: #4a5568; font-size: 14px; line-height: 1.7;">
                    <div style="text-align: center; color: #94a3b8; padding: 10px;">أدخل وزنك وطولك الآن للحصول على تقييم صحي دقيق وإرشادات مخصصة لحالتك.</div>
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

    const age = parseInt(document.getElementById('bmi-age').value) || 30;
    const valueEl = document.getElementById('bmi-value');
    const statusEl = document.getElementById('bmi-status');
    const boxEl = document.getElementById('bmi-result-box');
    const tipEl = document.getElementById('bmi-tip');

    valueEl.innerText = bmi.toFixed(1);

    let status = '';
    let color = '';

    // Adjust logic based on age
    let idealBmiMin = 18.5;
    let idealBmiMax = 25;

    if (age > 65) {
        idealBmiMin = 22;
        idealBmiMax = 27;
    }

    const tips = {
        underweight: [
            'ركز على تناول وجبات غنية بالسعرات الحرارية والمغذيات.',
            'أضف البروتينات الصحية مثل اللحوم، البيض، والبقوليات.',
            'مارس تمارين القوة لبناء الكتلة العضلية.',
            'استشر أخصائي تغذية لاستبعاد أي أسباب طبية.'
        ],
        normal: [
            'حافظ على نمط حياتك المتوازن الحالي.',
            'استمر في ممارسة النشاط البدني لمدة 150 دقيقة أسبوعياً.',
            'تنوع في تناول الخضروات والفواكه الطازجة.',
            'اشرب كميات كافية من الماء (2-3 لتر يومياً).'
        ],
        overweight: [
            'قلل من تناول السكريات والكربوهيدرات المكررة.',
            'زد من وتيرة التمارين الهوائية (المشي السريع، السباحة).',
            'راقب حجم الحصص الغذائية في وجباتك.',
            'حاول استبدال الوجبات السريعة بخيارات منزلية صحية.'
        ],
        obese: [
            'ننصح بزيارة الطبيب لعمل الفحوصات الدورية الشاملة.',
            'ضع أهدافاً صغيرة وواقعية لخفض الوزن تدريجياً.',
            'تجنب المشروبات الغازية والعصائر المحلاة تماماً.',
            'النوم الكافي يساعد في تنظيم عمليات الحرق في الجسم.'
        ]
    };

    let tipItems = [];
    if (bmi < idealBmiMin) {
        status = 'نقص في الوزن';
        color = '#3182ce';
        tipItems = tips.underweight;
    } else if (bmi < idealBmiMax) {
        status = 'وزن مثالي';
        color = '#38a169';
        tipItems = tips.normal;
    } else if (bmi < 30) {
        status = 'زيادة في الوزن';
        color = '#d69e2e';
        tipItems = tips.overweight;
    } else {
        status = 'سمنة مفرطة';
        color = '#e53e3e';
        tipItems = tips.obese;
    }

    // Add age-specific tip
    if (age > 65) {
        tipItems.unshift('بالنسبة لكبار السن (أكبر من 65 عاماً)، يفضل أن يكون مؤشر كتلة الجسم بين 22 و 27.');
    }

    statusEl.innerText = status;
    tipEl.innerHTML = `<ul style="margin: 15px 0 0 0; padding-right: 20px; list-style-type: disc;">${tipItems.map(t => `<li style="margin-bottom:8px;">${t}</li>`).join('')}</ul>`;
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
    const age = document.getElementById('bmi-age').value;
    const bmi = document.getElementById('bmi-value').innerText;
    const status = document.getElementById('bmi-status').innerText;
    const system = document.getElementById('bmi-unit-system').value;

    const fd = new FormData();
    fd.append('action', 'workedia_save_bmi');
    fd.append('weight', w);
    fd.append('height', h);
    fd.append('age', age);
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

function workediaGenerateBMIReport() {
    const bmi = document.getElementById('bmi-value').innerText;
    const status = document.getElementById('bmi-status').innerText;
    const weight = document.getElementById('bmi-weight').value;
    const height = document.getElementById('bmi-height').value;
    const age = document.getElementById('bmi-age').value || '-';

    if (bmi === '0.0') {
        alert('يرجى حساب مؤشر كتلة الجسم أولاً');
        return;
    }

    const reportWindow = window.open('', '_blank');
    const content = `
        <html dir="rtl">
        <head>
            <title>تقرير صحي مفصل - Workedia</title>
            <style>
                body { font-family: "Rubik", sans-serif; padding: 40px; color: #1a202c; line-height: 1.6; }
                .header { text-align: center; border-bottom: 3px solid #F63049; padding-bottom: 20px; margin-bottom: 40px; }
                .report-title { font-size: 24px; font-weight: 800; color: #111F35; }
                .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 40px; }
                .card { background: #f8fafc; padding: 20px; border-radius: 12px; border: 1px solid #e2e8f0; }
                .label { font-size: 12px; color: #64748b; font-weight: 600; }
                .value { font-size: 18px; font-weight: 800; color: #111F35; margin-top: 5px; }
                .bmi-result { text-align: center; padding: 30px; background: #F63049; color: white; border-radius: 20px; margin-bottom: 40px; }
                .bmi-value { font-size: 48px; font-weight: 900; }
                .bmi-status { font-size: 20px; font-weight: 700; margin-top: 10px; }
                .tips-section { background: #fff; border: 2px solid #edf2f7; padding: 30px; border-radius: 20px; }
                .tips-title { font-size: 18px; font-weight: 800; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
                .footer { margin-top: 50px; text-align: center; font-size: 11px; color: #94a3b8; border-top: 1px solid #eee; padding-top: 20px; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="report-title">تقرير التقييم الصحي (BMI)</div>
                <div style="font-size: 14px; color: #64748b; margin-top: 5px;">Workedia Health Services</div>
            </div>

            <div class="grid">
                <div class="card"><div class="label">الاسم</div><div class="value"><?php echo wp_get_current_user()->display_name; ?></div></div>
                <div class="card"><div class="label">تاريخ التقرير</div><div class="value">${new Date().toLocaleDateString('ar-EG')}</div></div>
                <div class="card"><div class="label">العمر</div><div class="value">${age} عاماً</div></div>
                <div class="card"><div class="label">الوزن / الطول</div><div class="value">${weight} كجم / ${height} سم</div></div>
            </div>

            <div class="bmi-result">
                <div class="label" style="color: rgba(255,255,255,0.8);">مؤشر كتلة الجسم الخاص بك</div>
                <div class="bmi-value">${bmi}</div>
                <div class="bmi-status">${status}</div>
            </div>

            <div class="tips-section">
                <div class="tips-title">💡 الإرشادات والتوصيات الصحية:</div>
                <div id="tips-content">${document.getElementById('bmi-tip').innerHTML}</div>
            </div>

            <div class="footer">
                صدر هذا التقرير آلياً عبر نظام Workedia. يرجى استشارة طبيب مختص قبل البدء بأي برنامج غذائي أو رياضي مكثف.
            </div>

            <script>window.print();<\/script>
        </body>
        </html>
    `;
    reportWindow.document.write(content);
    reportWindow.document.close();
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

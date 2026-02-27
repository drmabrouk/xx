<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container calculator-app" dir="ltr">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; direction: rtl;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">الحاسبة المتطورة</h2>
    </div>

    <div class="calc-layout" style="display: grid; grid-template-columns: 1fr 300px; gap: 20px; max-width: 900px; margin: 0 auto;">
        <!-- Calculator Body -->
        <div class="calc-main" style="background: #ffffff; border-radius: 24px; padding: 25px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); border: 1px solid #e2e8f0;">
            <div class="calc-display" style="background: #f8fafc; border-radius: 16px; padding: 20px; margin-bottom: 20px; text-align: right; min-height: 100px; display: flex; flex-direction: column; justify-content: flex-end; border: 1px solid #edf2f7;">
                <div id="calc-expression" style="color: #94a3b8; font-size: 1em; margin-bottom: 5px; font-family: monospace; letter-spacing: 1px;"></div>
                <div id="calc-result" style="color: #1a202c; font-size: 2.2em; font-weight: 800; overflow: hidden; font-family: monospace;">0</div>
            </div>

            <div class="calc-buttons" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px;">
                <!-- Row 1: Advanced Functions -->
                <button class="calc-btn func" onclick="calcAction('clear')">AC</button>
                <button class="calc-btn func" onclick="calcAction('delete')">DEL</button>
                <button class="calc-btn func" onclick="calcAction('operator', '%')">%</button>
                <button class="calc-btn op" onclick="calcAction('operator', '/')">÷</button>

                <!-- Row 2 -->
                <button class="calc-btn" onclick="calcAction('number', '7')">7</button>
                <button class="calc-btn" onclick="calcAction('number', '8')">8</button>
                <button class="calc-btn" onclick="calcAction('number', '9')">9</button>
                <button class="calc-btn op" onclick="calcAction('operator', '*')">×</button>

                <!-- Row 3 -->
                <button class="calc-btn" onclick="calcAction('number', '4')">4</button>
                <button class="calc-btn" onclick="calcAction('number', '5')">5</button>
                <button class="calc-btn" onclick="calcAction('number', '6')">6</button>
                <button class="calc-btn op" onclick="calcAction('operator', '-')">-</button>

                <!-- Row 4 -->
                <button class="calc-btn" onclick="calcAction('number', '1')">1</button>
                <button class="calc-btn" onclick="calcAction('number', '2')">2</button>
                <button class="calc-btn" onclick="calcAction('number', '3')">3</button>
                <button class="calc-btn op" onclick="calcAction('operator', '+')">+</button>

                <!-- Row 5 -->
                <button class="calc-btn" onclick="calcAction('number', '0')">0</button>
                <button class="calc-btn" onclick="calcAction('number', '.')">.</button>
                <button class="calc-btn" style="grid-column: span 2; background: var(--workedia-primary-color); color: #fff;" onclick="calcAction('equals')">=</button>
            </div>

            <div class="calc-advanced-tools" style="margin-top: 25px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                <button class="calc-tool-btn" onclick="toggleConverter('finance')"><span class="dashicons dashicons-chart-area"></span> مالية</button>
                <button class="calc-tool-btn" onclick="toggleConverter('math')"><span class="dashicons dashicons-calculator"></span> رياضيات</button>
                <button class="calc-tool-btn" onclick="toggleConverter('units')"><span class="dashicons dashicons-randomize"></span> تحويلات</button>
            </div>
        </div>

        <!-- Sidebar: History & Tools -->
        <div class="calc-sidebar" style="background: #fff; border-radius: 24px; padding: 25px; border: 1px solid var(--workedia-border-color); display: flex; flex-direction: column; overflow: hidden;">
            <div id="calc-history-panel" style="flex: 1; display: flex; flex-direction: column;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; direction: rtl; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                    <h4 style="margin: 0; font-weight: 800;">السجل</h4>
                    <button onclick="clearCalcHistory()" style="background: none; border: none; color: #e53e3e; font-size: 11px; cursor: pointer; font-weight: 700; font-family: 'Rubik';">مسح الكل</button>
                </div>
                <div id="calc-history-list" style="direction: rtl; font-family: monospace; font-size: 14px; flex: 1; overflow-y: auto;">
                    <div style="color: #94a3b8; text-align: center; margin-top: 50px;">لا توجد عمليات مسبقة</div>
                </div>
            </div>

            <!-- Specialized Panels -->
            <div id="calc-special-panel" style="display: none; direction: rtl; animation: workediaSlideUp 0.3s ease;">
                <h4 id="special-title" style="margin: 0 0 20px 0; font-weight: 800; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; color: var(--workedia-primary-color);">الأداة المتخصصة</h4>

                <div id="special-content">
                    <!-- Loaded dynamically -->
                </div>

                <button class="workedia-btn workedia-btn-outline" style="width: 100%; font-size: 12px; height: 35px; margin-top: 20px;" onclick="toggleConverter(null)">
                    <span class="dashicons dashicons-arrow-right-alt2" style="font-size: 16px;"></span> العودة لسجل العمليات
                </button>
            </div>

            <!-- Unit Converter (Hidden by default) -->
            <div id="calc-converter-panel" style="display: none; direction: rtl; animation: workediaSlideUp 0.3s ease;">
                <h4 id="converter-title" style="margin: 0 0 20px 0; font-weight: 800; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px; color: var(--workedia-primary-color);">تحويل الوحدات</h4>

                <div id="converter-category-selector" style="display: flex; gap: 5px; margin-bottom: 20px; flex-wrap: wrap;">
                    <!-- Dynamically populated -->
                </div>

                <div style="background: #f8fafc; padding: 15px; border-radius: 12px; border: 1px solid #edf2f7; margin-bottom: 15px;">
                    <div class="workedia-form-group" style="margin-bottom: 10px;">
                        <label class="workedia-label" style="font-size: 11px;">القيمة المصدر:</label>
                        <input type="number" id="convert-from-val" class="workedia-input" oninput="runConversion()" style="font-family: monospace; font-weight: 700;">
                        <select id="convert-from-unit" class="workedia-select" style="margin-top: 5px; font-size: 12px;" onchange="runConversion()"></select>
                    </div>

                    <div style="text-align: center; margin: 10px 0;">
                        <div style="width: 30px; height: 30px; background: white; border: 1px solid #e2e8f0; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; color: var(--workedia-primary-color); box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
                            <span class="dashicons dashicons-arrow-down-alt2"></span>
                        </div>
                    </div>

                    <div class="workedia-form-group" style="margin-bottom: 0;">
                        <label class="workedia-label" style="font-size: 11px;">النتيجة المحولة:</label>
                        <input type="text" id="convert-to-val" class="workedia-input" readonly style="font-family: monospace; font-weight: 700; background: #fff; color: var(--workedia-primary-color);">
                        <select id="convert-to-unit" class="workedia-select" style="margin-top: 5px; font-size: 12px;" onchange="runConversion()"></select>
                    </div>
                </div>

                <button class="workedia-btn workedia-btn-outline" style="width: 100%; font-size: 12px; height: 35px;" onclick="toggleConverter(null)">
                    <span class="dashicons dashicons-arrow-right-alt2" style="font-size: 16px;"></span> العودة لسجل العمليات
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.calc-btn {
    height: 52px; background: white; border: 1px solid #e2e8f0; border-radius: 14px;
    color: #1a202c; font-size: 1.2em; font-weight: 700; cursor: pointer; transition: all 0.2s;
}
.calc-btn:hover { background: #f8fafc; border-color: var(--workedia-primary-color); color: var(--workedia-primary-color); }
.calc-btn.op { background: #fcfcfc; color: var(--workedia-primary-color); font-size: 1.4em; }
.calc-btn.func { background: #f8fafc; color: #64748b; border-color: #e2e8f0; }

.calc-tool-btn {
    background: #f8fafc; border: 1px solid #e2e8f0; padding: 10px;
    border-radius: 10px; color: #64748b; cursor: pointer; transition: 0.2s; font-size: 12px;
    font-weight: 700; font-family: 'Rubik', sans-serif;
}
.calc-tool-btn:hover { background: #fff; color: var(--workedia-dark-color); }

.history-item { padding: 10px; border-bottom: 1px solid #f1f5f9; cursor: pointer; transition: 0.2s; border-radius: 8px; }
.history-item:hover { background: #f8fafc; color: var(--workedia-primary-color); }

/* Animation */
.calculator-app { animation: workediaFadeIn 0.5s ease; }
</style>

<script>
let calcState = {
    display: '0',
    expression: '',
    history: JSON.parse(localStorage.getItem('workedia_calc_history') || '[]')
};

window.addEventListener('load', renderHistory);

function updateDisplay() {
    document.getElementById('calc-result').innerText = calcState.display;
    document.getElementById('calc-expression').innerText = calcState.expression;
}

function calcAction(type, val) {
    if (type === 'number') {
        if (calcState.display === '0') calcState.display = val;
        else calcState.display += val;
    } else if (type === 'operator') {
        calcState.expression += calcState.display + ' ' + val + ' ';
        calcState.display = '0';
    } else if (type === 'clear') {
        calcState.display = '0';
        calcState.expression = '';
    } else if (type === 'delete') {
        calcState.display = calcState.display.slice(0, -1) || '0';
    } else if (type === 'equals') {
        try {
            const finalExpr = (calcState.expression + calcState.display).replace('×', '*').replace('÷', '/');
            // Safer evaluation replacement for simple math
            const sanitizedExpr = finalExpr.replace(/[^-()\d/*+.]/g, '');
            const result = Function('"use strict";return (' + sanitizedExpr + ')')();
            addToHistory(finalExpr + ' = ' + result);
            calcState.display = result.toString();
            calcState.expression = '';
        } catch (e) {
            calcState.display = 'Error';
        }
    }
    updateDisplay();
}

function addToHistory(item) {
    calcState.history.unshift({
        expr: item,
        time: new Date().toLocaleTimeString('ar-EG', {hour: '2-digit', minute:'2-digit'})
    });
    if (calcState.history.length > 50) calcState.history.pop();
    localStorage.setItem('workedia_calc_history', JSON.stringify(calcState.history));
    renderHistory();
}

function clearCalcHistory() {
    if (!confirm('هل تريد مسح سجل العمليات بالكامل؟')) return;
    calcState.history = [];
    localStorage.removeItem('workedia_calc_history');
    renderHistory();
}

function renderHistory() {
    const list = document.getElementById('calc-history-list');
    if (calcState.history.length === 0) {
        list.innerHTML = '<div style="color: #94a3b8; text-align: center; margin-top: 50px;">لا توجد عمليات مسبقة</div>';
        return;
    }
    list.innerHTML = calcState.history.map(h => `
        <div class="history-item" style="padding: 12px; margin-bottom: 8px; background: #f8fafc; border-radius: 12px; border: 1px solid #edf2f7; cursor: pointer;" onclick="calcState.display='${h.expr.split('=')[1].trim()}';updateDisplay()">
            <div style="font-size: 10px; color: #94a3b8; margin-bottom: 4px;">${h.time}</div>
            <div style="font-weight: 700; word-break: break-all;">${h.expr}</div>
        </div>
    `).join('');
}

const unitData = {
    units: {
        title: 'تحويل الوحدات',
        color: '#3182CE',
        categories: {
            'الطول': { 'متر': 1, 'كيلومتر': 0.001, 'سنتيمتر': 100, 'بوصة': 39.3701, 'قدم': 3.28084, 'ياردة': 1.09361 },
            'الوزن': { 'جرام': 1000, 'كيلوجرام': 1, 'باوند': 2.20462, 'أوقية': 35.274, 'طن': 0.001 },
            'المساحة': { 'متر مربع': 1, 'فدان': 0.000238, 'هكتار': 0.0001, 'سنت': 0.01 }
        }
    },
    finance: {
        title: 'أدوات مالية',
        color: '#38A169',
        categories: {
            'الضريبة': { 'المبلغ الصافي': 1, 'شامل ضريبة 14%': 1.14, 'شامل ضريبة 15%': 1.15, 'شامل ضريبة 5%': 1.05 },
            'القروض': { 'أصل المبلغ': 1, '+ فوائد 10%': 1.1, '+ فوائد 20%': 1.2 }
        }
    },
    math: {
        title: 'وظائف رياضية',
        color: '#805AD5',
        categories: {
            'النسبة المئوية': { 'القيمة الكاملة': 1, 'بعد خصم 10%': 0.9, 'بعد خصم 25%': 0.75, 'بعد خصم 50%': 0.5, 'زيادة 10%': 1.1, 'زيادة 50%': 1.5 },
            'الكسور': { 'واحد صحيح': 1, 'النصف (1/2)': 0.5, 'الربع (1/4)': 0.25, 'الثلث (1/3)': 0.333 }
        }
    }
};

let currentMode = null;
let currentCategory = null;

function toggleConverter(mode) {
    const historyPanel = document.getElementById('calc-history-panel');
    const converterPanel = document.getElementById('calc-converter-panel');

    if (!mode) {
        historyPanel.style.display = 'block';
        converterPanel.style.display = 'none';
        return;
    }

    currentMode = mode;
    historyPanel.style.display = 'none';
    converterPanel.style.display = 'block';

    const data = unitData[mode];
    const titleEl = document.getElementById('converter-title');
    titleEl.innerText = data.title;
    titleEl.style.color = data.color;

    const catSelector = document.getElementById('converter-category-selector');
    let catsHtml = '';
    let firstCat = null;

    for (let cat in data.categories) {
        if (!firstCat) firstCat = cat;
        catsHtml += `<button class="calc-cat-btn" onclick="selectCategory('${cat}')">${cat}</button>`;
    }
    catSelector.innerHTML = catsHtml;
    selectCategory(firstCat);
}

function selectCategory(cat) {
    currentCategory = cat;
    document.querySelectorAll('.calc-cat-btn').forEach(btn => {
        btn.classList.toggle('active', btn.innerText === cat);
    });

    const data = unitData[currentMode].categories[cat];
    const fromSelect = document.getElementById('convert-from-unit');
    const toSelect = document.getElementById('convert-to-unit');

    let options = '';
    for (let unit in data) {
        options += `<option value="${data[unit]}">${unit}</option>`;
    }

    fromSelect.innerHTML = options;
    toSelect.innerHTML = options;
    runConversion();
}

function runConversion() {
    const val = parseFloat(document.getElementById('convert-from-val').value) || 0;
    const fromRate = parseFloat(document.getElementById('convert-from-unit').value);
    const toRate = parseFloat(document.getElementById('convert-to-unit').value);

    // Normalize to base (1) then to target
    const result = (val / fromRate) * toRate;
    document.getElementById('convert-to-val').value = result.toLocaleString();
}
</script>

<?php if (!defined('ABSPATH')) exit; ?>
<div class="member-app-launcher">
    <div style="text-align: center; margin-bottom: 40px;">
        <div style="margin-bottom: 20px; display: inline-block; position: relative;" class="launcher-avatar-wrapper">
            <?php
            $user_id = get_current_user_id();
            global $wpdb;
            $member = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}workedia_members WHERE wp_user_id = %d", $user_id));
            $photo_url = $member->photo_url ?? '';
            $incomplete = false;
            if ($member) {
                if (empty($member->phone) || empty($member->email) || empty($member->residence_city)) {
                    $incomplete = true;
                }
            }
            ?>
            <div id="launcher-photo-container" onclick="workediaTriggerLauncherPhoto()" style="cursor: pointer; transition: 0.3s; position: relative;">
                <?php if ($photo_url): ?>
                    <img src="<?php echo esc_url($photo_url); ?>" style="width: 130px; height: 130px; border-radius: 50%; border: 5px solid white; box-shadow: 0 15px 35px rgba(0,0,0,0.12); object-fit: cover;">
                <?php else: ?>
                    <?php echo get_avatar($user_id, 130, '', '', array('style' => 'border-radius: 50%; border: 5px solid white; box-shadow: 0 15px 35px rgba(0,0,0,0.12);')); ?>
                <?php endif; ?>
                <div class="photo-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.3s;">
                    <span class="dashicons dashicons-camera" style="font-size: 30px; width: 30px; height: 30px;"></span>
                </div>
            </div>
            <input type="file" id="launcher-photo-input" style="display:none;" accept="image/*" onchange="workediaUploadLauncherPhoto(<?php echo $member->id ?? 0; ?>)">

            <?php if ($incomplete): ?>
                <div class="profile-incomplete-indicator" title="بيانات ملفك الشخصي غير مكتملة" style="position: absolute; top: 10px; right: 10px; background: #e53e3e; color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: 3px solid white; box-shadow: 0 4px 10px rgba(0,0,0,0.2); cursor: help;">
                    <span style="font-weight: 900; font-size: 18px;">!</span>
                </div>
            <?php endif; ?>
        </div>
        <h2 style="font-weight: 800; font-size: 1.8em; color: var(--workedia-dark-color); margin: 0;">أهلاً بك، <?php echo wp_get_current_user()->display_name; ?></h2>
        <p style="color: #64748b; font-size: 1em; margin-top: 5px;">
            <?php if ($incomplete): ?>
                <span style="color: #e53e3e; font-weight: 700;">يرجى استكمال بيانات ملفك الشخصي</span>
            <?php else: ?>
                اختر الخدمة التي ترغب في الوصول إليها
            <?php endif; ?>
        </p>
    </div>

    <div class="app-grid" style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 20px; padding: 10px; max-width: 1200px; margin: 0 auto;">
        <!-- Profile App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'my-profile'); ?>" class="app-card" data-hint="قم بتحديث بياناتك الشخصية، وتغيير كلمة المرور، ومتابعة حالة عضويتك في النظام.">
            <div class="app-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span class="dashicons dashicons-admin-users"></span>
            </div>
            <h3>الملف الشخصي</h3>
            <p>إدارة بياناتك الشخصية وحسابك</p>
        </a>

        <!-- Notebook App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'notebook'); ?>" class="app-card" data-hint="سجل أفكارك، ملاحظاتك السريعة، وصورك في مساحة عمل مرنة تشبه الورق اللاصق.">
            <div class="app-icon" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);">
                <span class="dashicons dashicons-edit"></span>
            </div>
            <h3>دفتر الملاحظات</h3>
            <p>تدوين الأفكار والملاحظات بسرعة</p>
        </a>

        <!-- Task List App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'task-list'); ?>" class="app-card" data-hint="نظم مهامك اليومية، حدد المواعيد النهائية، واستلم تنبيهات قبل فوات الأوان.">
            <div class="app-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <span class="dashicons dashicons-editor-ul"></span>
            </div>
            <h3>مدير المهام</h3>
            <p>تنظيم وإدارة مهامك اليومية</p>
        </a>

        <!-- Messaging App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'messaging'); ?>" class="app-card" data-hint="تواصل مباشرة مع فريق الدعم الفني والإداري، وأرسل استفساراتك وشكاواك بسهولة.">
            <div class="app-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <span class="dashicons dashicons-email"></span>
            </div>
            <h3>مركز المراسلات</h3>
            <p>التواصل المباشر مع إدارة Workedia</p>
        </a>

        <!-- Calculator App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'calculator'); ?>" class="app-card" data-hint="استخدم الحاسبة الاحترافية لإجراء العمليات المالية، الحسابية، وتحويل الوحدات بدقة.">
            <div class="app-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span class="dashicons dashicons-calculator"></span>
            </div>
            <h3>الحاسبة المتطورة</h3>
            <p>أدوات مالية وتحويلات احترافية</p>
        </a>

        <!-- Form Builder App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'form-builder'); ?>" class="app-card" data-hint="صمم نماذجك الخاصة، استطلاعات الرأي، واجمع البيانات من المستخدمين باحترافية.">
            <div class="app-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <span class="dashicons dashicons-forms"></span>
            </div>
            <h3>منشئ النماذج</h3>
            <p>إنشاء استبيانات وجمع البيانات</p>
        </a>

        <!-- BMI App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'bmi'); ?>" class="app-card" data-hint="احسب مؤشر كتلة جسمك، وتابع وزنك المثالي، واحصل على نصائح صحية مخصصة لعمرك.">
            <div class="app-icon" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);">
                <span class="dashicons dashicons-heart"></span>
            </div>
            <h3>حاسبة BMI</h3>
            <p>متابعة مؤشر كتلة الجسم والصحة</p>
        </a>

        <!-- Documents App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'documents'); ?>" class="app-card" data-hint="قم بتخزين وأرشفة وثائقك الهامة (PDF, Word, صور) في مكان آمن ومنظم.">
            <div class="app-icon" style="background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);">
                <span class="dashicons dashicons-portfolio"></span>
            </div>
            <h3>أرشيف الوثائق</h3>
            <p>حفظ وإدارة مستنداتك الهامة</p>
        </a>

        <!-- CV Builder App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'cv-builder'); ?>" class="app-card" data-hint="أنشئ سيرتك الذاتية باحترافية عالية باستخدام قوالب متوافقة مع أنظمة ATS العالمية.">
            <div class="app-icon" style="background: linear-gradient(135deg, #fdfcfb 0%, #e2d1c3 100%);">
                <span class="dashicons dashicons-id-alt"></span>
            </div>
            <h3>منشئ السيرة الذاتية</h3>
            <p>بناء CV احترافي متوافق مع ATS</p>
        </a>
    </div>
</div>

<script>
function workediaTriggerLauncherPhoto() {
    document.getElementById('launcher-photo-input').click();
}

function workediaUploadLauncherPhoto(memberId) {
    if (!memberId) return;
    const file = document.getElementById('launcher-photo-input').files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('action', 'workedia_update_member_photo');
    formData.append('member_id', memberId);
    formData.append('member_photo', file);
    formData.append('workedia_photo_nonce', '<?php echo wp_create_nonce("workedia_photo_action"); ?>');

    workediaShowNotification('جاري رفع الصورة...');

    fetch(ajaxurl, { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.getElementById('launcher-photo-container').innerHTML =
                `<img src="${res.data.photo_url}" style="width: 130px; height: 130px; border-radius: 50%; border: 5px solid white; box-shadow: 0 15px 35px rgba(0,0,0,0.12); object-fit: cover;">` +
                `<div class="photo-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.3s;">` +
                `<span class="dashicons dashicons-camera" style="font-size: 30px; width: 30px; height: 30px;"></span></div>`;
            workediaShowNotification('تم تحديث الصورة الشخصية بنجاح');
        } else {
            alert('فشل الرفع: ' + res.data);
        }
    });
}
</script>

<style>
.launcher-avatar-wrapper:hover .photo-overlay {
    opacity: 1 !important;
}
.app-card {
    position: relative;
    background: white;
    padding: 25px 20px;
    border-radius: 24px;
    text-align: center;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
    box-shadow: 0 4px 6px rgba(0,0,0,0.02), 0 1px 3px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    align-items: center;
    border: 1px solid rgba(255,255,255,0.8);
}
.app-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.06);
    border-color: var(--workedia-primary-color);
}
.app-icon {
    width: 55px;
    height: 55px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 15px;
    box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}
.app-icon .dashicons {
    font-size: 24px;
    width: 24px;
    height: 24px;
    color: white !important;
}
.app-card h3 {
    margin: 0 0 8px 0;
    font-weight: 800;
    color: var(--workedia-dark-color);
    font-size: 1.1em;
}
.app-card p {
    margin: 0;
    color: #64748b;
    font-size: 0.9em;
    line-height: 1.4;
}
.member-mode-ui .workedia-main-panel > div {
    background: white;
    padding: 40px;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.03);
}
.member-mode-ui .member-app-launcher {
    background: transparent !important;
    padding: 0 !important;
    box-shadow: none !important;
}
</style>

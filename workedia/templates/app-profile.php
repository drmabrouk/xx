<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container profile-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
        <h2 style="margin: 0; font-weight: 900; color: var(--workedia-dark-color); font-size: 2em;">إدارة الملف الشخصي</h2>
        <button onclick="workediaSaveFullProfile()" class="workedia-btn" style="width: auto; padding: 0 40px; height: 50px; font-weight: 800; font-size: 1.1em;">حفظ كافة التغييرات</button>
    </div>

    <div class="profile-layout" style="display: grid; grid-template-columns: 350px 1fr; gap: 40px;">
        <!-- Side: Avatar & Summary -->
        <div class="profile-sidebar">
            <div style="background: white; border-radius: 24px; padding: 40px; border: 1px solid var(--workedia-border-color); box-shadow: var(--workedia-shadow); text-align: center;">
                <div style="position: relative; display: inline-block; margin-bottom: 25px;" class="launcher-avatar-wrapper">
                    <?php
                    $user_id = get_current_user_id();
                    global $wpdb;
                    $member = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}workedia_members WHERE wp_user_id = %d", $user_id));
                    $photo_url = $member->photo_url ?? '';
                    ?>
                    <div id="profile-photo-container" onclick="workediaTriggerLauncherPhoto()" style="cursor: pointer; transition: 0.3s; position: relative;">
                        <?php if ($photo_url): ?>
                            <img src="<?php echo esc_url($photo_url); ?>" style="width: 180px; height: 180px; border-radius: 50%; border: 6px solid white; box-shadow: 0 15px 35px rgba(0,0,0,0.1); object-fit: cover;">
                        <?php else: ?>
                            <?php echo get_avatar($user_id, 180, '', '', array('style' => 'border-radius: 50%; border: 6px solid white; box-shadow: 0 15px 35px rgba(0,0,0,0.1);')); ?>
                        <?php endif; ?>
                        <div class="photo-overlay" style="position: absolute; inset: 0; background: rgba(0,0,0,0.4); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; opacity: 0; transition: 0.3s;">
                            <span class="dashicons dashicons-camera" style="font-size: 40px; width: 40px; height: 40px;"></span>
                        </div>
                    </div>
                </div>
                <h3 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);"><?php echo wp_get_current_user()->display_name; ?></h3>
                <p style="color: #64748b; font-size: 14px; margin-top: 5px;"><?php echo $member->membership_number ?: 'عضو غير مسجل'; ?></p>
                <div style="margin-top: 25px; padding-top: 25px; border-top: 1px solid #f1f5f9; display: grid; gap: 15px;">
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 11px; color: #94a3b8; font-weight: 800; margin-bottom: 5px;">حالة الحساب</span>
                        <span class="workedia-badge workedia-badge-high" style="background: #e6fffa; color: #319795; padding: 6px 15px; font-weight: 800;">نشط</span>
                    </div>
                    <div style="text-align: right;">
                        <span style="display: block; font-size: 11px; color: #94a3b8; font-weight: 800; margin-bottom: 5px;">تاريخ الانضمام</span>
                        <span style="font-weight: 700; color: var(--workedia-dark-color);"><?php echo date_i18n('j F Y', strtotime($member->registration_date)); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content: Forms -->
        <div class="profile-main-content" style="display: grid; gap: 30px;">
            <div style="background: white; border-radius: 24px; padding: 40px; border: 1px solid var(--workedia-border-color); box-shadow: var(--workedia-shadow);">
                <h4 style="margin: 0 0 25px 0; font-weight: 800; color: var(--workedia-dark-color); border-bottom: 2px solid #f8fafc; padding-bottom: 15px;">البيانات الشخصية</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="workedia-form-group">
                        <label class="workedia-label">الاسم الأول:</label>
                        <input type="text" id="prof-first-name" class="workedia-input" value="<?php echo esc_attr($member->first_name); ?>">
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label">اسم العائلة:</label>
                        <input type="text" id="prof-last-name" class="workedia-input" value="<?php echo esc_attr($member->last_name); ?>">
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label">البريد الإلكتروني:</label>
                        <input type="email" id="prof-email" class="workedia-input" value="<?php echo esc_attr($member->email); ?>" disabled style="background: #f8fafc; cursor: not-allowed;">
                        <small style="color: #94a3b8;">يرجى التواصل مع الإدارة لتغيير البريد الإلكتروني المعتمد.</small>
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label">رقم الهاتف:</label>
                        <input type="text" id="prof-phone" class="workedia-input" value="<?php echo esc_attr($member->phone); ?>">
                    </div>
                    <div class="workedia-form-group" style="grid-column: span 2;">
                        <label class="workedia-label">العنوان بالتفصيل:</label>
                        <textarea id="prof-address" class="workedia-textarea" rows="3"><?php echo esc_textarea($member->residence_street); ?></textarea>
                    </div>
                </div>
            </div>

            <div style="background: white; border-radius: 24px; padding: 40px; border: 1px solid var(--workedia-border-color); box-shadow: var(--workedia-shadow);">
                <h4 style="margin: 0 0 25px 0; font-weight: 800; color: var(--workedia-dark-color); border-bottom: 2px solid #f8fafc; padding-bottom: 15px;">أمان الحساب</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div class="workedia-form-group">
                        <label class="workedia-label">كلمة مرور جديدة:</label>
                        <input type="password" id="prof-pass" class="workedia-input" placeholder="اتركها فارغة لعدم التغيير">
                    </div>
                    <div class="workedia-form-group">
                        <label class="workedia-label">تأكيد كلمة المرور:</label>
                        <input type="password" id="prof-pass-conf" class="workedia-input">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function workediaSaveFullProfile() {
    const pass = document.getElementById('prof-pass').value;
    const conf = document.getElementById('prof-pass-conf').value;

    if (pass && pass !== conf) {
        return alert('كلمات المرور غير متطابقة');
    }

    const fd = new FormData();
    fd.append('action', 'workedia_update_profile_ajax');
    fd.append('first_name', document.getElementById('prof-first-name').value);
    fd.append('last_name', document.getElementById('prof-last-name').value);
    fd.append('phone', document.getElementById('prof-phone').value);
    fd.append('residence_street', document.getElementById('prof-address').value);
    if (pass) fd.append('user_pass', pass);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_profile_action"); ?>');

    workediaShowNotification('جاري حفظ البيانات...');

    fetch(ajaxurl, { method: 'POST', body: fd })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            workediaShowNotification('تم تحديث الملف الشخصي بنجاح');
            setTimeout(() => location.reload(), 1000);
        } else {
            alert('خطأ: ' + res.data);
        }
    });
}
</script>

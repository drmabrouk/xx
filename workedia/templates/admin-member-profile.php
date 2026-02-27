<?php if (!defined('ABSPATH')) exit;

$member_id = intval($_GET['member_id'] ?? 0);
$member = Workedia_DB::get_member_by_id($member_id);

if (!$member) {
    echo '<div class="error"><p>العضو غير موجود.</p></div>';
    return;
}

$user = wp_get_current_user();
$is_sys_manager = in_array('administrator', (array)$user->roles);
$is_administrator = in_array('administrator', (array)$user->roles);
$is_subscriber = in_array('subscriber', (array)$user->roles);

// IDOR CHECK: Restricted users can only see their own profile
if ($is_subscriber && !current_user_can('manage_options')) {
    if ($member->wp_user_id != $user->ID) {
        echo '<div class="error" style="padding:20px; background:#fff5f5; color:#c53030; border-radius:8px; border:1px solid #feb2b2;"><h4>⚠️ عذراً، لا تملك صلاحية الوصول لهذا الملف.</h4><p>لا يمكنك استعراض بيانات الأعضاء الآخرين.</p></div>';
        return;
    }
}

$statuses = Workedia_Settings::get_membership_statuses();
$member_mode = isset($member_mode) ? $member_mode : false; // Check if passed from parent
?>

<div class="workedia-member-profile-view <?php echo $member_mode ? 'workedia-app-container' : ''; ?>" dir="rtl">
    <?php if (!$member_mode): ?>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: #fff; padding: 20px; border-radius: 12px; border: 1px solid var(--workedia-border-color); box-shadow: var(--workedia-shadow);">
        <div style="display: flex; align-items: center; gap: 20px;">
            <div style="position: relative;">
                <div id="member-photo-container" style="width: 80px; height: 80px; background: #f0f4f8; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 40px; border: 3px solid var(--workedia-primary-color); overflow: hidden;">
                    <?php if ($member->photo_url): ?>
                        <img src="<?php echo esc_url($member->photo_url); ?>" style="width:100%; height:100%; object-fit:cover;">
                    <?php else: ?>
                        👤
                    <?php endif; ?>
                </div>
                <button onclick="workediaTriggerPhotoUpload()" style="position: absolute; bottom: 0; right: 0; background: var(--workedia-primary-color); color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; box-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                    <span class="dashicons dashicons-camera" style="font-size: 14px; width: 14px; height: 14px;"></span>
                </button>
                <input type="file" id="member-photo-input" style="display:none;" accept="image/*" onchange="workediaUploadMemberPhoto(<?php echo $member->id; ?>)">
            </div>
            <div>
                <h2 style="margin:0; color: var(--workedia-dark-color);"><?php echo esc_html($member->first_name . ' ' . $member->last_name); ?></h2>
            </div>
        </div>
        <div style="display: flex; gap: 10px; align-items: center;">
            <?php if (!$is_member): ?>
                <button onclick="workediaEditMember(JSON.parse(this.dataset.member))" data-member='<?php echo esc_attr(wp_json_encode($member)); ?>' class="workedia-btn" style="background: #3182ce; width: auto;"><span class="dashicons dashicons-edit"></span> تعديل البيانات</button>
            <?php endif; ?>

            <?php if (!$is_subscriber || current_user_can('manage_options')): ?>
                <a href="<?php echo admin_url('admin-ajax.php?action=workedia_print&print_type=id_card&member_id='.$member->id); ?>" target="_blank" class="workedia-btn" style="background: #27ae60; width: auto; text-decoration:none; display:flex; align-items:center; gap:8px;"><span class="dashicons dashicons-id-alt"></span> طباعة الكارنيه</a>
            <?php endif; ?>
            <?php if ($is_sys_manager): ?>
                <button onclick="deleteMember(<?php echo $member->id; ?>, '<?php echo esc_js($member->first_name . ' ' . $member->last_name); ?>')" class="workedia-btn" style="background: #e53e3e; width: auto;"><span class="dashicons dashicons-trash"></span> حذف العضو</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Profile Tabs -->
    <?php if (!$member_mode): ?>
    <div class="workedia-tabs-wrapper" style="display: flex; gap: 10px; margin-bottom: 25px; border-bottom: 2px solid #eee; padding-bottom: 10px;">
        <button class="workedia-tab-btn workedia-active" onclick="workediaOpenInternalTab('profile-info', this)"><span class="dashicons dashicons-admin-users"></span> بيانات العضوية</button>
        <button class="workedia-tab-btn" onclick="workediaOpenInternalTab('member-chat', this); setTimeout(() => selectConversation(<?php echo $member->id; ?>, '<?php echo esc_js($member->first_name . ' ' . $member->last_name); ?>', <?php echo $member->wp_user_id ?: 0; ?>), 100);"><span class="dashicons dashicons-email"></span> المراسلات والشكاوى</button>
    </div>
    <?php endif; ?>

    <div id="profile-info" class="workedia-internal-tab">
        <div style="display: grid; grid-template-columns: 1fr; gap: 30px;">
            <div style="display: flex; flex-direction: column; gap: 30px;">
                <!-- Basic Info -->
                <div style="background: #fff; padding: 25px; border-radius: 12px; border: 1px solid var(--workedia-border-color); box-shadow: var(--workedia-shadow);">
                <h3 style="margin-top:0; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">البيانات الأساسية</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div><label class="workedia-label">الاسم الأول:</label> <div class="workedia-value"><?php echo esc_html($member->first_name); ?></div></div>
                    <div><label class="workedia-label">اسم العائلة:</label> <div class="workedia-value"><?php echo esc_html($member->last_name); ?></div></div>
                    <div><label class="workedia-label">اسم المستخدم:</label> <div class="workedia-value"><?php echo esc_html($member->username); ?></div></div>
                    <div><label class="workedia-label">كود العضوية:</label> <div class="workedia-value"><?php echo esc_html($member->membership_number); ?></div></div>
                    <div><label class="workedia-label">رقم الهاتف:</label> <div class="workedia-value"><?php echo esc_html($member->phone); ?></div></div>
                    <div><label class="workedia-label">البريد الإلكتروني:</label> <div class="workedia-value"><?php echo esc_html($member->email); ?></div></div>
                </div>


                <h4 style="margin: 20px 0 10px 0; color: var(--workedia-primary-color);">بيانات السكن والاتصال</h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div><label class="workedia-label">المدينة:</label> <div class="workedia-value"><?php echo esc_html($member->residence_city); ?></div></div>
                    <div style="grid-column: span 2;"><label class="workedia-label">العنوان (الشارع):</label> <div class="workedia-value"><?php echo esc_html($member->residence_street); ?></div></div>
                    <?php if (!$member_mode && $member->wp_user_id): ?>
                        <?php $temp_pass = get_user_meta($member->wp_user_id, 'workedia_temp_pass', true); if ($temp_pass): ?>
                            <div style="grid-column: span 2; background: #fffaf0; padding: 15px; border-radius: 8px; border: 1px solid #feebc8; margin-top: 10px;">
                                <label class="workedia-label" style="color: #744210;">كلمة المرور المؤقتة للنظام:</label>
                                <div style="font-family: monospace; font-size: 1.2em; font-weight: 700; color: #975a16;"><?php echo esc_html($temp_pass); ?></div>
                                <small style="color: #975a16;">* يرجى تزويد العضو بهذه الكلمة ليتمكن من الدخول لأول مرة.</small>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <?php if ($member_mode): ?>
                <div style="margin-top: 30px; text-align: center;">
                    <button onclick="workediaEditMember(<?php echo esc_attr(wp_json_encode($member)); ?>)" class="workedia-btn" style="width: auto; height: 50px; padding: 0 40px; font-weight: 800; font-size: 1.1em;">
                        تحديث الملف الشخصي
                    </button>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <!-- Communication Tab -->
    <?php if (!$member_mode): ?>
    <div id="member-chat" class="workedia-internal-tab" style="display: none;">
        <div style="height: 600px; border: 1px solid #eee; border-radius: 12px; overflow: hidden; background: #fff;">
            <?php
            // Reuse messaging-center but in a compact way
            include WORKEDIA_PLUGIN_DIR . 'templates/messaging-center.php';
            ?>
        </div>
    </div>

    <?php endif; ?>

    <!-- Edit Member Modal -->
    <div id="edit-member-modal" class="workedia-modal-overlay">
        <div class="workedia-modal-content" style="max-width: 800px; padding: 0; overflow: hidden; border-radius: 24px;">
            <div class="workedia-modal-header" style="padding: 25px 30px; margin: 0; background: #fafafa;">
                <h3 style="font-weight: 800;">تحديث الملف الشخصي</h3>
                <button class="workedia-modal-close" onclick="document.getElementById('edit-member-modal').style.display='none'">&times;</button>
            </div>

            <form id="edit-member-form">
                <input type="hidden" name="member_id" id="edit_member_id_hidden">

                <div class="profile-edit-steps" style="padding: 30px;">
                    <!-- Step 1: Basic -->
                    <div class="profile-step" id="p-step-basic">
                        <h4 style="margin: 0 0 20px 0; color: var(--workedia-primary-color); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">1. البيانات الشخصية</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="workedia-form-group"><label class="workedia-label">الاسم الأول:</label><input name="first_name" id="edit_first_name" type="text" class="workedia-input" required></div>
                            <div class="workedia-form-group"><label class="workedia-label">اسم العائلة:</label><input name="last_name" id="edit_last_name" type="text" class="workedia-input" required></div>
                            <div class="workedia-form-group" style="grid-column: span 2;"><label class="workedia-label">اسم المستخدم (لا يمكن تغييره):</label><input name="username" id="edit_username" type="text" class="workedia-input" readonly style="background: #f8fafc;"></div>
                        </div>
                        <div style="text-align: left; margin-top: 20px;">
                            <button type="button" onclick="showProfileStep('contact')" class="workedia-btn" style="width: auto;">التالي: بيانات الاتصال <span class="dashicons dashicons-arrow-left-alt"></span></button>
                        </div>
                    </div>

                    <!-- Step 2: Contact -->
                    <div class="profile-step" id="p-step-contact" style="display: none;">
                        <h4 style="margin: 0 0 20px 0; color: var(--workedia-primary-color); border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">2. بيانات الاتصال والسكن</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="workedia-form-group"><label class="workedia-label">المدينة:</label><input name="residence_city" id="edit_res_city" type="text" class="workedia-input"></div>
                            <div class="workedia-form-group"><label class="workedia-label">رقم الهاتف:</label><input name="phone" id="edit_phone" type="text" class="workedia-input"></div>
                            <div class="workedia-form-group" style="grid-column: span 2;"><label class="workedia-label">العنوان بالتفصيل:</label><input name="residence_street" id="edit_res_street" type="text" class="workedia-input"></div>
                            <div class="workedia-form-group" style="grid-column: span 2;"><label class="workedia-label">البريد الإلكتروني:</label><input name="email" id="edit_email" type="email" class="workedia-input"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
                            <button type="button" onclick="showProfileStep('basic')" class="workedia-btn workedia-btn-outline" style="width: auto;"><span class="dashicons dashicons-arrow-right-alt"></span> السابق</button>
                            <button type="submit" class="workedia-btn" style="width: auto; background: #27ae60;">حفظ كافة التغييرات <span class="dashicons dashicons-yes"></span></button>
                        </div>
                    </div>
                </div>
                <?php wp_nonce_field('workedia_add_member', 'workedia_nonce'); ?>
            </form>
        </div>
    </div>
</div>

<script>
function workediaTriggerPhotoUpload() {
    document.getElementById('member-photo-input').click();
}

function workediaUploadMemberPhoto(memberId) {
    const file = document.getElementById('member-photo-input').files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('action', 'workedia_update_member_photo');
    formData.append('member_id', memberId);
    formData.append('member_photo', file);
    formData.append('workedia_photo_nonce', '<?php echo wp_create_nonce("workedia_photo_action"); ?>');

    fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            document.getElementById('member-photo-container').innerHTML = `<img src="${res.data.photo_url}" style="width:100%; height:100%; object-fit:cover;">`;
            workediaShowNotification('تم تحديث الصورة الشخصية');
        } else {
            alert('فشل الرفع: ' + res.data);
        }
    });
}

function deleteMember(id, name) {
    if (!confirm('هل أنت متأكد من حذف العضو: ' + name + ' نهائياً من النظام؟ لا يمكن التراجع عن هذا الإجراء.')) return;
    const formData = new FormData();
    formData.append('action', 'workedia_delete_member_ajax');
    formData.append('member_id', id);
    formData.append('nonce', '<?php echo wp_create_nonce("workedia_delete_member"); ?>');

    fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            window.location.href = '<?php echo add_query_arg('workedia_tab', 'users-management'); ?>';
        } else {
            alert('خطأ: ' + res.data);
        }
    });
}

window.showProfileStep = function(step) {
    document.querySelectorAll('.profile-step').forEach(p => p.style.display = 'none');
    document.getElementById('p-step-' + step).style.display = 'block';
};

window.workediaEditMember = function(s) {
    const f = document.getElementById('edit-member-form');
    f.elements['member_id'].value = s.id;
    f.elements['first_name'].value = s.first_name;
    f.elements['last_name'].value = s.last_name;
    f.elements['username'].value = s.username;
    f.elements['residence_city'].value = s.residence_city || '';
    f.elements['residence_street'].value = s.residence_street || '';
    f.elements['phone'].value = s.phone;
    f.elements['email'].value = s.email;
    showProfileStep('basic');
    document.getElementById('edit-member-modal').style.display = 'flex';
};

document.getElementById('edit-member-form').onsubmit = function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    formData.append('action', 'workedia_update_member_ajax');
    fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: formData })
    .then(r => r.json()).then(res => {
        if(res.success) {
            workediaShowNotification('تم تحديث البيانات بنجاح');
            setTimeout(() => location.reload(), 500);
        } else {
            alert(res.data);
        }
    });
};
</script>

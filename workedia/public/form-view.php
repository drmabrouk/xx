<?php
if (!defined('ABSPATH')) exit;

$token = $_GET['f'] ?? '';
global $wpdb;
$form = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}workedia_forms WHERE public_token = %s AND status = 'active'", $token));

if (!$form) {
    echo '<h1>عذراً، هذا النموذج غير موجود أو تم إيقافه.</h1>';
    return;
}

$fields = json_decode($form->fields, true);
?>
<div class="workedia-form-public-view" style="max-width: 700px; margin: 40px auto; background: white; padding: 40px; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.05);">
    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="margin: 0; font-weight: 800; font-size: 24px; color: #111F35;"><?php echo esc_html($form->title); ?></h1>
        <p style="color: #64748b; margin-top: 10px;"><?php echo esc_html($form->description); ?></p>
    </div>

    <div id="form-msg-container"></div>

    <form id="public-form-el">
        <?php foreach ($fields as $f): ?>
            <div class="workedia-form-group" style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 10px; font-weight: 600; font-size: 14px;"><?php echo esc_html($f['label']); ?></label>
                <?php if ($f['type'] === 'textarea'): ?>
                    <textarea name="<?php echo esc_attr($f['label']); ?>" class="workedia-textarea" rows="4"></textarea>
                <?php elseif ($f['type'] === 'number'): ?>
                    <input type="number" name="<?php echo esc_attr($f['label']); ?>" class="workedia-input">
                <?php elseif ($f['type'] === 'email'): ?>
                    <input type="email" name="<?php echo esc_attr($f['label']); ?>" class="workedia-input">
                <?php else: ?>
                    <input type="text" name="<?php echo esc_attr($f['label']); ?>" class="workedia-input">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit" class="workedia-btn" style="width: 100%; padding: 15px; height: auto;">إرسال البيانات</button>
    </form>
</div>

<script>
    document.getElementById('public-form-el').onsubmit = function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        btn.disabled = true;
        btn.innerText = 'جاري الإرسال...';

        const formData = new FormData(this);
        const submission = {};
        formData.forEach((value, key) => { submission[key] = value; });

        const ajaxData = new FormData();
        ajaxData.append('action', 'workedia_submit_public_form');
        ajaxData.append('form_id', <?php echo $form->id; ?>);
        ajaxData.append('submission', JSON.stringify(submission));

        fetch('<?php echo admin_url('admin-ajax.php'); ?>', { method: 'POST', body: ajaxData })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                document.getElementById('form-msg-container').innerHTML = `<div style="background: #F0FFF4; color: #276749; padding: 20px; border-radius: 12px; margin-bottom: 20px; border: 1px solid #C6F6D5; text-align: center;">${res.data}</div>`;
                document.getElementById('public-form-el').style.display = 'none';
            } else {
                alert('خطأ: ' + res.data);
                btn.disabled = false;
                btn.innerText = 'إرسال البيانات';
            }
        });
    };
</script>

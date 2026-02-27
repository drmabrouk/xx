<?php
if (!defined('ABSPATH')) exit;

$token = $_GET['f'] ?? '';
global $wpdb;
$form = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}workedia_forms WHERE public_token = %s AND status = 'active'", $token));

if (!$form) {
    wp_die('<h1>عذراً، هذا النموذج غير موجود أو تم إيقافه.</h1>');
}

$fields = json_decode($form->fields, true);
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($form->title); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #F63049; --dark: #111F35; }
        body { font-family: 'Rubik', sans-serif; background: #f0f2f5; color: var(--dark); margin: 0; padding: 40px 20px; }
        .form-container { max-width: 700px; margin: 0 auto; background: white; padding: 40px; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.05); }
        .header { text-align: center; margin-bottom: 40px; }
        .header h1 { margin: 0; font-weight: 800; font-size: 24px; }
        .header p { color: #64748b; margin-top: 10px; }
        .form-group { margin-bottom: 25px; }
        label { display: block; margin-bottom: 10px; font-weight: 600; font-size: 14px; }
        input, textarea, select { width: 100%; padding: 12px 15px; border: 1px solid #e2e8f0; border-radius: 12px; box-sizing: border-box; font-family: inherit; font-size: 15px; }
        input:focus { border-color: var(--primary); outline: none; }
        button { width: 100%; padding: 15px; background: var(--primary); color: white; border: none; border-radius: 12px; font-weight: 700; cursor: pointer; font-size: 16px; transition: 0.2s; }
        button:hover { opacity: 0.9; transform: translateY(-2px); }
        #msg { text-align: center; padding: 20px; border-radius: 12px; margin-bottom: 20px; display: none; }
        .success { background: #F0FFF4; color: #276749; border: 1px solid #C6F6D5; }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="header">
            <h1><?php echo esc_html($form->title); ?></h1>
            <p><?php echo esc_html($form->description); ?></p>
        </div>

        <div id="msg"></div>

        <form id="public-form">
            <?php foreach ($fields as $f): ?>
                <div class="form-group">
                    <label><?php echo esc_html($f['label']); ?></label>
                    <?php if ($f['type'] === 'textarea'): ?>
                        <textarea name="<?php echo esc_attr($f['label']); ?>" rows="4"></textarea>
                    <?php elseif ($f['type'] === 'number'): ?>
                        <input type="number" name="<?php echo esc_attr($f['label']); ?>">
                    <?php elseif ($f['type'] === 'email'): ?>
                        <input type="email" name="<?php echo esc_attr($f['label']); ?>">
                    <?php else: ?>
                        <input type="text" name="<?php echo esc_attr($f['label']); ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <button type="submit">إرسال البيانات</button>
        </form>
    </div>

    <script>
        document.getElementById('public-form').onsubmit = function(e) {
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
                    document.getElementById('msg').innerText = res.data;
                    document.getElementById('msg').className = 'success';
                    document.getElementById('msg').style.display = 'block';
                    document.getElementById('public-form').style.display = 'none';
                } else {
                    alert('خطأ: ' + res.data);
                    btn.disabled = false;
                    btn.innerText = 'إرسال البيانات';
                }
            });
        };
    </script>
</body>
</html>

<?php if (!defined('ABSPATH')) exit;

$token = sanitize_text_field($_GET['token'] ?? '');
$cv = Workedia_CVBuilder::get_public_cv($token);

if (!$cv) {
    wp_die('عذراً، لم يتم العثور على هذه السيرة الذاتية أو أنها غير متاحة للعموم.', 'خطأ في الوصول');
}

$data = json_decode($cv->content, true);
$settings = json_decode($cv->settings, true);
$lang = $cv->language;
?>
<!DOCTYPE html>
<html dir="<?php echo $lang === 'ar' ? 'rtl' : 'ltr'; ?>" lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc_html($cv->title); ?> - <?php echo esc_html($data['name']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@300;400;500;700;800;900&display=swap" rel="stylesheet">
    <link rel='stylesheet' href='<?php echo includes_url('css/dashicons.min.css'); ?>' type='text/css' media='all' />
    <style>
        body { background: #f0f2f5; margin: 0; padding: 40px 0; }
        .cv-container { max-width: 900px; margin: 0 auto; background: #fff; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .public-actions { position: fixed; bottom: 30px; left: 50%; transform: translateX(-50%); background: #fff; padding: 10px 25px; border-radius: 50px; box-shadow: 0 5px 15px rgba(0,0,0,0.2); display: flex; gap: 15px; align-items: center; z-index: 1000; }
        .action-btn { background: #F63049; color: #fff; border: none; padding: 8px 20px; border-radius: 20px; cursor: pointer; font-weight: 700; text-decoration: none; font-family: 'Rubik', sans-serif; font-size: 14px; }
        @media print { .public-actions { display: none !important; } body { background: #fff; padding: 0; } .cv-container { box-shadow: none; width: 100%; max-width: none; } }
    </style>
</head>
<body>
    <div class="cv-container">
        <?php echo Workedia_CVBuilder::render_template($cv); ?>
    </div>

    <div class="public-actions">
        <span style="font-weight: 700; font-family: 'Rubik', sans-serif; font-size: 13px;">هذه سيرة ذاتية تم إنشاؤها عبر Workedia</span>
        <button onclick="window.print()" class="action-btn">طباعة / تصدير PDF</button>
    </div>
</body>
</html>

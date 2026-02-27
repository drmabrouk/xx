<?php if (!defined('ABSPATH')) exit; ?>
<div class="cv-modern-template" style="color: #333; font-family: 'Rubik', sans-serif;">
    <header style="border-bottom: 4px solid <?php echo $settings['color']; ?>; padding-bottom: 25px; margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 style="margin: 0; color: <?php echo $settings['color']; ?>; font-size: 36pt; font-weight: 900; line-height: 1;"><?php echo esc_html($data['name']); ?></h1>
            <h2 style="margin: 10px 0 0 0; color: #555; font-size: 18pt; font-weight: 500;"><?php echo esc_html($data['job_title']); ?></h2>
        </div>
        <div style="text-align: <?php echo $lang === 'ar' ? 'left' : 'right'; ?>; font-size: 10pt; color: #666; line-height: 1.6;">
            <div><?php echo esc_html($data['email']); ?> <span class="dashicons dashicons-email" style="font-size: 12pt;"></span></div>
            <div><?php echo esc_html($data['phone']); ?> <span class="dashicons dashicons-phone" style="font-size: 12pt;"></span></div>
        </div>
    </header>

    <section class="cv-section" style="margin-bottom: 35px;">
        <h3 style="color: <?php echo $settings['color']; ?>; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; font-size: 14pt; font-weight: 800; text-transform: uppercase;">
            <?php echo $lang === 'ar' ? 'الخلاصة المهنية' : 'Professional Summary'; ?>
        </h3>
        <p style="line-height: 1.7; font-size: 11pt; margin: 0; text-align: justify;"><?php echo nl2br(esc_html($data['summary'])); ?></p>
    </section>

    <?php foreach (($data['sections'] ?? []) as $section): ?>
        <section class="cv-section" style="margin-bottom: 35px;">
            <h3 style="color: <?php echo $settings['color']; ?>; border-bottom: 1px solid #eee; padding-bottom: 8px; margin-bottom: 15px; font-size: 14pt; font-weight: 800; text-transform: uppercase;">
                <?php echo esc_html($section['title']); ?>
            </h3>
            <?php foreach (($section['items'] ?? []) as $item): ?>
                <div class="cv-item" style="margin-bottom: 20px;">
                    <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px;">
                        <strong style="font-size: 12pt; color: #111;"><?php echo esc_html($item['heading']); ?></strong>
                        <span style="font-size: 10pt; color: #888; font-weight: 500;"><?php echo esc_html($item['date']); ?></span>
                    </div>
                    <div style="font-size: 11pt; color: <?php echo $settings['color']; ?>; font-weight: 600; margin-bottom: 8px;"><?php echo esc_html($item['subheading']); ?></div>
                    <div style="line-height: 1.6; font-size: 10.5pt; color: #444;"><?php echo nl2br(esc_html($item['description'])); ?></div>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endforeach; ?>
</div>

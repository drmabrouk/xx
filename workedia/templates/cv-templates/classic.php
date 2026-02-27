<?php if (!defined('ABSPATH')) exit; ?>
<div class="cv-classic-template" style="color: #000; font-family: 'Times New Roman', serif;">
    <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px;">
        <h1 style="margin: 0; font-size: 28pt; text-transform: uppercase; letter-spacing: 2px;"><?php echo esc_html($data['name']); ?></h1>
        <div style="margin-top: 10px; font-size: 11pt;">
            <?php echo esc_html($data['email']); ?> | <?php echo esc_html($data['phone']); ?>
        </div>
        <div style="font-weight: bold; margin-top: 5px; font-size: 12pt;"><?php echo esc_html($data['job_title']); ?></div>
    </div>

    <div style="margin-bottom: 25px;">
        <h3 style="border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 10px; font-size: 13pt; font-weight: bold; text-transform: uppercase;">
            <?php echo $lang === 'ar' ? 'الهدف المهني' : 'Career Objective'; ?>
        </h3>
        <p style="line-height: 1.5; font-size: 11pt; margin: 0;"><?php echo nl2br(esc_html($data['summary'])); ?></p>
    </div>

    <?php foreach (($data['sections'] ?? []) as $section): ?>
        <div style="margin-bottom: 25px;">
            <h3 style="border-bottom: 1px solid #000; padding-bottom: 3px; margin-bottom: 10px; font-size: 13pt; font-weight: bold; text-transform: uppercase;">
                <?php echo esc_html($section['title']); ?>
            </h3>
            <?php foreach (($section['items'] ?? []) as $item): ?>
                <div style="margin-bottom: 15px;">
                    <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 11pt;">
                        <span><?php echo esc_html($item['heading']); ?></span>
                        <span><?php echo esc_html($item['date']); ?></span>
                    </div>
                    <div style="font-style: italic; margin-bottom: 5px; font-size: 11pt;"><?php echo esc_html($item['subheading']); ?></div>
                    <div style="line-height: 1.4; font-size: 10.5pt;"><?php echo nl2br(esc_html($item['description'])); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>

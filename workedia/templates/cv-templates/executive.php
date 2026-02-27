<?php if (!defined('ABSPATH')) exit; ?>
<div class="cv-executive-template" style="display: grid; grid-template-columns: 30% 70%; min-height: 250mm; font-family: 'Rubik', sans-serif;">
    <!-- Sidebar -->
    <div style="background: #2d3748; color: #fff; padding: 30px; display: flex; flex-direction: column; gap: 40px;">
        <div style="text-align: center;">
            <div style="width: 120px; height: 120px; background: #4a5568; border-radius: 50%; margin: 0 auto 20px; border: 4px solid <?php echo $settings['color']; ?>; overflow: hidden; display: flex; align-items: center; justify-content: center; font-size: 50px;">
                👤
            </div>
            <h1 style="font-size: 18pt; font-weight: 800; margin: 0; line-height: 1.2;"><?php echo esc_html($data['name']); ?></h1>
            <p style="color: <?php echo $settings['color']; ?>; font-weight: 600; margin-top: 10px;"><?php echo esc_html($data['job_title']); ?></p>
        </div>

        <div>
            <h4 style="border-bottom: 2px solid <?php echo $settings['color']; ?>; padding-bottom: 10px; margin-bottom: 20px; font-size: 11pt; text-transform: uppercase;">
                <?php echo $lang === 'ar' ? 'معلومات التواصل' : 'Contact Info'; ?>
            </h4>
            <div style="font-size: 9pt; display: flex; flex-direction: column; gap: 12px; opacity: 0.9;">
                <div><span class="dashicons dashicons-email"></span> <?php echo esc_html($data['email']); ?></div>
                <div><span class="dashicons dashicons-phone"></span> <?php echo esc_html($data['phone']); ?></div>
            </div>
        </div>

        <?php
        // Display a specific "Skills" section if it exists in data
        $skills_section = null;
        foreach(($data['sections'] ?? []) as $idx => $s) {
            if (stripos($s['title'], 'Skills') !== false || stripos($s['title'], 'المهارات') !== false) {
                $skills_section = $s;
                break;
            }
        }
        if ($skills_section): ?>
            <div>
                <h4 style="border-bottom: 2px solid <?php echo $settings['color']; ?>; padding-bottom: 10px; margin-bottom: 20px; font-size: 11pt; text-transform: uppercase;">
                    <?php echo esc_html($skills_section['title']); ?>
                </h4>
                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php foreach(($skills_section['items'] ?? []) as $item): ?>
                        <span style="background: rgba(255,255,255,0.1); padding: 4px 10px; border-radius: 4px; font-size: 8.5pt; border-right: 3px solid <?php echo $settings['color']; ?>;">
                            <?php echo esc_html($item['heading']); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Main Content -->
    <div style="background: #fff; padding: 40px; color: #333;">
        <div style="margin-bottom: 40px;">
            <h3 style="color: #2d3748; border-bottom: 2px solid #edf2f7; padding-bottom: 10px; margin-bottom: 15px; font-size: 14pt; font-weight: 800;">
                <?php echo $lang === 'ar' ? 'الملف الشخصي' : 'Professional Profile'; ?>
            </h3>
            <p style="line-height: 1.8; font-size: 10.5pt; color: #4a5568; margin: 0;"><?php echo nl2br(esc_html($data['summary'])); ?></p>
        </div>

        <?php foreach (($data['sections'] ?? []) as $section):
            if ($section === $skills_section) continue; // Already in sidebar
        ?>
            <div style="margin-bottom: 40px;">
                <h3 style="color: #2d3748; border-bottom: 2px solid #edf2f7; padding-bottom: 10px; margin-bottom: 20px; font-size: 14pt; font-weight: 800;">
                    <?php echo esc_html($section['title']); ?>
                </h3>
                <?php foreach (($section['items'] ?? []) as $item): ?>
                    <div style="margin-bottom: 25px; position: relative; padding-<?php echo $lang === 'ar' ? 'right' : 'left'; ?>: 20px; border-<?php echo $lang === 'ar' ? 'right' : 'left'; ?>: 2px solid #edf2f7;">
                        <div style="display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 5px;">
                            <strong style="font-size: 11.5pt; color: #1a202c;"><?php echo esc_html($item['heading']); ?></strong>
                            <span style="font-size: 9.5pt; color: <?php echo $settings['color']; ?>; font-weight: 700;"><?php echo esc_html($item['date']); ?></span>
                        </div>
                        <div style="font-size: 10.5pt; color: #718096; font-weight: 600; margin-bottom: 10px;"><?php echo esc_html($item['subheading']); ?></div>
                        <div style="line-height: 1.6; font-size: 10pt; color: #4a5568;"><?php echo nl2br(esc_html($item['description'])); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

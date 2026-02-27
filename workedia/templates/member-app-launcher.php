<?php if (!defined('ABSPATH')) exit; ?>
<div class="member-app-launcher">
    <div style="text-align: center; margin-bottom: 50px;">
        <h2 style="font-weight: 800; font-size: 2em; color: var(--workedia-dark-color);">أهلاً بك، <?php echo wp_get_current_user()->display_name; ?></h2>
        <p style="color: #64748b; font-size: 1.1em;">اختر الخدمة التي ترغب في الوصول إليها</p>
    </div>

    <div class="app-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 30px; padding: 10px;">
        <!-- Profile App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'my-profile'); ?>" class="app-card">
            <div class="app-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <span class="dashicons dashicons-admin-users"></span>
            </div>
            <h3>الملف الشخصي</h3>
            <p>إدارة بياناتك الشخصية وحسابك</p>
        </a>

        <!-- Notebook App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'notebook'); ?>" class="app-card">
            <div class="app-icon" style="background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%);">
                <span class="dashicons dashicons-edit"></span>
            </div>
            <h3>دفتر الملاحظات</h3>
            <p>تدوين الأفكار والملاحظات بسرعة</p>
        </a>

        <!-- Task List App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'task-list'); ?>" class="app-card">
            <div class="app-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <span class="dashicons dashicons-editor-ul"></span>
            </div>
            <h3>مدير المهام</h3>
            <p>تنظيم وإدارة مهامك اليومية</p>
        </a>

        <!-- Messaging App -->
        <a href="<?php echo add_query_arg('workedia_tab', 'messaging'); ?>" class="app-card">
            <div class="app-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <span class="dashicons dashicons-email"></span>
            </div>
            <h3>مركز المراسلات</h3>
            <p>التواصل المباشر مع إدارة Workedia</p>
        </a>
    </div>
</div>

<style>
.app-card {
    background: white;
    padding: 35px 25px;
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
    width: 70px;
    height: 70px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.app-icon .dashicons {
    font-size: 32px;
    width: 32px;
    height: 32px;
    color: white !important;
}
.app-card h3 {
    margin: 0 0 10px 0;
    font-weight: 800;
    color: var(--workedia-dark-color);
    font-size: 1.2em;
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

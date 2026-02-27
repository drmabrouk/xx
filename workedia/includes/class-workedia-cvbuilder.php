<?php

if (!class_exists('Workedia_CVBuilder')) {
    class Workedia_CVBuilder {
        public static function get_cvs($user_id) {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_cvs WHERE user_id = %d ORDER BY updated_at DESC",
                $user_id
            ));
        }

        public static function get_cv($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_cvs WHERE id = %d AND user_id = %d",
                $id, $user_id
            ));
        }

        public static function get_public_cv($token) {
            global $wpdb;
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_cvs WHERE public_token = %s AND is_public = 1",
                $token
            ));
        }

        public static function save_cv($data) {
            global $wpdb;
            $user_id = get_current_user_id();
            $table = $wpdb->prefix . 'workedia_cvs';

            $cv_id = !empty($data['id']) ? intval($data['id']) : null;

            $content = !empty($data['content']) ? (is_array($data['content']) ? json_encode($data['content']) : $data['content']) : json_encode([]);
            $settings = !empty($data['settings']) ? (is_array($data['settings']) ? json_encode($data['settings']) : $data['settings']) : json_encode([]);

            $save_data = [
                'title' => sanitize_text_field($data['title'] ?? 'بدون عنوان'),
                'language' => in_array($data['language'] ?? 'ar', ['ar', 'en']) ? $data['language'] : 'ar',
                'template' => sanitize_text_field($data['template'] ?? 'modern'),
                'content' => $content,
                'settings' => $settings,
                'is_public' => !empty($data['is_public']) ? 1 : 0
            ];

            if ($cv_id) {
                $wpdb->update($table, $save_data, ['id' => $cv_id, 'user_id' => $user_id]);
                self::save_version($cv_id, $content, 'التحديث اليدوي');
                return $cv_id;
            } else {
                $save_data['user_id'] = $user_id;
                $save_data['public_token'] = wp_generate_password(20, false);
                $wpdb->insert($table, $save_data);
                $new_id = $wpdb->insert_id;
                self::save_version($new_id, $content, 'النسخة الأولى');
                return $new_id;
            }
        }

        public static function delete_cv($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $wpdb->delete("{$wpdb->prefix}workedia_cv_versions", ['cv_id' => $id]);
            return $wpdb->delete("{$wpdb->prefix}workedia_cvs", ['id' => $id, 'user_id' => $user_id]);
        }

        public static function save_version($cv_id, $content, $label = '') {
            global $wpdb;
            return $wpdb->insert("{$wpdb->prefix}workedia_cv_versions", [
                'cv_id' => $cv_id,
                'content' => $content,
                'version_label' => sanitize_text_field($label),
                'created_at' => current_time('mysql')
            ]);
        }

        public static function get_versions($cv_id) {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_cv_versions WHERE cv_id = %d ORDER BY created_at DESC LIMIT 10",
                $cv_id
            ));
        }

        public static function restore_version($version_id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $version = $wpdb->get_row($wpdb->prepare(
                "SELECT v.* FROM {$wpdb->prefix}workedia_cv_versions v
                 JOIN {$wpdb->prefix}workedia_cvs c ON v.cv_id = c.id
                 WHERE v.id = %d AND c.user_id = %d",
                $version_id, $user_id
            ));

            if ($version) {
                return $wpdb->update("{$wpdb->prefix}workedia_cvs", ['content' => $version->content], ['id' => $version->cv_id]);
            }
            return false;
        }

        public static function render_template($cv) {
            $data = json_decode($cv->content, true);
            $settings = json_decode($cv->settings, true);
            $lang = $cv->language;
            $tpl = $cv->template;

            $file = WORKEDIA_PLUGIN_DIR . "templates/cv-templates/{$tpl}.php";
            if (!file_exists($file)) $file = WORKEDIA_PLUGIN_DIR . "templates/cv-templates/modern.php";

            ob_start();
            include $file;
            return ob_get_clean();
        }
    }
}

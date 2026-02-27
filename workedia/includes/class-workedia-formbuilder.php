<?php

if (!class_exists('Workedia_FormBuilder')) {
    class Workedia_FormBuilder {
        public static function get_forms($user_id) {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT f.*, (SELECT COUNT(*) FROM {$wpdb->prefix}workedia_form_submissions s WHERE s.form_id = f.id) as response_count
                 FROM {$wpdb->prefix}workedia_forms f WHERE f.user_id = %d ORDER BY f.created_at DESC",
                $user_id
            ));
        }

        public static function save_form($data) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_forms';
            $user_id = get_current_user_id();

            $form_id = !empty($data['id']) ? intval($data['id']) : null;
            $insert_data = [
                'user_id' => $user_id,
                'title' => sanitize_text_field($data['title']),
                'description' => sanitize_textarea_field($data['description'] ?? ''),
                'fields' => $data['fields'], // JSON string from JS
                'settings' => $data['settings'] ?? '{}',
                'status' => 'active'
            ];

            if ($form_id) {
                // Security Check
                $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $table WHERE id = %d", $form_id));
                if ($owner != $user_id) return false;
                $wpdb->update($table, $insert_data, ['id' => $form_id]);
                return $form_id;
            } else {
                $insert_data['public_token'] = wp_generate_password(12, false);
                $wpdb->insert($table, $insert_data);
                return $wpdb->insert_id;
            }
        }

        public static function delete_form($form_id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}workedia_forms WHERE id = %d", $form_id));
            if ($owner != $user_id) return false;

            $wpdb->delete("{$wpdb->prefix}workedia_form_submissions", ['form_id' => $form_id]);
            return $wpdb->delete("{$wpdb->prefix}workedia_forms", ['id' => $form_id]);
        }

        public static function submit_form($form_id, $submission_data) {
            global $wpdb;
            return $wpdb->insert("{$wpdb->prefix}workedia_form_submissions", [
                'form_id' => intval($form_id),
                'user_id' => get_current_user_id() ?: null,
                'submission_data' => json_encode($submission_data)
            ]);
        }

        public static function get_submissions($form_id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}workedia_forms WHERE id = %d", $form_id));
            if ($owner != $user_id) return [];

            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_form_submissions WHERE form_id = %d ORDER BY submitted_at DESC",
                $form_id
            ));
        }
    }
}

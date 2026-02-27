<?php

if (!class_exists('Workedia_BMI')) {
    class Workedia_BMI {
        public static function get_history($user_id) {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_bmi_history WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            ));
        }

        public static function save_entry($data) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_bmi_history';
            $user_id = get_current_user_id();

            $entry_id = !empty($data['id']) ? intval($data['id']) : null;
            $insert_data = [
                'user_id' => $user_id,
                'weight' => floatval($data['weight']),
                'height' => floatval($data['height']),
                'bmi' => floatval($data['bmi']),
                'classification' => sanitize_text_field($data['classification']),
                'units' => json_encode($data['units'] ?? ['w' => 'kg', 'h' => 'cm'])
            ];

            if ($entry_id) {
                // Security Check
                $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $table WHERE id = %d", $entry_id));
                if ($owner != $user_id) return false;
                $wpdb->update($table, $insert_data, ['id' => $entry_id]);
                return $entry_id;
            } else {
                $wpdb->insert($table, $insert_data);
                return $wpdb->insert_id;
            }
        }

        public static function delete_entry($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}workedia_bmi_history WHERE id = %d", $id));
            if ($owner != $user_id) return false;

            return $wpdb->delete("{$wpdb->prefix}workedia_bmi_history", ['id' => intval($id)]);
        }
    }
}

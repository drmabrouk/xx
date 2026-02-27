<?php

if (!class_exists('Workedia_TaskList')) {
    class Workedia_TaskList {
        public static function get_tasks($user_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_tasks';
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d ORDER BY deadline ASC, created_at DESC",
                $user_id
            ));
        }

        public static function get_subtasks($task_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_subtasks';
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM $table WHERE task_id = %d ORDER BY sort_order ASC",
                $task_id
            ));
        }

        public static function save_task($data) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_tasks';
            $user_id = get_current_user_id();

            $task_id = !empty($data['id']) ? intval($data['id']) : null;

            if ($task_id) {
                // Security Check
                $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $table WHERE id = %d", $task_id));
                if ($owner != $user_id) return false;

                $update_data = [];
                if (isset($data['title'])) $update_data['title'] = sanitize_text_field($data['title']);
                if (isset($data['description'])) $update_data['description'] = sanitize_textarea_field($data['description']);
                if (isset($data['task_date'])) $update_data['task_date'] = sanitize_text_field($data['task_date']);
                if (isset($data['deadline'])) $update_data['deadline'] = !empty($data['deadline']) ? sanitize_text_field($data['deadline']) : null;
                if (isset($data['reminder_at'])) $update_data['reminder_at'] = !empty($data['reminder_at']) ? sanitize_text_field($data['reminder_at']) : null;
                if (isset($data['status'])) $update_data['status'] = sanitize_text_field($data['status']);

                if (!empty($update_data)) {
                    $wpdb->update($table, $update_data, ['id' => $task_id]);
                }
                return $task_id;
            } else {
                $insert_data = [
                    'user_id' => get_current_user_id(),
                    'title' => sanitize_text_field($data['title'] ?? ''),
                    'description' => sanitize_textarea_field($data['description'] ?? ''),
                    'task_date' => sanitize_text_field($data['task_date'] ?? date('Y-m-d H:i:s')),
                    'deadline' => !empty($data['deadline']) ? sanitize_text_field($data['deadline']) : null,
                    'reminder_at' => !empty($data['reminder_at']) ? sanitize_text_field($data['reminder_at']) : null,
                    'status' => sanitize_text_field($data['status'] ?? 'pending')
                ];
                $wpdb->insert($table, $insert_data);
                return $wpdb->insert_id;
            }
        }

        public static function delete_task($task_id, $user_id) {
            global $wpdb;
            // Security Check
            $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}workedia_tasks WHERE id = %d", $task_id));
            if ($owner != $user_id) return false;

            $wpdb->delete($wpdb->prefix . 'workedia_subtasks', ['task_id' => intval($task_id)]);
            return $wpdb->delete($wpdb->prefix . 'workedia_tasks', ['id' => intval($task_id), 'user_id' => intval($user_id)]);
        }

        public static function toggle_subtask($subtask_id, $is_completed) {
            global $wpdb;
            $user_id = get_current_user_id();

            // Security Check: Verify task ownership via subtask
            $task_owner = $wpdb->get_var($wpdb->prepare(
                "SELECT t.user_id FROM {$wpdb->prefix}workedia_tasks t
                 JOIN {$wpdb->prefix}workedia_subtasks s ON t.id = s.task_id
                 WHERE s.id = %d", $subtask_id
            ));
            if ($task_owner != $user_id) return false;

            return $wpdb->update($wpdb->prefix . 'workedia_subtasks', ['is_completed' => $is_completed ? 1 : 0], ['id' => intval($subtask_id)]);
        }

        public static function add_subtask($task_id, $title) {
            global $wpdb;
            $user_id = get_current_user_id();

            // Security Check: Verify task ownership
            $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}workedia_tasks WHERE id = %d", $task_id));
            if ($owner != $user_id) return false;

            return $wpdb->insert($wpdb->prefix . 'workedia_subtasks', [
                'task_id' => intval($task_id),
                'title' => sanitize_text_field($title),
                'is_completed' => 0,
                'sort_order' => 0
            ]);
        }

        // Placeholders for Google Integration
        public static function sync_with_google_calendar($task_id) {
            // Placeholder logic for Google Calendar API
            return true;
        }

        public static function sync_with_gmail($user_id) {
            // Placeholder logic for Gmail API integration
            return true;
        }
    }
}

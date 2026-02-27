<?php

if (!class_exists('Workedia_Notebook')) {
    class Workedia_Notebook {
        public static function get_notes($user_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_notes';
            $share_table = $wpdb->prefix . 'workedia_note_shares';

            return $wpdb->get_results($wpdb->prepare(
                "SELECT n.* FROM $table n
                 LEFT JOIN $share_table s ON n.id = s.note_id
                 WHERE n.user_id = %d OR s.user_id = %d
                 GROUP BY n.id
                 ORDER BY n.updated_at DESC",
                $user_id, $user_id
            ));
        }

        public static function save_note($data) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_notes';
            $user_id = get_current_user_id();

            $note_id = !empty($data['id']) ? intval($data['id']) : null;

            if ($note_id) {
                // Security Check: Ensure user owns the note
                $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $table WHERE id = %d", $note_id));
                if ($owner != $user_id) return false;

                $update_data = [];
                if (isset($data['title'])) $update_data['title'] = sanitize_text_field($data['title']);
                if (isset($data['content'])) $update_data['content'] = wp_kses_post($data['content']);
                if (isset($data['color'])) $update_data['color'] = sanitize_hex_color($data['color']);
                if (isset($data['tags'])) $update_data['tags'] = sanitize_text_field($data['tags']);
                if (isset($data['image_url'])) $update_data['image_url'] = esc_url_raw($data['image_url']);

                if (!empty($update_data)) {
                    $wpdb->update($table, $update_data, ['id' => $note_id]);
                }
                return $note_id;
            } else {
                $insert_data = [
                    'user_id' => get_current_user_id(),
                    'title' => sanitize_text_field($data['title'] ?? ''),
                    'content' => wp_kses_post($data['content'] ?? ''),
                    'color' => sanitize_hex_color($data['color'] ?? '#ffffff'),
                    'tags' => sanitize_text_field($data['tags'] ?? ''),
                    'image_url' => esc_url_raw($data['image_url'] ?? '')
                ];
                $wpdb->insert($table, $insert_data);
                return $wpdb->insert_id;
            }
        }

        public static function delete_note($note_id, $user_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_notes';
            $wpdb->delete($wpdb->prefix . 'workedia_note_shares', ['note_id' => intval($note_id)]);
            return $wpdb->delete($table, ['id' => intval($note_id), 'user_id' => intval($user_id)]);
        }

        public static function share_note($note_id, $shared_user_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_note_shares';
            return $wpdb->insert($table, ['note_id' => intval($note_id), 'user_id' => intval($shared_user_id)]);
        }
    }
}

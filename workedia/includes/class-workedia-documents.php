<?php

if (!class_exists('Workedia_Documents')) {
    class Workedia_Documents {
        public static function get_documents($user_id, $args = []) {
            global $wpdb;
            $where = "user_id = %d";
            $params = [$user_id];

            if (!empty($args['search'])) {
                $where .= " AND title LIKE %s";
                $params[] = '%' . $wpdb->esc_like($args['search']) . '%';
            }

            if (!empty($args['category'])) {
                $where .= " AND category = %s";
                $params[] = sanitize_text_field($args['category']);
            }

            if (!empty($args['file_type'])) {
                $type = $args['file_type'];
                if ($type === 'pdf') {
                    $where .= " AND file_url LIKE '%.pdf'";
                } elseif ($type === 'doc') {
                    $where .= " AND (file_url LIKE '%.doc' OR file_url LIKE '%.docx')";
                } elseif ($type === 'image') {
                    $where .= " AND (file_url LIKE '%.jpg' OR file_url LIKE '%.jpeg' OR file_url LIKE '%.png' OR file_url LIKE '%.webp')";
                }
            }

            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_documents_archive WHERE $where ORDER BY created_at DESC",
                $params
            ));
        }

        public static function upload_document($data, $file) {
            global $wpdb;
            $user_id = get_current_user_id();

            // Validate File Type
            $allowed_types = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'image/jpeg',
                'image/png',
                'image/webp'
            ];

            if (!in_array($file['type'], $allowed_types)) {
                return new WP_Error('invalid_type', 'يسمح فقط بملفات الصور و PDF و Word.');
            }

            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_id = media_handle_upload('doc_file', 0);
            if (is_wp_error($attachment_id)) return $attachment_id;

            $file_url = wp_get_attachment_url($attachment_id);
            $file_type = $file['type'];
            $file_size = $file['size'];

            return $wpdb->insert("{$wpdb->prefix}workedia_documents_archive", [
                'user_id' => $user_id,
                'title' => sanitize_text_field($data['title']),
                'category' => sanitize_text_field($data['category'] ?? 'عام'),
                'file_url' => $file_url,
                'file_type' => $file_type,
                'file_size' => $file_size,
                'description' => sanitize_textarea_field($data['description'] ?? ''),
                'tags' => sanitize_text_field($data['tags'] ?? '')
            ]);
        }

        public static function delete_document($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}workedia_documents_archive WHERE id = %d", $id));
            if ($owner != $user_id) return false;

            return $wpdb->delete("{$wpdb->prefix}workedia_documents_archive", ['id' => intval($id)]);
        }
    }
}

<?php

if (!class_exists('Workedia_Invoicing')) {
    class Workedia_Invoicing {
        public static function get_invoices($user_id, $args = []) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_invoices';
            $query = "SELECT * FROM $table WHERE user_id = %d";
            $params = [$user_id];

            if (!empty($args['search'])) {
                $query .= " AND (client_name LIKE %s OR invoice_number LIKE %s)";
                $params[] = '%' . $wpdb->esc_like($args['search']) . '%';
                $params[] = '%' . $wpdb->esc_like($args['search']) . '%';
            }

            if (!empty($args['status']) && $args['status'] !== 'all') {
                $query .= " AND status = %s";
                $params[] = $args['status'];
            }

            $query .= " ORDER BY created_at DESC";
            return $wpdb->get_results($wpdb->prepare($query, $params));
        }

        public static function get_invoice($id, $user_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_invoices';
            return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table WHERE id = %d AND user_id = %d", $id, $user_id));
        }

        public static function get_invoice_items($invoice_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_invoice_items';
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE invoice_id = %d ORDER BY sort_order ASC", $invoice_id));
        }

        public static function get_invoice_attachments($invoice_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_invoice_attachments';
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE invoice_id = %d", $invoice_id));
        }

        public static function get_invoice_logs($invoice_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_invoice_logs';
            return $wpdb->get_results($wpdb->prepare("SELECT l.*, u.display_name FROM $table l LEFT JOIN $wpdb->users u ON l.user_id = u.ID WHERE l.invoice_id = %d ORDER BY l.created_at DESC", $invoice_id));
        }

        public static function save_invoice($data) {
            global $wpdb;
            $user_id = get_current_user_id();
            $table = $wpdb->prefix . 'workedia_invoices';

            $id = !empty($data['id']) ? intval($data['id']) : null;

            $invoice_data = [
                'user_id' => $user_id,
                'client_name' => sanitize_text_field($data['client_name']),
                'client_email' => sanitize_email($data['client_email']),
                'client_details' => sanitize_textarea_field($data['client_details']),
                'issue_date' => sanitize_text_field($data['issue_date']),
                'due_date' => sanitize_text_field($data['due_date']),
                'status' => sanitize_text_field($data['status']),
                'currency' => sanitize_text_field($data['currency']),
                'notes' => sanitize_textarea_field($data['notes']),
                'public_notes' => sanitize_textarea_field($data['public_notes'])
            ];

            if ($id) {
                $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $table WHERE id = %d", $id));
                if ($owner != $user_id) return false;
                $wpdb->update($table, $invoice_data, ['id' => $id]);
                self::log_activity($id, 'تحديث بيانات الفاتورة');
            } else {
                $invoice_data['invoice_number'] = self::generate_invoice_number($user_id);
                $invoice_data['public_token'] = wp_generate_password(20, false);
                $wpdb->insert($table, $invoice_data);
                $id = $wpdb->insert_id;
                self::log_activity($id, 'إنشاء فاتورة جديدة');
            }

            if ($id && !empty($data['items'])) {
                self::save_invoice_items($id, $data['items']);
                self::calculate_invoice_totals($id);
            }

            return $id;
        }

        private static function generate_invoice_number($user_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_invoices';
            $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d", $user_id));
            return 'INV-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
        }

        private static function save_invoice_items($invoice_id, $items_json) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_invoice_items';
            $wpdb->delete($table, ['invoice_id' => $invoice_id]);

            $items = json_decode(stripslashes($items_json), true);
            if (empty($items)) return;

            foreach ($items as $index => $item) {
                $wpdb->insert($table, [
                    'invoice_id' => $invoice_id,
                    'description' => sanitize_text_field($item['description']),
                    'quantity' => floatval($item['quantity']),
                    'unit_price' => floatval($item['unit_price']),
                    'tax_rate' => floatval($item['tax_rate'] ?? 0),
                    'discount_amount' => floatval($item['discount_amount'] ?? 0),
                    'sort_order' => $index
                ]);
            }
        }

        public static function calculate_invoice_totals($invoice_id) {
            global $wpdb;
            $items = self::get_invoice_items($invoice_id);

            $subtotal = 0;
            $tax_total = 0;
            $discount_total = 0;

            foreach ($items as $item) {
                $line_total = $item->quantity * $item->unit_price;
                $subtotal += $line_total;
                $tax_total += ($line_total * ($item->tax_rate / 100));
                $discount_total += $item->discount_amount;
            }

            $total = $subtotal + $tax_total - $discount_total;

            return $wpdb->update($wpdb->prefix . 'workedia_invoices', [
                'subtotal' => $subtotal,
                'tax_total' => $tax_total,
                'discount_total' => $discount_total,
                'total_amount' => $total
            ], ['id' => $invoice_id]);
        }

        public static function delete_invoice($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}workedia_invoices WHERE id = %d", $id));
            if ($owner != $user_id) return false;

            $wpdb->delete("{$wpdb->prefix}workedia_invoice_items", ['invoice_id' => $id]);
            $wpdb->delete("{$wpdb->prefix}workedia_invoice_attachments", ['invoice_id' => $id]);
            $wpdb->delete("{$wpdb->prefix}workedia_invoice_logs", ['invoice_id' => $id]);
            return $wpdb->delete("{$wpdb->prefix}workedia_invoices", ['id' => $id]);
        }

        public static function duplicate_invoice($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $invoice = self::get_invoice($id, $user_id);
            if (!$invoice) return false;

            $items = self::get_invoice_items($id);

            $new_id = self::save_invoice([
                'client_name' => $invoice->client_name,
                'client_email' => $invoice->client_email,
                'client_details' => $invoice->client_details,
                'issue_date' => current_time('Y-m-d'),
                'due_date' => $invoice->due_date,
                'status' => 'draft',
                'currency' => $invoice->currency,
                'notes' => $invoice->notes,
                'public_notes' => $invoice->public_notes,
                'items' => json_encode($items)
            ]);

            self::log_activity($new_id, 'تكرار فاتورة من الرقم: ' . $invoice->invoice_number);
            return $new_id;
        }

        public static function log_activity($invoice_id, $action) {
            global $wpdb;
            return $wpdb->insert($wpdb->prefix . 'workedia_invoice_logs', [
                'invoice_id' => $invoice_id,
                'user_id' => get_current_user_id(),
                'action' => $action
            ]);
        }

        public static function upload_attachment($invoice_id, $file_key) {
            if (empty($_FILES[$file_key]['name'])) return false;

            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');

            $attachment_id = media_handle_upload($file_key, 0);
            if (is_wp_error($attachment_id)) return $attachment_id;

            global $wpdb;
            $file_url = wp_get_attachment_url($attachment_id);
            $file_name = $_FILES[$file_key]['name'];

            $wpdb->insert($wpdb->prefix . 'workedia_invoice_attachments', [
                'invoice_id' => $invoice_id,
                'file_url' => $file_url,
                'file_name' => $file_name
            ]);

            self::log_activity($invoice_id, 'إرفاق ملف: ' . $file_name);
            return true;
        }

        public static function delete_attachment($id) {
            global $wpdb;
            $attachment = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}workedia_invoice_attachments WHERE id = %d", $id));
            if (!$attachment) return false;

            // Check if user owns the invoice
            $user_id = get_current_user_id();
            $owner = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}workedia_invoices WHERE id = %d", $attachment->invoice_id));
            if ($owner != $user_id) return false;

            $wpdb->delete("{$wpdb->prefix}workedia_invoice_attachments", ['id' => $id]);
            self::log_activity($attachment->invoice_id, 'حذف مرفق: ' . $attachment->file_name);
            return true;
        }

        public static function get_statistics($user_id) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_invoices';

            $stats = [
                'total_revenue' => $wpdb->get_var($wpdb->prepare("SELECT SUM(total_amount) FROM $table WHERE user_id = %d AND status = 'paid'", $user_id)) ?: 0,
                'pending_amount' => $wpdb->get_var($wpdb->prepare("SELECT SUM(total_amount) FROM $table WHERE user_id = %d AND status = 'pending'", $user_id)) ?: 0,
                'overdue_count' => $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE user_id = %d AND status = 'pending' AND due_date < CURDATE()", $user_id)) ?: 0,
                'recent_invoices' => $wpdb->get_results($wpdb->prepare("SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC LIMIT 5", $user_id))
            ];

            return $stats;
        }
    }
}

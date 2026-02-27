<?php

if (!class_exists('Workedia_ReferenceManager')) {
    class Workedia_ReferenceManager {

        // --- PROJECTS ---

        public static function get_projects($user_id) {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_research_projects WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            ));
        }

        public static function get_project($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            return $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_research_projects WHERE id = %d AND user_id = %d",
                $id, $user_id
            ));
        }

        public static function save_project($data) {
            global $wpdb;
            $user_id = get_current_user_id();
            $table = $wpdb->prefix . 'workedia_research_projects';
            $id = !empty($data['id']) ? intval($data['id']) : null;

            $save_data = [
                'title' => sanitize_text_field($data['title']),
                'description' => sanitize_textarea_field($data['description'] ?? ''),
                'citation_style' => sanitize_text_field($data['citation_style'] ?? 'apa')
            ];

            if ($id) {
                $wpdb->update($table, $save_data, ['id' => $id, 'user_id' => $user_id]);
                return $id;
            } else {
                $save_data['user_id'] = $user_id;
                $wpdb->insert($table, $save_data);
                return $wpdb->insert_id;
            }
        }

        public static function delete_project($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $project = self::get_project($id);
            if (!$project) return false;

            // Delete associated paragraphs and their reference links
            $paragraphs = $wpdb->get_col($wpdb->prepare("SELECT id FROM {$wpdb->prefix}workedia_research_paragraphs WHERE project_id = %d", $id));
            if (!empty($paragraphs)) {
                $ids = implode(',', array_map('intval', $paragraphs));
                $wpdb->query("DELETE FROM {$wpdb->prefix}workedia_paragraph_references WHERE paragraph_id IN ($ids)");
                $wpdb->query("DELETE FROM {$wpdb->prefix}workedia_research_paragraphs WHERE project_id = $id");
            }

            // References might be shared or project-specific. For now, let's keep references or handle them.
            // Requirement says "automatic categorization". Let's assume references belong to projects for now.
            $wpdb->delete("{$wpdb->prefix}workedia_references", ['project_id' => $id]);

            return $wpdb->delete("{$wpdb->prefix}workedia_research_projects", ['id' => $id, 'user_id' => $user_id]);
        }

        // --- REFERENCES ---

        public static function get_references($project_id = null) {
            global $wpdb;
            $user_id = get_current_user_id();
            $where = "user_id = %d";
            $params = [$user_id];
            if ($project_id) {
                $where .= " AND project_id = %d";
                $params[] = $project_id;
            }
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_references WHERE $where ORDER BY authors ASC, year DESC",
                $params
            ));
        }

        public static function save_reference($data) {
            global $wpdb;
            $user_id = get_current_user_id();
            $table = $wpdb->prefix . 'workedia_references';
            $id = !empty($data['id']) ? intval($data['id']) : null;

            $save_data = [
                'project_id' => !empty($data['project_id']) ? intval($data['project_id']) : null,
                'ref_type' => sanitize_text_field($data['ref_type']),
                'title' => sanitize_text_field($data['title']),
                'authors' => sanitize_text_field($data['authors']),
                'year' => sanitize_text_field($data['year']),
                'source_title' => sanitize_text_field($data['source_title'] ?? ''),
                'volume' => sanitize_text_field($data['volume'] ?? ''),
                'issue' => sanitize_text_field($data['issue'] ?? ''),
                'pages' => sanitize_text_field($data['pages'] ?? ''),
                'publisher' => sanitize_text_field($data['publisher'] ?? ''),
                'url' => esc_url_raw($data['url'] ?? ''),
                'doi' => sanitize_text_field($data['doi'] ?? ''),
                'notes' => sanitize_textarea_field($data['notes'] ?? '')
            ];

            if ($id) {
                $wpdb->update($table, $save_data, ['id' => $id, 'user_id' => $user_id]);
                return $id;
            } else {
                $save_data['user_id'] = $user_id;
                $wpdb->insert($table, $save_data);
                return $wpdb->insert_id;
            }
        }

        public static function delete_reference($id) {
            global $wpdb;
            $user_id = get_current_user_id();
            $wpdb->delete("{$wpdb->prefix}workedia_paragraph_references", ['reference_id' => $id]);
            return $wpdb->delete("{$wpdb->prefix}workedia_references", ['id' => $id, 'user_id' => $user_id]);
        }

        // --- PARAGRAPHS ---

        public static function get_paragraphs($project_id) {
            global $wpdb;
            return $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}workedia_research_paragraphs WHERE project_id = %d ORDER BY sort_order ASC",
                $project_id
            ));
        }

        public static function save_paragraph($data) {
            global $wpdb;
            $table = $wpdb->prefix . 'workedia_research_paragraphs';
            $id = !empty($data['id']) ? intval($data['id']) : null;
            $project_id = intval($data['project_id']);

            // Security: Check project ownership
            if (!self::get_project($project_id)) return false;

            $save_data = [
                'project_id' => $project_id,
                'content' => wp_kses_post($data['content']),
                'sort_order' => intval($data['sort_order'] ?? 0)
            ];

            if ($id) {
                $wpdb->update($table, $save_data, ['id' => $id]);
            } else {
                $wpdb->insert($table, $save_data);
                $id = $wpdb->insert_id;
            }

            // Handle links if provided
            if (isset($data['reference_ids'])) {
                $wpdb->delete("{$wpdb->prefix}workedia_paragraph_references", ['paragraph_id' => $id]);
                $ref_ids = is_array($data['reference_ids']) ? $data['reference_ids'] : explode(',', $data['reference_ids']);
                foreach ($ref_ids as $rid) {
                    $wpdb->insert("{$wpdb->prefix}workedia_paragraph_references", [
                        'paragraph_id' => $id,
                        'reference_id' => intval($rid)
                    ]);
                }
            }

            return $id;
        }

        public static function delete_paragraph($id) {
            global $wpdb;
            // Verify ownership via project
            $paragraph = $wpdb->get_row($wpdb->prepare("SELECT * FROM {$wpdb->prefix}workedia_research_paragraphs WHERE id = %d", $id));
            if (!$paragraph || !self::get_project($paragraph->project_id)) return false;

            $wpdb->delete("{$wpdb->prefix}workedia_paragraph_references", ['paragraph_id' => $id]);
            return $wpdb->delete("{$wpdb->prefix}workedia_research_paragraphs", ['id' => $id]);
        }

        public static function update_paragraph_order($ids) {
            global $wpdb;
            $ids_array = explode(',', $ids);
            foreach ($ids_array as $index => $id) {
                $wpdb->update("{$wpdb->prefix}workedia_research_paragraphs", ['sort_order' => $index], ['id' => intval($id)]);
            }
            return true;
        }

        // --- CITATION ENGINE ---

        public static function format_citation($ref, $style = 'apa') {
            $authors = $ref->authors;
            $year = $ref->year;
            $title = $ref->title;
            $source = $ref->source_title;

            switch (strtolower($style)) {
                case 'mla':
                    return "{$authors}. \"{$title}.\" {$source}, {$year}.";
                case 'chicago':
                    return "{$authors}. \"{$title}.\" {$source} ({$year}).";
                case 'harvard':
                    return "{$authors} ({$year}) '{$title}', {$source}.";
                case 'apa':
                default:
                    return "{$authors} ({$year}). {$title}. {$source}.";
            }
        }

        public static function get_paragraph_citations($paragraph_id, $style = 'apa') {
            global $wpdb;
            $refs = $wpdb->get_results($wpdb->prepare(
                "SELECT r.* FROM {$wpdb->prefix}workedia_references r
                 JOIN {$wpdb->prefix}workedia_paragraph_references pr ON r.id = pr.reference_id
                 WHERE pr.paragraph_id = %d",
                $paragraph_id
            ));

            $formatted = [];
            foreach ($refs as $r) {
                $formatted[] = self::format_citation($r, $style);
            }
            return $formatted;
        }

        // --- EXPORT ---

        public static function export_bibtex($project_id) {
            $refs = self::get_references($project_id);
            $bib = "";
            foreach ($refs as $r) {
                $type = $r->ref_type == 'article' ? 'article' : 'book';
                $key = strtolower(str_replace(' ', '', explode(',', $r->authors)[0]) . $r->year);
                $bib .= "@{$type}{{$key},\n";
                $bib .= "  author = {{$r->authors}},\n";
                $bib .= "  title = {{$r->title}},\n";
                $bib .= "  year = {{$r->year}},\n";
                if ($r->source_title) $bib .= "  journal = {{$r->source_title}},\n";
                if ($r->publisher) $bib .= "  publisher = {{$r->publisher}},\n";
                $bib .= "}\n\n";
            }
            return $bib;
        }

        public static function validate_reference($ref) {
            $errors = [];
            if (empty($ref->authors)) $errors[] = 'اسم المؤلف مفقود';
            if (empty($ref->year)) $errors[] = 'سنة النشر مفقودة';
            if (empty($ref->title)) $errors[] = 'العنوان مفقود';
            if ($ref->ref_type == 'article' && empty($ref->source_title)) $errors[] = 'اسم المجلة مفقود';
            return $errors;
        }

        public static function smart_search($query) {
            // Mock API for smart search (simulating library search)
            // In real app, this would call CrossRef or similar
            return [
                [
                    'title' => 'Sample Research Paper on AI',
                    'authors' => 'John Doe, Jane Smith',
                    'year' => '2023',
                    'source_title' => 'Journal of AI Research',
                    'ref_type' => 'article'
                ],
                [
                    'title' => 'The Future of Technology',
                    'authors' => 'Alan Turing',
                    'year' => '2024',
                    'publisher' => 'Academic Press',
                    'ref_type' => 'book'
                ]
            ];
        }

        public static function export_word($project_id) {
            $project = self::get_project($project_id);
            if (!$project) return "";

            $paragraphs = self::get_paragraphs($project_id);
            $html = "<html dir='rtl'><head><meta charset='UTF-8'></head><body>";
            $html .= "<h1>" . esc_html($project->title) . "</h1>";
            $html .= "<p>" . esc_html($project->description) . "</p><hr>";

            foreach ($paragraphs as $p) {
                $html .= "<div>" . $p->content;
                $citations = self::get_paragraph_citations($p->id, $project->citation_style);
                if (!empty($citations)) {
                    $html .= " [" . implode('; ', $citations) . "]";
                }
                $html .= "</div><br>";
            }

            $html .= "<h2>المراجع</h2><ul>";
            $refs = self::get_references($project_id);
            foreach ($refs as $r) {
                $html .= "<li>" . self::format_citation($r, $project->citation_style) . "</li>";
            }
            $html .= "</ul></body></html>";
            return $html;
        }
    }
}

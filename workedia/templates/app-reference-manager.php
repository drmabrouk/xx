<?php if (!defined('ABSPATH')) exit; ?>
<div class="workedia-app-container reference-manager-app">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2 style="margin: 0; font-weight: 800; color: var(--workedia-dark-color);">مدير المراجع العلمية</h2>
        <button onclick="workediaOpenProjectModal()" class="workedia-btn" style="width: auto;">+ مشروع بحثي جديد</button>
    </div>

    <div class="workedia-card-grid">
        <?php
        $projects = Workedia_ReferenceManager::get_projects(get_current_user_id());
        if (empty($projects)): ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px; background: #fff; border: 2px dashed #e2e8f0; border-radius: 24px; color: #94a3b8;">
                <span class="dashicons dashicons-welcome-learn-more" style="font-size: 64px; width: 64px; height: 64px; opacity: 0.2;"></span>
                <p style="font-size: 1.2em; font-weight: 600; margin-top: 20px;">ابدأ بتنظيم أبحاثك العلمية الآن.</p>
                <button onclick="workediaOpenProjectModal()" class="workedia-btn workedia-btn-outline" style="width: auto; margin-top: 20px;">إنشاء أول مشروع</button>
            </div>
        <?php else: foreach ($projects as $project): ?>
            <div class="note-modern-card project-card" style="text-align: right; position: relative;">
                <div class="note-card-body">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                        <div style="background: #EBF8FF; width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: #3182ce;">
                            <span class="dashicons dashicons-portfolio" style="font-size:24px; width:24px; height:24px;"></span>
                        </div>
                        <div class="workedia-actions-dropdown">
                            <button class="workedia-actions-trigger">...</button>
                            <div class="workedia-actions-content">
                                <a href="<?php echo add_query_arg('workedia_tab', 'reference-writer'); ?>&project_id=<?php echo $project->id; ?>" class="workedia-action-item"><span class="dashicons dashicons-editor-paragraph"></span> كتابة البحث</a>
                                <a href="<?php echo add_query_arg('workedia_tab', 'reference-library'); ?>&project_id=<?php echo $project->id; ?>" class="workedia-action-item"><span class="dashicons dashicons-book"></span> المكتبة</a>
                                <a href="javascript:void(0)" onclick='workediaEditProject(<?php echo json_encode($project); ?>)' class="workedia-action-item"><span class="dashicons dashicons-edit"></span> تعديل</a>
                                <a href="javascript:void(0)" onclick="workediaDeleteProject(<?php echo $project->id; ?>)" class="workedia-action-item" style="color: #e53e3e !important;"><span class="dashicons dashicons-trash"></span> حذف</a>
                            </div>
                        </div>
                    </div>
                    <h3 class="note-card-title"><?php echo esc_html($project->title); ?></h3>
                    <p style="color: #64748b; font-size: 12px; line-height: 1.5;"><?php echo esc_html($project->description); ?></p>
                    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                        <span class="workedia-badge" style="background: #E6FFFA; color: #319795; font-size: 10px;"><?php echo strtoupper($project->citation_style); ?> Style</span>
                        <span style="font-size: 11px; color: #94a3b8;"><?php echo date('Y-m-d', strtotime($project->created_at)); ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<!-- Project Modal -->
<div id="project-modal" class="workedia-modal-overlay">
    <div class="workedia-modal-content" style="max-width: 500px;">
        <div class="workedia-modal-header">
            <h3 id="project-modal-title">مشروع بحثي جديد</h3>
            <button class="workedia-modal-close" onclick="document.getElementById('project-modal').style.display='none'">&times;</button>
        </div>
        <form id="project-form" style="padding: 20px;">
            <input type="hidden" name="id" id="project-id">
            <div class="workedia-form-group">
                <label class="workedia-label">عنوان البحث / المشروع:</label>
                <input type="text" name="title" id="project-title" class="workedia-input" required>
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">وصف البحث:</label>
                <textarea name="description" id="project-desc" class="workedia-textarea" rows="3"></textarea>
            </div>
            <div class="workedia-form-group">
                <label class="workedia-label">نمط الاقتباس (Citation Style):</label>
                <select name="citation_style" id="project-style" class="workedia-select">
                    <option value="apa">APA (American Psychological Association)</option>
                    <option value="mla">MLA (Modern Language Association)</option>
                    <option value="chicago">Chicago Manual of Style</option>
                    <option value="harvard">Harvard System</option>
                </select>
            </div>
            <button type="submit" class="workedia-btn" style="width: 100%; margin-top: 10px;">حفظ المشروع</button>
        </form>
    </div>
</div>

<script>
function workediaOpenProjectModal() {
    document.getElementById('project-form').reset();
    document.getElementById('project-id').value = '';
    document.getElementById('project-modal-title').innerText = 'مشروع بحثي جديد';
    document.getElementById('project-modal').style.display = 'flex';
}

function workediaEditProject(p) {
    document.getElementById('project-id').value = p.id;
    document.getElementById('project-title').value = p.title;
    document.getElementById('project-desc').value = p.description;
    document.getElementById('project-style').value = p.citation_style;
    document.getElementById('project-modal-title').innerText = 'تعديل المشروع';
    document.getElementById('project-modal').style.display = 'flex';
}

document.getElementById('project-form').addEventListener('submit', function(e) {
    e.preventDefault();
    const fd = new FormData(this);
    fd.append('action', 'workedia_save_research_project');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
        else alert(res.data);
    });
});

function workediaDeleteProject(id) {
    if (!confirm('سيتم حذف المشروع وكافة الفقرات والمراجع المرتبطة به. هل أنت متأكد؟')) return;
    const fd = new FormData();
    fd.append('action', 'workedia_delete_research_project');
    fd.append('id', id);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}
</script>

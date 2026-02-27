<?php if (!defined('ABSPATH')) exit;
$project_id = intval($_GET['project_id'] ?? 0);
$project = Workedia_ReferenceManager::get_project($project_id);
if (!$project) { echo 'Unauthorized'; return; }
?>
<div class="workedia-app-container reference-writer-app" style="padding: 0;">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <div style="display: flex; height: calc(100vh - 150px);">
        <!-- Side Panel: References Library -->
        <div style="width: 350px; background: #f8fafc; border-left: 1px solid #e2e8f0; display: flex; flex-direction: column;">
            <div style="padding: 20px; border-bottom: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 15px 0; font-weight: 800;">مراجع المشروع</h4>
                <input type="text" id="writer-ref-search" class="workedia-input" placeholder="ابحث لربط مرجع..." style="font-size: 12px; height: 35px; border-radius: 50px;">
            </div>
            <div id="writer-refs-list" style="flex: 1; overflow-y: auto; padding: 15px;">
                <?php
                $refs = Workedia_ReferenceManager::get_references($project_id);
                foreach ($refs as $r): ?>
                    <div class="writer-ref-item" draggable="true" ondragstart="event.dataTransfer.setData('text/plain', '<?php echo $r->id; ?>')" style="background:white; padding:12px; border-radius:10px; border:1px solid #e2e8f0; margin-bottom:10px; cursor: grab; transition: 0.2s;">
                        <div style="font-size: 12px; font-weight: 700; color: #111F35;"><?php echo $r->authors; ?> (<?php echo $r->year; ?>)</div>
                        <div style="font-size: 11px; color: #64748b; margin-top: 3px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;"><?php echo $r->title; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Main Workspace: Writing Area -->
        <div style="flex: 1; display: flex; flex-direction: column; background: #fff; padding: 40px; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px;">
                <div>
                    <h2 style="margin: 0; font-weight: 900; color: #111F35;"><?php echo esc_html($project->title); ?></h2>
                    <p style="color: #64748b; margin-top: 5px;">اكتب فقرات بحثك واربطها بالمراجع من القائمة الجانبية.</p>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button onclick="workediaExportWord(<?php echo $project_id; ?>)" class="workedia-btn" style="width: auto; background: #2b6cb0;"><span class="dashicons dashicons-media-document"></span> تصدير Word</button>
                    <button onclick="workediaExportBibtex(<?php echo $project_id; ?>)" class="workedia-btn" style="width: auto; background: #4a5568;"><span class="dashicons dashicons-download"></span> BibTeX</button>
                    <a href="<?php echo add_query_arg('workedia_tab', 'reference-library'); ?>&project_id=<?php echo $project_id; ?>" class="workedia-btn workedia-btn-outline" style="width: auto;">المكتبة</a>
                </div>
            </div>

            <div id="paragraphs-container" style="display: flex; flex-direction: column; gap: 20px;">
                <?php
                $paragraphs = Workedia_ReferenceManager::get_paragraphs($project_id);
                foreach ($paragraphs as $p):
                    $citations = Workedia_ReferenceManager::get_paragraph_citations($p->id, $project->citation_style);
                ?>
                    <div class="paragraph-block" data-id="<?php echo $p->id; ?>" style="background: #fff; border: 1px solid #f1f5f9; border-radius: 16px; padding: 25px; position: relative; box-shadow: 0 4px 6px rgba(0,0,0,0.02); transition: 0.3s;" ondragover="event.preventDefault()" ondrop="workediaDropRef(event, <?php echo $p->id; ?>)">
                        <div class="paragraph-drag-handle" style="position: absolute; right: -15px; top: 50%; transform: translateY(-50%); cursor: grab; color: #cbd5e0; opacity: 0; transition: 0.2s;"><span class="dashicons dashicons-move"></span></div>
                        <textarea onblur="workediaSavePara(<?php echo $p->id; ?>, this.value)" style="width: 100%; border: none; font-size: 16px; line-height: 1.8; resize: none; min-height: 100px; padding: 0; outline: none; font-family: 'Rubik', sans-serif;"><?php echo esc_textarea($p->content); ?></textarea>

                        <div style="margin-top: 15px; display: flex; flex-wrap: wrap; gap: 8px; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                            <?php if (empty($citations)): ?>
                                <span style="font-size: 11px; color: #94a3b8; font-style: italic;">لا توجد مراجع مرتبطة بهذه الفقرة. اسحب مرجعاً هنا للربط.</span>
                            <?php else: foreach ($citations as $cit): ?>
                                <div class="citation-tag" style="background: #f0f4f8; padding: 4px 12px; border-radius: 50px; font-size: 11px; font-weight: 700; color: #3182ce; display: flex; align-items: center; gap: 6px;">
                                    <span><?php echo $cit; ?></span>
                                    <button class="dashicons dashicons-no-alt" style="background:none; border:none; padding:0; cursor:pointer; font-size:12px; color:#cbd5e0;" onmouseover="this.style.color='#e53e3e'" onmouseout="this.style.color='#cbd5e0'"></button>
                                </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <button onclick="workediaAddPara()" class="workedia-btn workedia-btn-outline" style="margin-top: 30px; border-style: dashed; background: #f8fafc; height: 60px; font-weight: 700; color: #94a3b8 !important;">+ إضافة فقرة جديدة</button>
        </div>
    </div>
</div>

<style>
.paragraph-block:hover { border-color: #3182ce; box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
.paragraph-block:hover .paragraph-drag-handle { opacity: 1; right: -25px; }
.writer-ref-item:hover { transform: scale(1.02); border-color: #3182ce; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
.writer-ref-item:active { cursor: grabbing; }
</style>

<script>
function workediaAddPara() {
    const fd = new FormData();
    fd.append('action', 'workedia_save_research_paragraph');
    fd.append('project_id', '<?php echo $project_id; ?>');
    fd.append('content', '');
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}

function workediaSavePara(id, content) {
    const fd = new FormData();
    fd.append('action', 'workedia_save_research_paragraph');
    fd.append('id', id);
    fd.append('project_id', '<?php echo $project_id; ?>');
    fd.append('content', content);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');
    fetch(ajaxurl, { method: 'POST', body: fd });
}

function workediaDropRef(event, paraId) {
    event.preventDefault();
    const refId = event.dataTransfer.getData('text/plain');
    if (!refId) return;

    const fd = new FormData();
    fd.append('action', 'workedia_link_ref_to_para');
    fd.append('paragraph_id', paraId);
    fd.append('reference_id', refId);
    fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');

    fetch(ajaxurl, { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.success) location.reload();
    });
}

function workediaExportWord(projectId) {
    window.location.href = ajaxurl + '?action=workedia_export_research_word&project_id=' + projectId + '&nonce=' + '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>';
}

function workediaExportBibtex(projectId) {
    window.location.href = ajaxurl + '?action=workedia_export_research_bibtex&project_id=' + projectId + '&nonce=' + '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>';
}

window.addEventListener('load', function() {
    const el = document.getElementById('paragraphs-container');
    if (el) {
        Sortable.create(el, {
            animation: 150,
            handle: '.paragraph-drag-handle',
            onEnd: function() {
                const ids = [];
                el.querySelectorAll('.paragraph-block').forEach(item => ids.push(item.dataset.id));
                const fd = new FormData();
                fd.append('action', 'workedia_update_paragraph_order');
                fd.append('ids', ids.join(','));
                fd.append('nonce', '<?php echo wp_create_nonce("workedia_ref_manager_action"); ?>');
                fetch(ajaxurl, { method: 'POST', body: fd });
            }
        });
    }
});
</script>

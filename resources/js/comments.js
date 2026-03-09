/**
 * comments.js
 * Handles the comments section on the task detail page (tasks/show).
 * Depends on: marked (npm), window.apiCall, window.toast, window.confirmDialog
 */
import { marked } from 'marked';

marked.setOptions({ breaks: true, gfm: true });

export function initComments({ taskId }) {
    const commentBody      = document.getElementById('comment-body');
    const commentPreview   = document.getElementById('comment-preview');
    const commentCharCount = document.getElementById('comment-char-count');
    const btnAddComment    = document.getElementById('btn-add-comment');
    const commentList      = document.getElementById('comment-list');
    const countBadge       = document.getElementById('comment-count');
    const loadMoreWrap     = document.getElementById('comments-load-more-wrap');
    const btnLoadMore      = document.getElementById('btn-load-more');
    const tabWrite         = document.getElementById('tab-write');
    const tabPreview       = document.getElementById('tab-preview');

    if (!commentList) return;

    const apiCall      = window.apiCall;
    const toast        = window.toast;
    const confirmDialog = window.confirmDialog;

    let currentPage = 1;
    let lastPage    = 1;
    let totalCount  = parseInt(countBadge?.textContent) || 0;

    // ── Helpers ───────────────────────────────────────────────────────────────
    function renderMd(text) {
        return marked.parse(text || '');
    }

    function setCount(n) {
        totalCount = n;
        if (countBadge) countBadge.textContent = n;
    }

    function deltaCount(d) {
        setCount(Math.max(0, totalCount + d));
    }

    function showEmpty() {
        if (document.getElementById('comments-empty')) return;
        const empty = document.createElement('div');
        empty.id = 'comments-empty';
        empty.style.cssText = 'text-align:center;padding:20px 0;color:var(--muted);font-size:13px';
        empty.textContent = 'Nenhum comentário ainda.';
        commentList.appendChild(empty);
    }

    // ── Tabs ──────────────────────────────────────────────────────────────────
    function setTab(tab) {
        const accent   = 'var(--accent)';
        const muted    = 'var(--muted)';
        const transparent = 'transparent';
        if (tab === 'write') {
            if (commentBody)    commentBody.style.display    = '';
            if (commentPreview) commentPreview.style.display = 'none';
            if (tabWrite)   { tabWrite.style.color = accent;  tabWrite.style.borderBottomColor = accent; }
            if (tabPreview) { tabPreview.style.color = muted; tabPreview.style.borderBottomColor = transparent; }
        } else {
            if (commentPreview) {
                commentPreview.innerHTML = renderMd(commentBody?.value) || '<em style="color:var(--muted)">Nada para pré-visualizar.</em>';
                commentPreview.style.display = '';
            }
            if (commentBody)  commentBody.style.display  = 'none';
            if (tabPreview) { tabPreview.style.color = accent; tabPreview.style.borderBottomColor = accent; }
            if (tabWrite)   { tabWrite.style.color = muted;    tabWrite.style.borderBottomColor = transparent; }
        }
    }

    tabWrite?.addEventListener('click',   () => setTab('write'));
    tabPreview?.addEventListener('click', () => setTab('preview'));

    commentBody?.addEventListener('input', () => {
        const len = commentBody.value.length;
        if (commentCharCount) {
            commentCharCount.textContent = `${len} / 2000`;
            commentCharCount.style.color = len > 1800 ? 'var(--danger)' : 'var(--muted)';
        }
    });

    commentBody?.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); btnAddComment?.click(); }
    });

    // ── Build comment element ─────────────────────────────────────────────────
    function buildCommentEl(comment) {
        const div = document.createElement('div');
        div.className = 'comment-item';
        div.dataset.id   = comment.id;
        div.dataset.body = comment.body;
        div.style.cssText = 'display:flex;gap:10px;padding:10px 0;border-bottom:1px solid var(--border)';

        const editedLabel = comment.edited
            ? `<span style="color:var(--muted);font-size:10px;margin-left:6px">(editado)</span>`
            : '';

        div.innerHTML = `
            <div style="width:28px;height:28px;border-radius:50%;background:rgba(255,145,77,.15);display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;margin-top:1px">💬</div>
            <div style="flex:1;min-width:0">
                <div class="comment-body-display md-body">${renderMd(comment.body)}</div>
                <div style="display:flex;align-items:center;justify-content:space-between;margin-top:5px;flex-wrap:wrap;gap:4px">
                    <span style="color:var(--muted);font-size:11px;font-family:'DM Sans',monospace">
                        ${comment.created_at}${editedLabel}
                    </span>
                    <div style="display:flex;gap:4px">
                        <button class="btn-edit-comment" data-id="${comment.id}" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:color .15s,background .15s">Editar</button>
                        <button class="btn-delete-comment" data-id="${comment.id}" style="background:none;border:none;color:var(--muted);cursor:pointer;font-size:11px;padding:2px 6px;border-radius:4px;transition:color .15s,background .15s">Excluir</button>
                    </div>
                </div>
            </div>
        `;

        div.querySelectorAll('button').forEach(btn => {
            btn.addEventListener('mouseenter', () => {
                const isDanger = btn.classList.contains('btn-delete-comment');
                btn.style.color = isDanger ? 'var(--danger)' : 'var(--accent)';
                btn.style.background = isDanger ? 'rgba(224,84,84,.1)' : 'rgba(255,145,77,.1)';
            });
            btn.addEventListener('mouseleave', () => {
                btn.style.color = 'var(--muted)';
                btn.style.background = 'none';
            });
        });

        attachHandlers(div);
        return div;
    }

    // ── Edit / delete handlers ────────────────────────────────────────────────
    function attachHandlers(el) {
        el.querySelector('.btn-edit-comment')?.addEventListener('click', () => startEdit(el));
        el.querySelector('.btn-delete-comment')?.addEventListener('click', () => handleDelete(el));
    }

    function startEdit(el) {
        if (el.querySelector('.comment-edit-textarea')) return;
        const display = el.querySelector('.comment-body-display');
        const rawBody = el.dataset.body;
        display.style.display = 'none';

        const wrap     = document.createElement('div');
        wrap.className = 'comment-edit-wrap';

        const textarea = document.createElement('textarea');
        textarea.className = 'comment-edit-textarea';
        textarea.value = rawBody;
        textarea.style.cssText = 'width:100%;resize:vertical;min-height:72px;background:var(--surface2);border:1px solid var(--accent);border-radius:8px;padding:10px 12px;font-size:13px;font-family:inherit;color:var(--text);line-height:1.55;outline:none;box-sizing:border-box;box-shadow:0 0 0 3px rgba(255,145,77,.1)';

        const counter = document.createElement('div');
        counter.style.cssText = 'font-size:11px;color:var(--muted);font-family:"DM Sans",monospace;text-align:right;margin-top:3px';
        counter.textContent = `${rawBody.length} / 2000`;
        textarea.addEventListener('input', () => {
            counter.textContent = `${textarea.value.length} / 2000`;
            counter.style.color = textarea.value.length > 1800 ? 'var(--danger)' : 'var(--muted)';
        });

        const actions  = document.createElement('div');
        actions.style.cssText = 'display:flex;gap:6px;justify-content:flex-end;margin-top:6px';

        const btnCancel = document.createElement('button');
        btnCancel.textContent = 'Cancelar';
        btnCancel.className = 'btn btn-ghost btn-sm';
        btnCancel.addEventListener('click', () => cancelEdit(el, display, wrap));

        const btnSave = document.createElement('button');
        btnSave.textContent = 'Salvar';
        btnSave.className = 'btn btn-primary btn-sm';
        btnSave.addEventListener('click', () => saveEdit(el, textarea, display, wrap, btnSave));

        textarea.addEventListener('keydown', e => {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') { e.preventDefault(); btnSave.click(); }
            if (e.key === 'Escape') cancelEdit(el, display, wrap);
        });

        actions.append(btnCancel, btnSave);
        wrap.append(textarea, counter, actions);
        display.after(wrap);
        textarea.focus();
        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
    }

    function cancelEdit(el, display, wrap) {
        wrap.remove();
        display.style.display = '';
    }

    async function saveEdit(el, textarea, display, wrap, btnSave) {
        const body = textarea.value.trim();
        if (!body) { textarea.focus(); return; }
        if (body.length > 2000) { toast('Comentário muito longo.', 'error'); return; }

        btnSave.innerHTML = '<span class="spinner"></span>';
        btnSave.disabled  = true;

        const id  = el.dataset.id;
        const res = await apiCall('PATCH', `/api/v1/tasks/${taskId}/comments/${id}`, { body });

        if (res.ok) {
            const updated = await res.json();
            el.dataset.body = updated.body;
            display.innerHTML = renderMd(updated.body);

            const editedSpan = el.querySelector('[data-edited]');
            if (editedSpan) {
                editedSpan.textContent = '(editado)';
            } else {
                const ts = el.querySelector('[style*="monospace"]');
                if (ts) ts.insertAdjacentHTML('beforeend', '<span style="color:var(--muted);font-size:10px;margin-left:6px" data-edited>(editado)</span>');
            }

            wrap.remove();
            display.style.display = '';
            toast('Comentário atualizado.', 'success');
        } else {
            const data = await res.json().catch(() => ({}));
            const msg  = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Erro.');
            toast(msg, 'error');
            btnSave.innerHTML = 'Salvar';
            btnSave.disabled  = false;
        }
    }

    async function handleDelete(el) {
        const id = el.dataset.id;
        confirmDialog('Excluir comentário', 'Esta ação não pode ser desfeita.', async () => {
            const res = await apiCall('DELETE', `/api/v1/tasks/${taskId}/comments/${id}`);
            if (res.ok) {
                el.remove();
                deltaCount(-1);
                if (!commentList.querySelector('.comment-item')) showEmpty();
                toast('Comentário excluído.', 'info');
            } else {
                toast('Erro ao excluir comentário.', 'error');
            }
        });
    }

    // ── Pagination ────────────────────────────────────────────────────────────
    async function loadComments(page = 1) {
        if (btnLoadMore) { btnLoadMore.innerHTML = '<span class="spinner"></span>'; btnLoadMore.disabled = true; }

        try {
            const res  = await apiCall('GET', `/api/v1/tasks/${taskId}/comments?page=${page}`);
            const data = await res.json();

            if (page === 1) {
                commentList.innerHTML = '';
                document.getElementById('comments-empty')?.remove();
            }

            if (data.data.length === 0 && page === 1) {
                showEmpty();
            } else {
                data.data.forEach(c => commentList.appendChild(buildCommentEl(c)));
            }

            currentPage = data.current_page;
            lastPage    = data.last_page;
            setCount(data.total);

            if (loadMoreWrap) loadMoreWrap.style.display = currentPage < lastPage ? '' : 'none';
        } catch {
            toast('Erro ao carregar comentários.', 'error');
        } finally {
            if (btnLoadMore) { btnLoadMore.innerHTML = 'Carregar mais'; btnLoadMore.disabled = false; }
        }
    }

    btnLoadMore?.addEventListener('click', () => loadComments(currentPage + 1));

    // ── Add comment ───────────────────────────────────────────────────────────
    btnAddComment?.addEventListener('click', async function () {
        const body = commentBody?.value.trim();
        if (!body) { setTab('write'); commentBody?.focus(); return; }
        if (body.length > 2000) { toast('Comentário muito longo (máx 2000 chars).', 'error'); return; }

        this.innerHTML = '<span class="spinner"></span>';
        this.disabled  = true;

        try {
            const res = await apiCall('POST', `/api/v1/tasks/${taskId}/comments`, { body });
            if (res.ok) {
                const comment = await res.json();
                document.getElementById('comments-empty')?.remove();
                commentList.insertBefore(buildCommentEl(comment), commentList.firstChild);
                if (commentBody) commentBody.value = '';
                if (commentCharCount) commentCharCount.textContent = '0 / 2000';
                setTab('write');
                deltaCount(+1);
                toast('Comentário adicionado.', 'success');
            } else {
                const data = await res.json();
                const msg  = data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || 'Erro.');
                toast(msg, 'error');
            }
        } catch {
            toast('Erro de conexão.', 'error');
        } finally {
            this.innerHTML = 'Comentar';
            this.disabled  = false;
        }
    });

    // Initial load
    loadComments(1);
}

window.initComments = initComments;

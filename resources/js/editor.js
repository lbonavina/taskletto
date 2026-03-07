import { Editor, Extension } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Highlight from '@tiptap/extension-highlight';
import TaskList from '@tiptap/extension-task-list';
import TaskItem from '@tiptap/extension-task-item';
import { Table } from '@tiptap/extension-table';
import { TableRow } from '@tiptap/extension-table-row';
import { TableCell } from '@tiptap/extension-table-cell';
import { TableHeader } from '@tiptap/extension-table-header';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Placeholder from '@tiptap/extension-placeholder';
import CharacterCount from '@tiptap/extension-character-count';
import { Suggestion } from '@tiptap/suggestion';

// ── Slash commands data ───────────────────────────────────────────────────────
const SLASH_ITEMS = [
    { group: 'Texto', icon: '¶', label: 'Parágrafo', desc: 'Texto normal', cmd: (e, r) => e.chain().focus().deleteRange(r).setParagraph().run() },
    { group: 'Texto', icon: 'H1', label: 'Título 1', desc: 'Título grande', cmd: (e, r) => e.chain().focus().deleteRange(r).setHeading({ level: 1 }).run() },
    { group: 'Texto', icon: 'H2', label: 'Título 2', desc: 'Título médio', cmd: (e, r) => e.chain().focus().deleteRange(r).setHeading({ level: 2 }).run() },
    { group: 'Texto', icon: 'H3', label: 'Título 3', desc: 'Título pequeno', cmd: (e, r) => e.chain().focus().deleteRange(r).setHeading({ level: 3 }).run() },
    { group: 'Listas', icon: '•', label: 'Lista', desc: 'Lista com marcadores', cmd: (e, r) => e.chain().focus().deleteRange(r).toggleBulletList().run() },
    { group: 'Listas', icon: '1.', label: 'Lista numerada', desc: 'Lista com números', cmd: (e, r) => e.chain().focus().deleteRange(r).toggleOrderedList().run() },
    { group: 'Listas', icon: '✓', label: 'Checklist', desc: 'Lista de tarefas', cmd: (e, r) => e.chain().focus().deleteRange(r).toggleTaskList().run() },
    { group: 'Blocos', icon: '"', label: 'Citação', desc: 'Bloco de citação', cmd: (e, r) => e.chain().focus().deleteRange(r).toggleBlockquote().run() },
    { group: 'Blocos', icon: '<>', label: 'Código', desc: 'Bloco de código', cmd: (e, r) => e.chain().focus().deleteRange(r).toggleCodeBlock().run() },
    { group: 'Blocos', icon: '—', label: 'Divisória', desc: 'Linha horizontal', cmd: (e, r) => e.chain().focus().deleteRange(r).setHorizontalRule().run() },
    { group: 'Conteúdo', icon: '⊞', label: 'Tabela', desc: 'Tabela 3×3', cmd: (e, r) => e.chain().focus().deleteRange(r).insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run() },
    { group: 'Conteúdo', icon: '🖼', label: 'Imagem', desc: 'Inserir imagem', cmd: (e, r) => { e.chain().focus().deleteRange(r).run(); window.openImagePopover?.(); } },
];

// ── Slash menu DOM ────────────────────────────────────────────────────────────
function getOrCreateSlashMenu() {
    let el = document.getElementById('slash-menu');
    if (el) return el;
    el = document.createElement('div');
    el.id = 'slash-menu';
    // Must be in body, NOT inside .note-shell (overflow:hidden clips it)
    document.body.appendChild(el);
    return el;
}

function applySlashMenuTheme(el) {
    const light = document.documentElement.getAttribute('data-theme') === 'light';
    el.style.cssText = [
        'display:none', 'position:fixed', 'z-index:9999',
        `background:${light ? '#ffffff' : '#1e1e28'}`,
        `border:1px solid ${light ? '#dddde6' : '#2e2e3e'}`,
        'border-radius:12px', 'padding:6px', 'min-width:270px',
        `box-shadow:0 12px 40px ${light ? 'rgba(0,0,0,.15)' : 'rgba(0,0,0,.6)'}`,
        'max-height:360px', 'overflow-y:auto',
        'scrollbar-width:thin',
        `scrollbar-color:${light ? '#ccc' : '#333'} transparent`,
        'font-family:inherit',
    ].join(';');
}

function renderSlashMenu(items, el, onSelect) {
    el.innerHTML = '';
    const light = document.documentElement.getAttribute('data-theme') === 'light';
    const iconBg = light ? '#ececf0' : '#2a2a35';
    const iconCol = light ? '#333344' : '#c8c8d8';
    const textCol = light ? '#18181c' : '#e8e8f0';
    const mutedCol = light ? '#77778a' : '#8888a0';
    const sepCol = light ? '#e0e0ea' : '#2e2e3e';
    const hoverBg = 'rgba(255,145,77,.13)';

    if (!items.length) {
        const empty = document.createElement('div');
        empty.style.cssText = `padding:10px 12px;font-size:12.5px;color:${mutedCol}`;
        empty.textContent = 'Nenhum comando';
        el.appendChild(empty);
        return;
    }

    let lastGroup = null;
    items.forEach((item, idx) => {
        if (item.group !== lastGroup) {
            if (lastGroup !== null) {
                const sep = document.createElement('div');
                sep.style.cssText = `height:1px;background:${sepCol};margin:4px 6px`;
                el.appendChild(sep);
            }
            const lbl = document.createElement('div');
            lbl.style.cssText = `font-size:10px;font-weight:700;color:${mutedCol};letter-spacing:.8px;text-transform:uppercase;padding:8px 12px 3px`;
            lbl.textContent = item.group;
            el.appendChild(lbl);
            lastGroup = item.group;
        }

        const row = document.createElement('button');
        row.dataset.idx = idx;
        row.dataset.hoverBg = hoverBg;
        row.style.cssText = `display:flex;align-items:center;gap:11px;width:100%;padding:8px 10px;border-radius:8px;border:none;background:none;cursor:pointer;text-align:left;transition:background .1s;font-family:inherit`;

        const icon = document.createElement('span');
        icon.style.cssText = `width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:${iconBg};border-radius:7px;font-size:13px;font-weight:700;flex-shrink:0;color:${iconCol};font-family:'DM Sans',monospace`;
        icon.textContent = item.icon;

        const texts = document.createElement('span');
        texts.style.cssText = 'display:flex;flex-direction:column;gap:1px';

        const label = document.createElement('span');
        label.style.cssText = `font-size:13.5px;font-weight:500;color:${textCol}`;
        label.textContent = item.label;

        const desc = document.createElement('span');
        desc.style.cssText = `font-size:11.5px;color:${mutedCol}`;
        desc.textContent = item.desc;

        texts.appendChild(label);
        texts.appendChild(desc);
        row.appendChild(icon);
        row.appendChild(texts);

        row.addEventListener('mouseenter', () => setSelected(el, idx));
        row.addEventListener('mousedown', e => { e.preventDefault(); onSelect(item); });
        el.appendChild(row);
    });
    setSelected(el, 0);
}

function setSelected(el, idx) {
    const btns = el.querySelectorAll('button[data-idx]');
    btns.forEach((b, i) => {
        b.style.background = i === idx ? 'rgba(255,145,77,.13)' : 'none';
    });
    btns[idx]?.scrollIntoView({ block: 'nearest' });
}

function getSelectedIdx(el) {
    const btns = [...el.querySelectorAll('button[data-idx]')];
    return btns.findIndex(b => b.style.background !== '');
}

// Position a floating panel near a cursor rect, keeping it on-screen
function placeMenu(el, rect) {
    if (!rect) return;
    // Make visible off-screen first so we can measure real dimensions
    el.style.visibility = 'hidden';
    el.style.display = 'block';
    const mw = el.offsetWidth;
    const mh = el.offsetHeight;
    el.style.visibility = '';

    const vw = window.innerWidth;
    const vh = window.innerHeight;

    let top = rect.bottom + 6;
    if (top + mh > vh - 8) top = rect.top - mh - 6;
    top = Math.max(8, top);

    let left = rect.left;
    if (left + mw > vw - 8) left = vw - mw - 8;
    left = Math.max(8, left);

    el.style.top = top + 'px';
    el.style.left = left + 'px';
}

// ── SlashCommands Extension ───────────────────────────────────────────────────
const SlashCommands = Extension.create({
    name: 'slashCommands',
    addProseMirrorPlugins() {
        return [
            Suggestion({
                editor: this.editor,
                char: '/',
                startOfLine: false,
                items: ({ query }) => {
                    if (!query) return SLASH_ITEMS;
                    const q = query.toLowerCase();
                    return SLASH_ITEMS.filter(i =>
                        i.label.toLowerCase().includes(q) || i.desc.toLowerCase().includes(q)
                    );
                },
                render: () => {
                    let el;
                    return {
                        onStart(props) {
                            el = getOrCreateSlashMenu();
                            applySlashMenuTheme(el);
                            const onSelect = item => { item.cmd(props.editor, props.range); el.style.display = 'none'; };
                            renderSlashMenu(props.items, el, onSelect);
                            placeMenu(el, props.clientRect?.());
                        },
                        onUpdate(props) {
                            const onSelect = item => { item.cmd(props.editor, props.range); el.style.display = 'none'; };
                            renderSlashMenu(props.items, el, onSelect);
                            placeMenu(el, props.clientRect?.());
                        },
                        onKeyDown({ event }) {
                            if (!el) return false;
                            const btns = [...el.querySelectorAll('button[data-idx]')];
                            const cur = getSelectedIdx(el);
                            if (event.key === 'ArrowDown') { event.preventDefault(); setSelected(el, Math.min(cur + 1, btns.length - 1)); return true; }
                            if (event.key === 'ArrowUp') { event.preventDefault(); setSelected(el, Math.max(cur - 1, 0)); return true; }
                            if (event.key === 'Enter') { event.preventDefault(); btns[cur]?.dispatchEvent(new MouseEvent('mousedown')); return true; }
                            if (event.key === 'Escape') { el.style.display = 'none'; return true; }
                            return false;
                        },
                        onExit() { if (el) el.style.display = 'none'; },
                    };
                },
            }),
        ];
    },
});

// ── Main ──────────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const cfg = window.__NOTE__ ?? {};
    if (!cfg.noteId || !document.getElementById('tiptap-editor')) return;

    const { noteId, csrf, content } = cfg;
    let saveTimer = null;
    let isSaving = false;

    const editor = new Editor({
        element: document.getElementById('tiptap-editor'),
        extensions: [
            StarterKit.configure({ codeBlock: { languageClassPrefix: 'language-' } }),
            Underline,
            Highlight.configure({ multicolor: false }),
            TaskList,
            TaskItem.configure({ nested: true }),
            Table.configure({ resizable: true }),
            TableRow, TableCell, TableHeader,
            Link.configure({ openOnClick: false, HTMLAttributes: { rel: 'noopener noreferrer' } }),
            Image.configure({ inline: false }),
            Placeholder.configure({ placeholder: 'Escreva algo, ou digite / para comandos…' }),
            CharacterCount,
            SlashCommands,
        ],
        content: content || '',
        onUpdate({ editor }) { updateStats(editor); scheduleSave(); },
        onSelectionUpdate({ editor }) { updateToolbar(editor); buildBubbleMenu(editor); },
    });

    // ── Bubble menu ───────────────────────────────────────────────────────────
    function buildBubbleMenu(ed) {
        const sel = ed.state.selection;
        const bubble = document.getElementById('bubble-menu');
        if (!bubble) return;
        if (sel.empty) { bubble.style.display = 'none'; return; }

        const view = ed.view;
        const start = view.coordsAtPos(sel.from);
        const end = view.coordsAtPos(sel.to);

        bubble.style.display = 'flex';
        const bw = bubble.offsetWidth;
        const midX = (start.left + end.right) / 2;
        let left = Math.max(8, Math.min(midX - bw / 2, window.innerWidth - bw - 8));
        bubble.style.left = left + 'px';
        bubble.style.top = (start.top - bubble.offsetHeight - 8) + 'px';

        bubble.querySelectorAll('[data-mark]').forEach(btn => {
            btn.classList.toggle('active', ed.isActive(btn.dataset.mark));
        });
    }

    document.addEventListener('mousedown', e => {
        const b = document.getElementById('bubble-menu');
        if (b && !b.contains(e.target)) b.style.display = 'none';
    });

    document.querySelectorAll('#bubble-menu [data-mark]').forEach(btn => {
        btn.addEventListener('mousedown', e => {
            e.preventDefault();
            ({
                bold: () => editor.chain().focus().toggleBold().run(),
                italic: () => editor.chain().focus().toggleItalic().run(),
                underline: () => editor.chain().focus().toggleUnderline().run(),
                strike: () => editor.chain().focus().toggleStrike().run(),
                highlight: () => editor.chain().focus().toggleHighlight().run(),
                link: () => {
                    const bblBtn = document.querySelector('#bubble-menu .bbl-btn[data-mark="link"]');
                    openLinkPopover(bblBtn);
                },
            })[btn.dataset.mark]?.();
            buildBubbleMenu(editor);
        });
    });

    // ── Stats ─────────────────────────────────────────────────────────────────
    function updateStats(ed) {
        const chars = ed.storage.characterCount.characters();
        const words = ed.storage.characterCount.words();
        document.getElementById('stat-words').textContent = words + ' palavras';
        document.getElementById('stat-chars').textContent = chars + ' caracteres';
        document.getElementById('stat-read').textContent = Math.max(1, Math.round(words / 200)) + ' min leitura';
    }
    updateStats(editor);

    // ── Toolbar ───────────────────────────────────────────────────────────────
    function updateToolbar(ed) {
        document.querySelectorAll('.ttb-btn[data-cmd]').forEach(btn => {
            btn.classList.toggle('active', !!({
                bold: ed.isActive('bold'), italic: ed.isActive('italic'),
                underline: ed.isActive('underline'), strike: ed.isActive('strike'),
                highlight: ed.isActive('highlight'), bulletList: ed.isActive('bulletList'),
                orderedList: ed.isActive('orderedList'), taskList: ed.isActive('taskList'),
                blockquote: ed.isActive('blockquote'), codeBlock: ed.isActive('codeBlock'),
                link: ed.isActive('link'),
            })[btn.dataset.cmd]);
        });
        // Update heading dropdown label + active state
        const hdLabel = document.getElementById('ttb-heading-label');
        const labels = { 0: 'Parágrafo', 1: 'Título 1', 2: 'Título 2', 3: 'Título 3' };
        let activeH = 0;
        if (ed.isActive('heading', { level: 1 })) activeH = 1;
        else if (ed.isActive('heading', { level: 2 })) activeH = 2;
        else if (ed.isActive('heading', { level: 3 })) activeH = 3;
        if (hdLabel) hdLabel.textContent = labels[activeH];
        document.querySelectorAll('#ttb-heading-menu .ttb-dropdown-item').forEach(i => {
            i.classList.toggle('active', parseInt(i.dataset.heading) === activeH);
        });
    }

    document.querySelectorAll('.ttb-btn[data-cmd]').forEach(btn => {
        btn.addEventListener('mousedown', e => {
            e.preventDefault();
            ({
                bold: () => editor.chain().focus().toggleBold().run(),
                italic: () => editor.chain().focus().toggleItalic().run(),
                underline: () => editor.chain().focus().toggleUnderline().run(),
                strike: () => editor.chain().focus().toggleStrike().run(),
                highlight: () => editor.chain().focus().toggleHighlight().run(),
                bulletList: () => editor.chain().focus().toggleBulletList().run(),
                orderedList: () => editor.chain().focus().toggleOrderedList().run(),
                taskList: () => editor.chain().focus().toggleTaskList().run(),
                blockquote: () => editor.chain().focus().toggleBlockquote().run(),
                codeBlock: () => editor.chain().focus().toggleCodeBlock().run(),
                horizontalRule: () => editor.chain().focus().setHorizontalRule().run(),
                insertTable: () => editor.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(),
                addRowAfter: () => editor.chain().focus().addRowAfter().run(),
                deleteRow: () => editor.chain().focus().deleteRow().run(),
                link: () => openLinkPopover(btn),
                image: () => openImagePopover(btn),
                undo: () => editor.chain().focus().undo().run(),
                redo: () => editor.chain().focus().redo().run(),
            })[btn.dataset.cmd]?.();
            updateToolbar(editor);
        });
    });

    // Heading dropdown
    const hdTrigger = document.getElementById('ttb-heading-trigger');
    const hdMenu = document.getElementById('ttb-heading-menu');

    hdTrigger?.addEventListener('mousedown', e => {
        e.preventDefault();
        const isOpen = hdMenu.classList.contains('open');
        hdMenu.classList.toggle('open', !isOpen);
        hdTrigger.classList.toggle('open', !isOpen);
    });

    document.addEventListener('mousedown', e => {
        if (!hdTrigger?.contains(e.target) && !hdMenu?.contains(e.target)) {
            hdMenu?.classList.remove('open');
            hdTrigger?.classList.remove('open');
        }
    });

    hdMenu?.querySelectorAll('.ttb-dropdown-item').forEach(item => {
        item.addEventListener('mousedown', e => {
            e.preventDefault();
            const v = parseInt(item.dataset.heading);
            v === 0 ? editor.chain().focus().setParagraph().run()
                : editor.chain().focus().toggleHeading({ level: v }).run();
            hdMenu.classList.remove('open');
            hdTrigger.classList.remove('open');
            updateToolbar(editor);
        });
    });

    // ── Image popover ─────────────────────────────────────────────────────────
    const popover = document.getElementById('image-popover');
    const tabUrl = document.getElementById('img-tab-url');
    const tabUpload = document.getElementById('img-tab-upload');
    const panelUrl = document.getElementById('img-panel-url');
    const panelUpload = document.getElementById('img-panel-upload');
    const urlInput = document.getElementById('img-url-input');
    const altInput = document.getElementById('img-alt-input');
    const fileInput = document.getElementById('img-file-input');
    const dropZone = document.getElementById('img-drop-zone');
    const previewWrap = document.getElementById('img-preview-wrap');
    const previewImg = document.getElementById('img-preview');
    let pendingBase64 = null;
    let activeTab = 'url';

    function openImagePopover(anchorBtn) {
        urlInput.value = ''; altInput.value = '';
        if (fileInput) fileInput.value = '';
        pendingBase64 = null;
        if (previewWrap) previewWrap.style.display = 'none';
        switchImgTab('url');
        popover.style.display = 'block';

        const pw = popover.offsetWidth || 320;
        const ph = popover.offsetHeight || 220;
        const vw = window.innerWidth;
        const vh = window.innerHeight;

        let top, left;
        if (anchorBtn) {
            const r = anchorBtn.getBoundingClientRect();
            top = r.bottom + 6;
            left = r.left;
        } else {
            // from slash command — center on screen
            top = (vh - ph) / 2;
            left = (vw - pw) / 2;
        }

        if (left + pw > vw - 8) left = vw - pw - 8;
        if (top + ph > vh - 8) top = vh - ph - 8;
        popover.style.left = Math.max(8, left) + 'px';
        popover.style.top = Math.max(8, top) + 'px';
        urlInput.focus();
    }
    window.openImagePopover = openImagePopover;

    function closeImagePopover() {
        popover.style.display = 'none';
        editor.chain().focus().run();
    }

    function switchImgTab(tab) {
        activeTab = tab;
        tabUrl?.classList.toggle('active', tab === 'url');
        tabUpload?.classList.toggle('active', tab === 'upload');
        if (panelUrl) panelUrl.style.display = tab === 'url' ? '' : 'none';
        if (panelUpload) panelUpload.style.display = tab === 'upload' ? '' : 'none';
    }

    tabUrl?.addEventListener('click', () => switchImgTab('url'));
    tabUpload?.addEventListener('click', () => switchImgTab('upload'));

    function handleFile(file) {
        if (!file?.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            pendingBase64 = e.target.result;
            if (previewImg) previewImg.src = pendingBase64;
            if (previewWrap) previewWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    fileInput?.addEventListener('change', () => handleFile(fileInput.files[0]));
    dropZone?.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone?.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone?.addEventListener('drop', e => { e.preventDefault(); dropZone.classList.remove('dragover'); handleFile(e.dataTransfer.files[0]); });

    document.getElementById('tiptap-editor')?.addEventListener('paste', e => {
        const item = [...e.clipboardData.items].find(i => i.type.startsWith('image/'));
        if (!item) return;
        const reader = new FileReader();
        reader.onload = ev => editor.chain().focus().setImage({ src: ev.target.result }).run();
        reader.readAsDataURL(item.getAsFile());
    });

    document.getElementById('img-insert-btn')?.addEventListener('click', () => {
        if (activeTab === 'url') {
            const src = urlInput.value.trim();
            if (!src) { urlInput.focus(); return; }
            editor.chain().focus().setImage({ src, alt: altInput.value.trim() || undefined }).run();
        } else {
            if (!pendingBase64) return;
            editor.chain().focus().setImage({ src: pendingBase64 }).run();
        }
        closeImagePopover();
    });

    document.getElementById('img-cancel-btn')?.addEventListener('click', closeImagePopover);
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeImagePopover(); });
    document.addEventListener('mousedown', e => {
        if (popover?.style.display !== 'none' && !popover?.contains(e.target)) {
            if (!document.querySelector('.ttb-btn[data-cmd="image"]')?.contains(e.target)) closeImagePopover();
        }
    });
    urlInput?.addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('img-insert-btn')?.click(); });

    // ── Link popover ──────────────────────────────────────────────────────────
    const linkPopover = document.getElementById('link-popover');
    const linkUrlInput = document.getElementById('link-url-input');
    const linkTextInput = document.getElementById('link-text-input');

    function openLinkPopover(anchorBtn) {
        if (!linkPopover) return;
        // Pre-fill with existing link if cursor is inside one
        const existingHref = editor.getAttributes('link').href || '';
        linkUrlInput.value = existingHref;
        // Pre-fill text from selection
        const { from, to } = editor.state.selection;
        linkTextInput.value = from !== to ? editor.state.doc.textBetween(from, to) : '';

        // Show remove button only when editing an existing link
        const removeBtn = document.getElementById('link-remove-btn');
        if (removeBtn) removeBtn.style.display = existingHref ? '' : 'none';

        linkPopover.style.display = 'block';

        // Position near anchor button
        const lw = linkPopover.offsetWidth || 320;
        const lh = linkPopover.offsetHeight || 180;
        const vw = window.innerWidth;
        const vh = window.innerHeight;
        let top, left;
        if (anchorBtn) {
            const r = anchorBtn.getBoundingClientRect();
            top = r.bottom + 6;
            left = r.left;
        } else {
            top = (vh - lh) / 2;
            left = (vw - lw) / 2;
        }
        if (left + lw > vw - 8) left = vw - lw - 8;
        if (top + lh > vh - 8) top = vh - lh - 8;
        linkPopover.style.left = Math.max(8, left) + 'px';
        linkPopover.style.top = Math.max(8, top) + 'px';
        linkUrlInput.focus();
    }
    window.openLinkPopover = openLinkPopover;

    function closeLinkPopover() {
        if (linkPopover) linkPopover.style.display = 'none';
        editor.chain().focus().run();
    }

    document.getElementById('link-insert-btn')?.addEventListener('click', () => {
        const href = linkUrlInput.value.trim();
        if (!href) { linkUrlInput.focus(); return; }
        editor.chain().focus().setLink({ href }).run();
        closeLinkPopover();
    });

    document.getElementById('link-remove-btn')?.addEventListener('click', () => {
        editor.chain().focus().unsetLink().run();
        closeLinkPopover();
    });

    document.getElementById('link-cancel-btn')?.addEventListener('click', closeLinkPopover);

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLinkPopover(); });
    document.addEventListener('mousedown', e => {
        if (linkPopover?.style.display !== 'none' && !linkPopover?.contains(e.target)) {
            const isLinkBtn = document.querySelector('.ttb-btn[data-cmd="link"]')?.contains(e.target)
                || document.querySelector('.bbl-btn[data-mark="link"]')?.contains(e.target);
            if (!isLinkBtn) closeLinkPopover();
        }
    });
    linkUrlInput?.addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('link-insert-btn')?.click(); });

    // ── Auto-save ─────────────────────────────────────────────────────────────
    function setStatus(msg, color = 'var(--muted)') {
        const el = document.getElementById('save-status');
        if (!el) return;
        el.textContent = msg; el.style.color = color; el.style.opacity = '1';
    }

    function scheduleSave() {
        clearTimeout(saveTimer);
        setStatus('Editando…');
        saveTimer = setTimeout(saveNote, 1200);
    }

    async function saveNote() {
        if (isSaving) return;
        isSaving = true; setStatus('Salvando…');
        try {
            const title = document.getElementById('note-title')?.value.trim() || 'Sem título';
            const res = await fetch(`/notes/${noteId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ title, content: editor.getHTML() }),
            });
            const data = await res.json();
            if (res.ok) {
                setStatus('✓ Salvo ' + data.updated_at, 'var(--success)');
                const el = document.getElementById('sidebar-updated');
                if (el) el.textContent = data.updated_at;
                setTimeout(() => { const s = document.getElementById('save-status'); if (s) s.style.opacity = '0'; }, 3000);
            } else { setStatus('Erro ao salvar', 'var(--danger)'); }
        } catch { setStatus('Erro de conexão', 'var(--danger)'); }
        finally { isSaving = false; }
    }

    const titleEl = document.getElementById('note-title');
    if (titleEl) {
        const resize = () => { titleEl.style.height = 'auto'; titleEl.style.height = titleEl.scrollHeight + 'px'; };
        resize();
        titleEl.addEventListener('input', () => { resize(); scheduleSave(); });
    }

    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') { e.preventDefault(); saveNote(); }
    });

    document.querySelectorAll('.note-color-dot').forEach(dot => {
        dot.addEventListener('click', async () => {
            document.querySelectorAll('.note-color-dot').forEach(d => d.classList.remove('active'));
            dot.classList.add('active');
            await fetch(`/notes/${noteId}`, {
                method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ color: dot.dataset.color }),
            });
        });
    });

    document.getElementById('note-category')?.addEventListener('change', async function () {
        await fetch(`/notes/${noteId}`, {
            method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ category: this.value || null }),
        });
    });

    document.getElementById('btn-pin')?.addEventListener('click', async function () {
        const isPinned = this.textContent.includes('Fixada');
        const res = await fetch(`/notes/${noteId}`, {
            method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ pinned: !isPinned }),
        });
        if (res.ok) this.textContent = isPinned ? '📌 Fixar' : '📌 Fixada';
    });

    document.getElementById('btn-delete')?.addEventListener('click', function () {
        const doDelete = async () => {
            const res = await fetch(`/notes/${noteId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf } });
            if (res.ok) window.location.href = '/notes';
        };
        window.confirmDialog
            ? window.confirmDialog('Excluir nota', 'Esta ação não pode ser desfeita.', doDelete)
            : confirm('Excluir esta nota?') && doDelete();
    });

    window.addEventListener('beforeunload', () => { if (saveTimer) saveNote(); });
});
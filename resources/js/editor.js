import { Editor, Extension, Node } from '@tiptap/core';
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
import { TextStyle } from '@tiptap/extension-text-style';
import { Color } from '@tiptap/extension-color';
import { TextAlign } from '@tiptap/extension-text-align';
import { Subscript } from '@tiptap/extension-subscript';
import { Superscript } from '@tiptap/extension-superscript';

// ── TextStyle extended with fontFamily ────────────────────────────────────────
const TextStyleWithFont = TextStyle.extend({
    addAttributes() {
        return {
            ...this.parent?.(),
            fontFamily: {
                default: null,
                parseHTML: el => el.style.fontFamily || null,
                renderHTML: attrs => {
                    if (!attrs.fontFamily) return {};
                    return { style: `font-family: ${attrs.fontFamily}` };
                },
            },
        };
    },
});

// ── Callout Node extension ────────────────────────────────────────────────────
const CALLOUT_TYPES = [
    { id: 'info',    label: 'Info',    color: '#3b82f6',
      svg: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#3b82f6"/><rect x="8.2" y="8" width="1.6" height="5.5" rx=".8" fill="white"/><circle cx="9" cy="5.5" r="1.1" fill="white"/></svg>` },
    { id: 'success', label: 'Sucesso', color: '#22c55e',
      svg: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#22c55e"/><path d="M5.5 9.5l2.5 2.5 5-5" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>` },
    { id: 'warning', label: 'Aviso',   color: '#f59e0b',
      svg: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M9 2L16.5 15H1.5L9 2Z" fill="#f59e0b"/><rect x="8.2" y="7" width="1.6" height="4" rx=".8" fill="white"/><circle cx="9" cy="13" r=".9" fill="white"/></svg>` },
    { id: 'danger',  label: 'Perigo',  color: '#ef4444',
      svg: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#ef4444"/><path d="M6 6l6 6M12 6l-6 6" stroke="white" stroke-width="1.8" stroke-linecap="round"/></svg>` },
    { id: 'tip',     label: 'Dica',    color: '#a855f7',
      svg: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#a855f7"/><path d="M9 5a3.5 3.5 0 011.2 6.8V13H7.8v-1.2A3.5 3.5 0 019 5z" fill="white"/><rect x="8.2" y="13.5" width="1.6" height="1.5" rx=".6" fill="white"/></svg>` },
    { id: 'note',    label: 'Nota',    color: '#64748b',
      svg: `<svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="1" y="1" width="16" height="16" rx="3.5" fill="#64748b"/><path d="M5 6.5h8M5 9h8M5 11.5h5" stroke="white" stroke-width="1.5" stroke-linecap="round"/></svg>` },
];

const Callout = Node.create({
    name: 'callout',
    group: 'block',
    content: 'block+',
    defining: true,
    isolating: true,

    addAttributes() {
        return {
            type: { default: 'info' },
        };
    },

    parseHTML() {
        return [{ tag: 'div[data-callout]', getAttrs: el => ({ type: el.getAttribute('data-callout') }) }];
    },

    renderHTML({ HTMLAttributes }) {
        const t = CALLOUT_TYPES.find(c => c.id === HTMLAttributes.type) || CALLOUT_TYPES[0];
        return ['div', { 'data-callout': HTMLAttributes.type }, ['div', { class: 'callout-content' }, 0]];
    },

    addNodeView() {
        return ({ node, editor, getPos }) => {
            const t = CALLOUT_TYPES.find(c => c.id === node.attrs.type) || CALLOUT_TYPES[0];
            const light = document.documentElement.getAttribute('data-theme') === 'light';

            // Outer wrapper
            const dom = document.createElement('div');
            dom.setAttribute('data-callout', node.attrs.type);
            dom.className = 'callout-block';
            dom.style.cssText = `
                display:flex; gap:12px; align-items:flex-start;
                border-left: 3px solid ${t.color};
                background: ${hexToRgba(t.color, light ? .07 : .1)};
                border-radius: 0 10px 10px 0;
                padding: 12px 16px 12px 14px;
                margin: 10px 0; position: relative;
            `;

            // Icon + type selector button
            const iconBtn = document.createElement('button');
            iconBtn.type = 'button';
            iconBtn.className = 'callout-icon-btn';
            iconBtn.title = 'Mudar tipo';
            iconBtn.style.cssText = `
                background:none; border:none; cursor:pointer; font-size:18px;
                padding:0; line-height:1; flex-shrink:0; margin-top:1px;
                border-radius:4px; transition:transform .15s;
                user-select:none; -webkit-user-select:none;
            `;
            iconBtn.innerHTML = t.svg;

            // Type picker dropdown
            const picker = document.createElement('div');
            picker.className = 'callout-type-picker';
            picker.style.cssText = `
                display:none; position:absolute; left:10px; top:calc(100% + 6px);
                background:${light ? '#fff' : '#1e1e28'};
                border:1px solid ${light ? '#dddde6' : '#2e2e3e'};
                border-radius:10px; padding:5px; z-index:9000;
                box-shadow:0 10px 30px rgba(0,0,0,.3);
                display:none; flex-direction:column; gap:2px; min-width:130px;
            `;

            CALLOUT_TYPES.forEach(ct => {
                const opt = document.createElement('button');
                opt.type = 'button';
                opt.style.cssText = `
                    display:flex; align-items:center; gap:8px;
                    background:none; border:none; cursor:pointer;
                    padding:6px 10px; border-radius:6px; width:100%;
                    font-size:13px; font-family:inherit; text-align:left;
                    color:${light ? '#18181c' : '#e8e8f0'};
                    transition:background .1s;
                `;
                opt.innerHTML = `<span style="width:20px;display:flex;align-items:center;justify-content:center;flex-shrink:0">${ct.svg}</span><span style="flex:1">${ct.label}</span>`;
                if (ct.id === node.attrs.type) opt.style.background = `${hexToRgba(ct.color, .15)}`;
                opt.addEventListener('mouseover', () => { opt.style.background = hexToRgba(ct.color, .15); });
                opt.addEventListener('mouseout',  () => { opt.style.background = ct.id === node.attrs.type ? hexToRgba(ct.color, .15) : 'none'; });
                opt.addEventListener('mousedown', e => {
                    e.preventDefault();
                    if (typeof getPos === 'function') {
                        editor.chain().focus().command(({ tr }) => {
                            tr.setNodeMarkup(getPos(), undefined, { type: ct.id });
                            return true;
                        }).run();
                    }
                    picker.style.display = 'none';
                });
                picker.appendChild(opt);
            });

            iconBtn.addEventListener('mousedown', e => {
                e.preventDefault();
                e.stopPropagation();
                const isOpen = picker.style.display === 'flex';
                picker.style.display = isOpen ? 'none' : 'flex';
            });

            // Close picker on outside click
            document.addEventListener('mousedown', function closePicker(e) {
                if (!picker.contains(e.target) && e.target !== iconBtn) {
                    picker.style.display = 'none';
                }
            });

            // Content area — editable
            const content = document.createElement('div');
            content.className = 'callout-content';
            content.style.cssText = 'flex:1; min-width:0; padding-top:1px;';

            dom.appendChild(iconBtn);
            dom.appendChild(picker);
            dom.appendChild(content);

            return { dom, contentDOM: content };
        };
    },

    addCommands() {
        return {
            setCallout: (type = 'info') => ({ commands }) =>
                commands.wrapIn(this.type, { type }),
            insertCallout: (type = 'info') => ({ chain }) =>
                chain().insertContent({
                    type: this.name,
                    attrs: { type },
                    content: [{ type: 'paragraph' }],
                }).run(),
        };
    },
});

function hexToRgba(hex, alpha) {
    const r = parseInt(hex.slice(1,3), 16);
    const g = parseInt(hex.slice(3,5), 16);
    const b = parseInt(hex.slice(5,7), 16);
    return `rgba(${r},${g},${b},${alpha})`;
}

// ── Editor font options ───────────────────────────────────────────────────────
const FONT_OPTIONS = [
    { id: 'sans',  label: 'Sans-serif', family: "'DM Sans', sans-serif",             desc: 'Padrão'  },
    { id: 'serif', label: 'Serif',      family: "'Georgia', 'Times New Roman', serif", desc: 'Leitura' },
    { id: 'mono',  label: 'Monospace',  family: "'DM Mono', 'Courier New', monospace", desc: 'Código'  },
];
const FONT_STORAGE_KEY = 'taskletto-editor-font';

// ── Emoji groups ──────────────────────────────────────────────────────────────
const EMOJI_GROUPS = [
    { label: '😀 Rostos', emojis: ['😀','😃','😄','😁','😆','😅','🤣','😂','🙂','🙃','😉','😊','😇','🥰','😍','🤩','😘','😚','😋','😛','😜','😝','🤑','🤗','🤔','🤐','🤨','😐','😑','😶','😏','😒','🙄','😬','😌','😔','😪','😴','😷','🤒','🥴','😵','🤯','🤠','🥳','😎','🤓','🧐','😕','😟','🙁','☹️','😮','😲','😳','🥺','😦','😧','😨','😰','😥','😢','😭','😱','😖','😣','😞','😓','😩','😫','🥱','😤','😡','😠','🤬','😈','👿'] },
    { label: '👍 Gestos', emojis: ['👍','👎','👊','✊','🤛','🤜','🤞','✌️','🤟','🤘','👌','🤌','👈','👉','👆','👇','☝️','👋','🤚','🖐️','✋','🖖','👏','🙌','🤲','🤝','🙏','✍️','💪'] },
    { label: '❤️ Símbolos', emojis: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💔','❣️','💕','💞','💓','💗','💖','💘','💝','🔥','💫','⭐','🌟','✨','💥','❄️','🌈','☁️','🌙','☀️','🌊','💧','🌿','🍀','🌸','🌺','🌻','🌹','🌷','🎯','🎲','🎮','🎵','🎶','🎉','🎊','🎁','🎈','🏆','🥇'] },
    { label: '✅ Úteis', emojis: ['✅','❌','⚠️','ℹ️','❓','❗','💡','🔔','📌','📍','🔑','🔒','🔓','📎','✂️','🗑️','📋','📄','📝','✏️','🖊️','📅','📆','⏰','⌚','🔍','🔎','💬','💭','📢','🚀','💎','🛠️','⚙️','🔧','🔨','🧩','📦','📬','🌐','💻','🖥️','📱','📷','🎥'] },
];

// ── Slash commands ────────────────────────────────────────────────────────────
const SLASH_ITEMS = [
    { group: 'Texto',    icon: '¶',  label: 'Parágrafo',     desc: 'Texto normal',     cmd: (e,r) => e.chain().focus().deleteRange(r).setParagraph().run() },
    { group: 'Texto',    icon: 'H1', label: 'Título 1',       desc: 'Título grande',    cmd: (e,r) => e.chain().focus().deleteRange(r).setHeading({level:1}).run() },
    { group: 'Texto',    icon: 'H2', label: 'Título 2',       desc: 'Título médio',     cmd: (e,r) => e.chain().focus().deleteRange(r).setHeading({level:2}).run() },
    { group: 'Texto',    icon: 'H3', label: 'Título 3',       desc: 'Título pequeno',   cmd: (e,r) => e.chain().focus().deleteRange(r).setHeading({level:3}).run() },
    { group: 'Listas',   icon: '•',  label: 'Lista',          desc: 'Com marcadores',   cmd: (e,r) => e.chain().focus().deleteRange(r).toggleBulletList().run() },
    { group: 'Listas',   icon: '1.', label: 'Lista numerada', desc: 'Com números',      cmd: (e,r) => e.chain().focus().deleteRange(r).toggleOrderedList().run() },
    { group: 'Listas',   icon: '✓',  label: 'Checklist',      desc: 'Lista de tarefas', cmd: (e,r) => e.chain().focus().deleteRange(r).toggleTaskList().run() },
    { group: 'Blocos',   icon: '"',  label: 'Citação',        desc: 'Bloco de citação', cmd: (e,r) => e.chain().focus().deleteRange(r).toggleBlockquote().run() },
    { group: 'Blocos',   icon: '<>', label: 'Código',         desc: 'Bloco de código',  cmd: (e,r) => e.chain().focus().deleteRange(r).toggleCodeBlock().run() },
    { group: 'Blocos',   icon: '—',  label: 'Divisória',      desc: 'Linha horizontal', cmd: (e,r) => e.chain().focus().deleteRange(r).setHorizontalRule().run() },
    { group: 'Conteúdo', icon: '⊞', label: 'Tabela',         desc: 'Tabela 3×3',       cmd: (e,r) => e.chain().focus().deleteRange(r).insertTable({rows:3,cols:3,withHeaderRow:true}).run() },
    { group: 'Conteúdo', icon: '🖼', label: 'Imagem',         desc: 'Inserir imagem',   cmd: (e,r) => { e.chain().focus().deleteRange(r).run(); window.openImagePopover?.(); } },
    { group: 'Conteúdo', icon: '😀', label: 'Emoji',          desc: 'Escolher emoji',   cmd: (e,r) => { e.chain().focus().deleteRange(r).run(); window.openEmojiPicker?.(); } },
    { group: 'Callout',  icon: 'ℹ️', label: 'Info',           desc: 'Bloco informativo', cmd: (e,r) => e.chain().focus().deleteRange(r).insertCallout('info').run()    },
    { group: 'Callout',  icon: '✅', label: 'Sucesso',         desc: 'Bloco de sucesso', cmd: (e,r) => e.chain().focus().deleteRange(r).insertCallout('success').run() },
    { group: 'Callout',  icon: '⚠️', label: 'Aviso',          desc: 'Bloco de aviso',   cmd: (e,r) => e.chain().focus().deleteRange(r).insertCallout('warning').run() },
    { group: 'Callout',  icon: '❌', label: 'Perigo',          desc: 'Bloco de perigo',  cmd: (e,r) => e.chain().focus().deleteRange(r).insertCallout('danger').run()  },
    { group: 'Callout',  icon: '💡', label: 'Dica',            desc: 'Bloco de dica',    cmd: (e,r) => e.chain().focus().deleteRange(r).insertCallout('tip').run()     },
    { group: 'Callout',  icon: '📝', label: 'Nota',            desc: 'Bloco de nota',    cmd: (e,r) => e.chain().focus().deleteRange(r).insertCallout('note').run()    },
];

// ── Slash menu helpers ────────────────────────────────────────────────────────
function getOrCreateSlashMenu() {
    let el = document.getElementById('slash-menu');
    if (el) return el;
    el = document.createElement('div');
    el.id = 'slash-menu';
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
        'max-height:360px', 'overflow-y:auto', 'scrollbar-width:thin',
        `scrollbar-color:${light ? '#ccc' : '#333'} transparent`,
        'font-family:inherit',
    ].join(';');
}

function renderSlashMenu(items, el, onSelect) {
    el.innerHTML = '';
    const light    = document.documentElement.getAttribute('data-theme') === 'light';
    const iconBg   = light ? '#ececf0' : '#2a2a35';
    const iconCol  = light ? '#333344' : '#c8c8d8';
    const textCol  = light ? '#18181c' : '#e8e8f0';
    const mutedCol = light ? '#77778a' : '#8888a0';
    const sepCol   = light ? '#e0e0ea' : '#2e2e3e';

    if (!items.length) {
        const empty = document.createElement('div');
        empty.style.cssText = `padding:10px 12px;font-size:12.5px;color:${mutedCol}`;
        empty.textContent = 'Nenhum comando';
        el.appendChild(empty);
        return;
    }

    let lastGroup = null;
    let btnIdx = 0;
    items.forEach(item => {
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
        const myIdx = btnIdx++;
        row.dataset.idx = myIdx;
        row.style.cssText = `display:flex;align-items:center;gap:11px;width:100%;padding:8px 10px;border-radius:8px;border:none;background:none;cursor:pointer;text-align:left;transition:background .1s;font-family:inherit`;
        const icon = document.createElement('span');
        icon.style.cssText = `width:32px;height:32px;display:flex;align-items:center;justify-content:center;background:${iconBg};border-radius:7px;font-size:13px;font-weight:700;flex-shrink:0;color:${iconCol};font-family:'DM Mono',monospace`;
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
        row.addEventListener('mouseenter', () => setSlashSelected(el, myIdx));
        row.addEventListener('mousedown', e => { e.preventDefault(); onSelect(item); });
        el.appendChild(row);
    });
    setSlashSelected(el, 0);
}

function setSlashSelected(el, idx) {
    const btns = [...el.querySelectorAll('button[data-idx]')];
    if (!btns.length) return 0;
    idx = Math.max(0, Math.min(idx, btns.length - 1));
    btns.forEach(b => {
        b.style.background = 'none';
        b.classList.remove('slash-selected');
    });
    btns[idx].style.background = 'rgba(255,145,77,.13)';
    btns[idx].classList.add('slash-selected');
    btns[idx].scrollIntoView({ block: 'nearest' });
    return idx;
}

function getSlashSelectedIdx(el) {
    const btns = [...el.querySelectorAll('button[data-idx]')];
    const found = btns.findIndex(b => b.classList.contains('slash-selected'));
    return found >= 0 ? found : 0;
}

function placeMenu(el, rect) {
    if (!rect) return;
    el.style.visibility = 'hidden';
    el.style.display = 'block';
    const mw = el.offsetWidth, mh = el.offsetHeight;
    el.style.visibility = '';
    const vw = window.innerWidth, vh = window.innerHeight;
    let top = rect.bottom + 6;
    if (top + mh > vh - 8) top = rect.top - mh - 6;
    let left = rect.left;
    if (left + mw > vw - 8) left = vw - mw - 8;
    el.style.top  = Math.max(8, top) + 'px';
    el.style.left = Math.max(8, left) + 'px';
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
                            if (!el || el.style.display === 'none') return false;
                            const btns = [...el.querySelectorAll('button[data-idx]')];
                            if (!btns.length) return false;
                            const cur = getSlashSelectedIdx(el);
                            if (event.key === 'ArrowDown') {
                                event.preventDefault();
                                setSlashSelected(el, cur + 1);
                                return true;
                            }
                            if (event.key === 'ArrowUp') {
                                event.preventDefault();
                                setSlashSelected(el, cur - 1);
                                return true;
                            }
                            if (event.key === 'Enter') {
                                event.preventDefault();
                                btns[cur]?.dispatchEvent(new MouseEvent('mousedown', { bubbles: true }));
                                return true;
                            }
                            if (event.key === 'Escape') {
                                el.style.display = 'none';
                                return true;
                            }
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
    let isSaving  = false;

    // ── Editor font (applies to selection only via TextStyle) ────────────────
    const savedFont = localStorage.getItem(FONT_STORAGE_KEY) || 'sans';

    function applyFontToSelection(fontId) {
        const opt = FONT_OPTIONS.find(f => f.id === fontId) || FONT_OPTIONS[0];
        // Apply to selected text, or set as "next character" mark if no selection
        if (fontId === 'sans') {
            editor.chain().focus().unsetMark('textStyle').run();
        } else {
            editor.chain().focus().setMark('textStyle', { fontFamily: opt.family }).run();
        }
        // Update dropdown label to show last chosen
        const lbl = document.getElementById('ttb-font-label');
        if (lbl) lbl.textContent = opt.label;
        document.querySelectorAll('.ttb-font-item').forEach(el => {
            el.classList.toggle('active', el.dataset.font === fontId);
        });
        localStorage.setItem(FONT_STORAGE_KEY, fontId);
    }

    // ── Editor init ───────────────────────────────────────────────────────────
    const editor = new Editor({
        element: document.getElementById('tiptap-editor'),
        extensions: [
            StarterKit.configure({ codeBlock: { languageClassPrefix: 'language-' } }),
            Underline,
            TextStyleWithFont,
            Color,
            TextAlign.configure({ types: ['heading', 'paragraph'] }),
            Subscript,
            Superscript,
            Highlight.configure({ multicolor: false }),
            TaskList,
            TaskItem.configure({ nested: true }),
            Table.configure({ resizable: true }),
            TableRow, TableCell, TableHeader,
            Link.configure({ openOnClick: false, HTMLAttributes: { rel: 'noopener noreferrer' } }),
            Image.configure({ inline: false }),
            Callout,
            Placeholder.configure({ placeholder: 'Escreva algo ou digite  /  para inserir blocos…' }),
            CharacterCount,
            SlashCommands,
        ],
        content: content || '',
        onUpdate({ editor })          { updateStats(editor); scheduleSave(); },
        onSelectionUpdate({ editor }) { updateToolbar(editor); buildBubbleMenu(editor); buildTableMenu(editor); },
    });

    // ── Text selection bubble menu ────────────────────────────────────────────
    function buildBubbleMenu(ed) {
        const sel    = ed.state.selection;
        const bubble = document.getElementById('bubble-menu');
        if (!bubble) return;
        if (sel.empty) { bubble.style.display = 'none'; return; }
        const view  = ed.view;
        const start = view.coordsAtPos(sel.from);
        const end   = view.coordsAtPos(sel.to);
        bubble.style.display = 'flex';
        const bw   = bubble.offsetWidth;
        const midX = (start.left + end.right) / 2;
        bubble.style.left = Math.max(8, Math.min(midX - bw/2, window.innerWidth - bw - 8)) + 'px';
        bubble.style.top  = (start.top - bubble.offsetHeight - 8) + 'px';
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
                bold:      () => editor.chain().focus().toggleBold().run(),
                italic:    () => editor.chain().focus().toggleItalic().run(),
                underline: () => editor.chain().focus().toggleUnderline().run(),
                strike:    () => editor.chain().focus().toggleStrike().run(),
                highlight: () => editor.chain().focus().toggleHighlight().run(),
                link:      () => openLinkPopover(document.querySelector('#bubble-menu .bbl-btn[data-mark="link"]')),
            })[btn.dataset.mark]?.();
            buildBubbleMenu(editor);
        });
    });

    // ── Table floating toolbar ────────────────────────────────────────────────
    let tableMenu = null;

    function getOrCreateTableMenu() {
        if (tableMenu) return tableMenu;
        const light = document.documentElement.getAttribute('data-theme') === 'light';
        tableMenu = document.createElement('div');
        tableMenu.id = 'table-bubble-menu';
        tableMenu.style.cssText = [
            'display:none', 'position:fixed', 'z-index:9998',
            `background:${light ? '#ffffff' : 'var(--surface)'}`,
            `border:1px solid ${light ? '#dddde6' : 'var(--border)'}`,
            'border-radius:10px', 'padding:4px', 'gap:3px',
            `box-shadow:0 8px 30px ${light ? 'rgba(0,0,0,.12)' : 'rgba(0,0,0,.5)'}`,
            'align-items:center', 'flex-wrap:wrap', 'max-width:340px',
        ].join(';');

        const ACTIONS = [
            { label: '+ Col →',  title: 'Adicionar coluna à direita',  cmd: () => editor.chain().focus().addColumnAfter().run()  },
            { label: '+ Col ←',  title: 'Adicionar coluna à esquerda', cmd: () => editor.chain().focus().addColumnBefore().run() },
            { label: '− Col',    title: 'Remover coluna',               cmd: () => editor.chain().focus().deleteColumn().run()    },
            { label: '+ Linha ↓',title: 'Adicionar linha abaixo',       cmd: () => editor.chain().focus().addRowAfter().run()     },
            { label: '+ Linha ↑',title: 'Adicionar linha acima',        cmd: () => editor.chain().focus().addRowBefore().run()    },
            { label: '− Linha',  title: 'Remover linha',                cmd: () => editor.chain().focus().deleteRow().run()       },
            { label: '⇔ Mesclar',title: 'Mesclar células selecionadas', cmd: () => editor.chain().focus().mergeCells().run()      },
            { label: '⇔ Dividir',title: 'Dividir célula mesclada',      cmd: () => editor.chain().focus().splitCell().run()       },
            { label: '🗑',        title: 'Excluir tabela inteira',       cmd: () => editor.chain().focus().deleteTable().run()     },
        ];

        ACTIONS.forEach((action, i) => {
            if (i === 3 || i === 6 || i === 8) {
                const sep = document.createElement('div');
                sep.style.cssText = 'width:1px;height:18px;background:var(--border);margin:0 2px;flex-shrink:0';
                tableMenu.appendChild(sep);
            }
            const btn = document.createElement('button');
            btn.title = action.title;
            btn.textContent = action.label;
            btn.style.cssText = [
                'border:none', 'border-radius:6px', 'padding:4px 8px',
                'font-size:11px', 'font-family:inherit', 'cursor:pointer',
                'white-space:nowrap', 'transition:background .1s, color .1s',
                `background:${light ? '#f0f0f4' : 'var(--surface2)'}`,
                `color:${light ? '#444' : 'var(--muted)'}`,
            ].join(';');
            btn.addEventListener('mouseover', () => {
                btn.style.background = 'rgba(255,145,77,.18)';
                btn.style.color = 'var(--accent)';
            });
            btn.addEventListener('mouseout', () => {
                btn.style.background = light ? '#f0f0f4' : 'var(--surface2)';
                btn.style.color = light ? '#444' : 'var(--muted)';
            });
            btn.addEventListener('mousedown', e => { e.preventDefault(); action.cmd(); });
            tableMenu.appendChild(btn);
        });

        document.body.appendChild(tableMenu);
        return tableMenu;
    }

    function buildTableMenu(ed) {
        const menu = getOrCreateTableMenu();
        if (!ed.isActive('table')) { menu.style.display = 'none'; return; }
        const coords = ed.view.coordsAtPos(ed.state.selection.from);
        menu.style.display = 'flex';
        const mw = menu.offsetWidth || 320, mh = menu.offsetHeight || 38;
        const vw = window.innerWidth, vh = window.innerHeight;
        let top  = coords.bottom + 8;
        if (top + mh > vh - 8) top = coords.top - mh - 8;
        let left = coords.left;
        if (left + mw > vw - 8) left = vw - mw - 8;
        menu.style.top  = Math.max(8, top)  + 'px';
        menu.style.left = Math.max(8, left) + 'px';
    }

    document.addEventListener('mousedown', e => {
        const menu = document.getElementById('table-bubble-menu');
        if (menu && !menu.contains(e.target) && !e.target.closest('.ProseMirror table')) {
            menu.style.display = 'none';
        }
    });

    // ── Stats ─────────────────────────────────────────────────────────────────
    function updateStats(ed) {
        const chars = ed.storage.characterCount.characters();
        const words = ed.storage.characterCount.words();
        const i18n  = window.TASKLETTO_I18N || {};
        const sw = document.getElementById('stat-words');
        const sc = document.getElementById('stat-chars');
        const sr = document.getElementById('stat-read');
        if (sw) sw.textContent = words + ' ' + (i18n.words    || 'palavras');
        if (sc) sc.textContent = chars + ' ' + (i18n.chars    || 'chars');
        if (sr) sr.textContent = Math.max(1, Math.round(words/200)) + ' ' + (i18n.read_min || 'min');
    }
    updateStats(editor);

    // ── Toolbar state update ──────────────────────────────────────────────────
    function updateToolbar(ed) {
        document.querySelectorAll('.ttb-btn[data-cmd]').forEach(btn => {
            btn.classList.toggle('active', !!({
                bold:         ed.isActive('bold'),
                italic:       ed.isActive('italic'),
                underline:    ed.isActive('underline'),
                strike:       ed.isActive('strike'),
                highlight:    ed.isActive('highlight'),
                bulletList:   ed.isActive('bulletList'),
                orderedList:  ed.isActive('orderedList'),
                taskList:     ed.isActive('taskList'),
                blockquote:   ed.isActive('blockquote'),
                codeBlock:    ed.isActive('codeBlock'),
                link:         ed.isActive('link'),
                subscript:    ed.isActive('subscript'),
                superscript:  ed.isActive('superscript'),
                alignLeft:    ed.isActive({ textAlign: 'left' }),
                alignCenter:  ed.isActive({ textAlign: 'center' }),
                alignRight:   ed.isActive({ textAlign: 'right' }),
                alignJustify: ed.isActive({ textAlign: 'justify' }),
            })[btn.dataset.cmd]);
        });

        const colorBar = document.getElementById('ttb-color-bar');
        if (colorBar) {
            const attrs = ed.getAttributes('textStyle');
            colorBar.style.background = attrs?.color || 'var(--accent)';
        }

        const hdLabel = document.getElementById('ttb-heading-label');
        const labels  = { 0:'Parágrafo', 1:'Título 1', 2:'Título 2', 3:'Título 3' };
        let activeH = 0;
        if (ed.isActive('heading', { level:1 })) activeH = 1;
        else if (ed.isActive('heading', { level:2 })) activeH = 2;
        else if (ed.isActive('heading', { level:3 })) activeH = 3;
        if (hdLabel) hdLabel.textContent = labels[activeH];
        document.querySelectorAll('#ttb-heading-menu .ttb-dropdown-item').forEach(i => {
            i.classList.toggle('active', parseInt(i.dataset.heading) === activeH);
        });
    }

    // ── Toolbar commands ──────────────────────────────────────────────────────
    document.querySelectorAll('.ttb-btn[data-cmd]').forEach(btn => {
        btn.addEventListener('mousedown', e => {
            e.preventDefault();
            ({
                bold:           () => editor.chain().focus().toggleBold().run(),
                italic:         () => editor.chain().focus().toggleItalic().run(),
                underline:      () => editor.chain().focus().toggleUnderline().run(),
                strike:         () => editor.chain().focus().toggleStrike().run(),
                highlight:      () => editor.chain().focus().toggleHighlight().run(),
                subscript:      () => editor.chain().focus().toggleSubscript().run(),
                superscript:    () => editor.chain().focus().toggleSuperscript().run(),
                alignLeft:      () => editor.chain().focus().setTextAlign('left').run(),
                alignCenter:    () => editor.chain().focus().setTextAlign('center').run(),
                alignRight:     () => editor.chain().focus().setTextAlign('right').run(),
                alignJustify:   () => editor.chain().focus().setTextAlign('justify').run(),
                bulletList:     () => editor.chain().focus().toggleBulletList().run(),
                orderedList:    () => editor.chain().focus().toggleOrderedList().run(),
                taskList:       () => editor.chain().focus().toggleTaskList().run(),
                blockquote:     () => editor.chain().focus().toggleBlockquote().run(),
                codeBlock:      () => editor.chain().focus().toggleCodeBlock().run(),
                horizontalRule: () => editor.chain().focus().setHorizontalRule().run(),
                insertTable:    () => editor.chain().focus().insertTable({rows:3,cols:3,withHeaderRow:true}).run(),
                addRowAfter:    () => editor.chain().focus().addRowAfter().run(),
                deleteRow:      () => editor.chain().focus().deleteRow().run(),
                link:           () => openLinkPopover(btn),
                image:          () => openImagePopover(btn),
                emoji:          () => openEmojiPicker(btn),
                undo:           () => editor.chain().focus().undo().run(),
                redo:           () => editor.chain().focus().redo().run(),
            })[btn.dataset.cmd]?.();
            updateToolbar(editor);
        });
    });

    // ── Text color palette ────────────────────────────────────────────────────
    const colorTrigger = document.getElementById('ttb-color-trigger');
    const colorPalette = document.getElementById('ttb-color-palette');
    colorTrigger?.addEventListener('mousedown', e => {
        e.preventDefault();
        colorPalette?.classList.toggle('open');
    });
    document.addEventListener('mousedown', e => {
        if (!colorTrigger?.contains(e.target) && !colorPalette?.contains(e.target)) {
            colorPalette?.classList.remove('open');
        }
    });
    document.getElementById('ttb-color-grid')?.addEventListener('mousedown', e => {
        e.preventDefault();
        const swatch = e.target.closest('.ttb-color-swatch');
        if (!swatch) return;
        const color = swatch.dataset.color;
        editor.chain().focus().setColor(color).run();
        document.querySelectorAll('.ttb-color-swatch').forEach(s => s.classList.toggle('active', s.dataset.color === color));
        const bar = document.getElementById('ttb-color-bar');
        if (bar) bar.style.background = color;
        colorPalette?.classList.remove('open');
        updateToolbar(editor);
    });
    document.getElementById('ttb-color-remove')?.addEventListener('mousedown', e => {
        e.preventDefault();
        editor.chain().focus().unsetColor().run();
        document.querySelectorAll('.ttb-color-swatch').forEach(s => s.classList.remove('active'));
        const bar = document.getElementById('ttb-color-bar');
        if (bar) bar.style.background = 'var(--accent)';
        colorPalette?.classList.remove('open');
        updateToolbar(editor);
    });

    // ── Shared dropdown positioning ──────────────────────────────────────────
    function positionMenu(trigger, menu) {
        const r = trigger.getBoundingClientRect();
        menu.style.top   = (r.bottom + 4) + 'px';
        menu.style.left  = r.left + 'px';
        menu.style.right = 'auto';
        requestAnimationFrame(() => {
            const mw = menu.offsetWidth;
            if (r.left + mw > window.innerWidth - 8) {
                menu.style.left  = 'auto';
                menu.style.right = (window.innerWidth - r.right) + 'px';
            }
        });
    }

    // ── Font picker ───────────────────────────────────────────────────────────
    (function initFontPicker() {
        const trigger = document.getElementById('ttb-font-trigger');
        const menu    = document.getElementById('ttb-font-menu');
        if (!trigger || !menu) return;

        menu.innerHTML = '';
        FONT_OPTIONS.forEach(opt => {
            const item = document.createElement('button');
            item.type = 'button';
            item.dataset.font = opt.id;
            item.className = 'ttb-dropdown-item ttb-font-item';
            item.style.fontFamily = opt.family;
            item.style.display = 'flex';
            item.style.alignItems = 'center';
            item.style.gap = '8px';
            item.innerHTML = `<span style="font-size:13px;font-weight:600;flex:1">${opt.label}</span><span style="font-size:11px;opacity:.5">${opt.desc}</span>`;
            item.addEventListener('mousedown', e => {
                e.preventDefault();
                applyFontToSelection(opt.id);
                menu.classList.remove('open');
                trigger.classList.remove('open');
            });
            menu.appendChild(item);
        });

        trigger.addEventListener('mousedown', e => {
            e.preventDefault();
            e.stopPropagation();
            const wasOpen = menu.classList.contains('open');
            document.querySelectorAll('.ttb-dropdown-menu.open, .ttb-more-menu.open')
                .forEach(m => { if (m !== menu) m.classList.remove('open'); });
            document.querySelectorAll('.ttb-dropdown-trigger.open, .ttb-more-btn.open')
                .forEach(b => { if (b !== trigger) b.classList.remove('open'); });
            if (!wasOpen) {
                menu.classList.add('open');
                trigger.classList.add('open');
                positionMenu(trigger, menu);
            } else {
                menu.classList.remove('open');
                trigger.classList.remove('open');
            }
        });
        document.addEventListener('mousedown', e => {
            if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('open');
                trigger.classList.remove('open');
            }
        });
    })();

    // ── Heading dropdown ──────────────────────────────────────────────────────
    const hdTrigger = document.getElementById('ttb-heading-trigger');
    const hdMenu    = document.getElementById('ttb-heading-menu');

    hdTrigger?.addEventListener('mousedown', e => {
        e.preventDefault();
        e.stopPropagation();
        const wasOpen = hdMenu.classList.contains('open');
        document.querySelectorAll('.ttb-dropdown-menu.open, .ttb-more-menu.open')
            .forEach(m => { if (m !== hdMenu) m.classList.remove('open'); });
        document.querySelectorAll('.ttb-dropdown-trigger.open, .ttb-more-btn.open')
            .forEach(b => { if (b !== hdTrigger) b.classList.remove('open'); });
        if (!wasOpen) {
            hdMenu.classList.add('open');
            hdTrigger.classList.add('open');
            positionMenu(hdTrigger, hdMenu);
        } else {
            hdMenu.classList.remove('open');
            hdTrigger.classList.remove('open');
        }
    });
    hdTrigger?.addEventListener('click', e => e.stopPropagation());
    hdMenu?.addEventListener('mousedown', e => e.stopPropagation());
    hdMenu?.addEventListener('click',     e => e.stopPropagation());
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

    // ── Callout dropdown ──────────────────────────────────────────────────────
    (function initCalloutDropdown() {
        const trigger = document.getElementById('ttb-callout-trigger');
        const menu    = document.getElementById('ttb-callout-menu');
        if (!trigger || !menu) return;

        trigger.addEventListener('mousedown', e => {
            e.preventDefault();
            e.stopPropagation();
            const wasOpen = menu.classList.contains('open');
            document.querySelectorAll('.ttb-dropdown-menu.open, .ttb-more-menu.open')
                .forEach(m => { if (m !== menu) m.classList.remove('open'); });
            document.querySelectorAll('.ttb-dropdown-trigger.open, .ttb-more-btn.open')
                .forEach(b => { if (b !== trigger) b.classList.remove('open'); });
            if (!wasOpen) {
                menu.classList.add('open');
                trigger.classList.add('open');
                positionMenu(trigger, menu);
            } else {
                menu.classList.remove('open');
                trigger.classList.remove('open');
            }
        });
        document.addEventListener('mousedown', e => {
            if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.remove('open');
                trigger.classList.remove('open');
            }
        });

        menu.querySelectorAll('.ttb-callout-item').forEach(item => {
            item.addEventListener('mousedown', e => {
                e.preventDefault();
                const type = item.dataset.callout;
                editor.chain().focus().insertCallout(type).run();
                // Update trigger icon - get the emoji/icon from CALLOUT_TYPES
                const ct = CALLOUT_TYPES.find(c => c.id === type);
                const icon = document.getElementById('ttb-callout-icon');
                if (icon && ct) icon.innerHTML = ct.svg;
                menu.classList.remove('open');
                trigger.classList.remove('open');
            });
        });
    })();

    // ── Emoji picker ──────────────────────────────────────────────────────────
    let emojiPicker = null;

    function buildEmojiPicker() {
        if (emojiPicker) return emojiPicker;
        const light = document.documentElement.getAttribute('data-theme') === 'light';
        emojiPicker = document.createElement('div');
        emojiPicker.id = 'emoji-picker';
        emojiPicker.style.cssText = [
            'display:none', 'position:fixed', 'z-index:9999',
            `background:${light ? '#ffffff' : '#1e1e28'}`,
            `border:1px solid ${light ? '#dddde6' : '#2e2e3e'}`,
            'border-radius:14px', 'padding:12px', 'width:304px',
            `box-shadow:0 16px 50px ${light ? 'rgba(0,0,0,.15)' : 'rgba(0,0,0,.6)'}`,
            'max-height:340px', 'overflow-y:auto', 'scrollbar-width:thin',
        ].join(';');

        // Search bar
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.placeholder = 'Buscar emoji…';
        searchInput.id = 'emoji-search';
        searchInput.style.cssText = [
            'width:100%;box-sizing:border-box;padding:7px 10px',
            `background:${light ? '#f0f0f4' : '#2a2a35'}`,
            `border:1px solid ${light ? '#dddde6' : '#3a3a48'}`,
            'border-radius:8px;outline:none;margin-bottom:10px;display:block',
            `color:${light ? '#18181c' : '#e8e8f0'}`,
            'font-size:13px;font-family:inherit',
        ].join(';');
        emojiPicker.appendChild(searchInput);

        const groupsContainer = document.createElement('div');
        groupsContainer.id = 'emoji-groups';
        emojiPicker.appendChild(groupsContainer);

        function renderGroups(filter) {
            groupsContainer.innerHTML = '';
            const q = (filter || '').toLowerCase();
            EMOJI_GROUPS.forEach(group => {
                const emojis = q ? group.emojis.filter(e => e.includes(q)) : group.emojis;
                if (!emojis.length) return;
                const grpLabel = document.createElement('div');
                grpLabel.style.cssText = `font-size:10px;font-weight:700;letter-spacing:.7px;text-transform:uppercase;color:${light ? '#8888a0' : '#8888a0'};margin:8px 0 5px`;
                grpLabel.textContent = group.label;
                groupsContainer.appendChild(grpLabel);
                const grid = document.createElement('div');
                grid.style.cssText = 'display:flex;flex-wrap:wrap;gap:2px';
                emojis.forEach(emoji => {
                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = emoji;
                    btn.title = emoji;
                    btn.style.cssText = 'width:34px;height:34px;border:none;background:none;border-radius:6px;font-size:19px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .1s;line-height:1';
                    btn.addEventListener('mouseover', () => { btn.style.background = 'rgba(255,145,77,.15)'; });
                    btn.addEventListener('mouseout',  () => { btn.style.background = 'none'; });
                    btn.addEventListener('mousedown', e => {
                        e.preventDefault();
                        editor.chain().focus().insertContent(emoji).run();
                        closeEmojiPicker();
                    });
                    grid.appendChild(btn);
                });
                groupsContainer.appendChild(grid);
            });
        }

        renderGroups('');
        searchInput.addEventListener('input', () => renderGroups(searchInput.value.trim()));
        document.body.appendChild(emojiPicker);
        return emojiPicker;
    }

    function openEmojiPicker(anchorBtn) {
        const picker = buildEmojiPicker();
        picker.style.display = 'block';
        const pw = picker.offsetWidth || 304, ph = picker.offsetHeight || 300;
        const vw = window.innerWidth, vh = window.innerHeight;
        let top, left;
        if (anchorBtn) { const r = anchorBtn.getBoundingClientRect(); top = r.bottom + 6; left = r.left; }
        else           { top = (vh - ph) / 2; left = (vw - pw) / 2; }
        if (left + pw > vw - 8) left = vw - pw - 8;
        if (top  + ph > vh - 8) top  = vh - ph - 8;
        picker.style.left = Math.max(8, left) + 'px';
        picker.style.top  = Math.max(8, top)  + 'px';
        setTimeout(() => document.getElementById('emoji-search')?.focus(), 50);
    }
    window.openEmojiPicker = openEmojiPicker;

    function closeEmojiPicker() {
        if (emojiPicker) emojiPicker.style.display = 'none';
        editor.chain().focus().run();
    }

    document.addEventListener('mousedown', e => {
        if (emojiPicker?.style.display !== 'none' && !emojiPicker.contains(e.target)) {
            if (!e.target.closest('[data-cmd="emoji"]')) closeEmojiPicker();
        }
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape' && emojiPicker?.style.display !== 'none') closeEmojiPicker();
    });

    // ── Image popover ─────────────────────────────────────────────────────────
    const popover     = document.getElementById('image-popover');
    const tabUrl      = document.getElementById('img-tab-url');
    const tabUpload   = document.getElementById('img-tab-upload');
    const panelUrl    = document.getElementById('img-panel-url');
    const panelUpload = document.getElementById('img-panel-upload');
    const urlInput    = document.getElementById('img-url-input');
    const altInput    = document.getElementById('img-alt-input');
    const fileInput   = document.getElementById('img-file-input');
    const dropZone    = document.getElementById('img-drop-zone');
    const previewWrap = document.getElementById('img-preview-wrap');
    const previewImg  = document.getElementById('img-preview');
    let pendingBase64 = null, activeTab = 'url';

    function openImagePopover(anchorBtn) {
        if (!popover) return;
        urlInput.value = ''; altInput.value = '';
        if (fileInput) fileInput.value = '';
        pendingBase64 = null;
        if (previewWrap) previewWrap.style.display = 'none';
        switchImgTab('url');
        popover.style.display = 'block';
        const pw = popover.offsetWidth || 320, ph = popover.offsetHeight || 220;
        const vw = window.innerWidth, vh = window.innerHeight;
        let top, left;
        if (anchorBtn) { const r = anchorBtn.getBoundingClientRect(); top = r.bottom+6; left = r.left; }
        else           { top = (vh-ph)/2; left = (vw-pw)/2; }
        if (left+pw > vw-8) left = vw-pw-8;
        if (top+ph  > vh-8) top  = vh-ph-8;
        popover.style.left = Math.max(8,left)+'px';
        popover.style.top  = Math.max(8,top) +'px';
        urlInput.focus();
    }
    window.openImagePopover = openImagePopover;

    function closeImagePopover() { if (popover) popover.style.display = 'none'; editor.chain().focus().run(); }
    function switchImgTab(tab) {
        activeTab = tab;
        tabUrl?.classList.toggle('active', tab === 'url');
        tabUpload?.classList.toggle('active', tab === 'upload');
        if (panelUrl)    panelUrl.style.display    = tab === 'url'    ? '' : 'none';
        if (panelUpload) panelUpload.style.display = tab === 'upload' ? '' : 'none';
    }
    tabUrl?.addEventListener('click', () => switchImgTab('url'));
    tabUpload?.addEventListener('click', () => switchImgTab('upload'));

    function handleFile(file) {
        if (!file?.type.startsWith('image/')) return;
        const reader = new FileReader();
        reader.onload = e => {
            pendingBase64 = e.target.result;
            if (previewImg)  previewImg.src = pendingBase64;
            if (previewWrap) previewWrap.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
    fileInput?.addEventListener('change', () => handleFile(fileInput.files[0]));
    dropZone?.addEventListener('dragover',  e => { e.preventDefault(); dropZone.classList.add('dragover'); });
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
    document.addEventListener('mousedown', e => {
        if (popover?.style.display !== 'none' && !popover?.contains(e.target)) {
            if (!document.querySelector('.ttb-btn[data-cmd="image"]')?.contains(e.target)) closeImagePopover();
        }
    });
    urlInput?.addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('img-insert-btn')?.click(); });

    // ── Link popover ──────────────────────────────────────────────────────────
    const linkPopover   = document.getElementById('link-popover');
    const linkUrlInput  = document.getElementById('link-url-input');
    const linkTextInput = document.getElementById('link-text-input');

    function openLinkPopover(anchorBtn) {
        if (!linkPopover) return;
        const existingHref = editor.getAttributes('link').href || '';
        linkUrlInput.value  = existingHref;
        const { from, to } = editor.state.selection;
        linkTextInput.value = from !== to ? editor.state.doc.textBetween(from, to) : '';
        const removeBtn = document.getElementById('link-remove-btn');
        if (removeBtn) removeBtn.style.display = existingHref ? '' : 'none';
        linkPopover.style.display = 'block';
        const lw = linkPopover.offsetWidth||320, lh = linkPopover.offsetHeight||180;
        const vw = window.innerWidth, vh = window.innerHeight;
        let top, left;
        if (anchorBtn) { const r = anchorBtn.getBoundingClientRect(); top = r.bottom+6; left = r.left; }
        else           { top = (vh-lh)/2; left = (vw-lw)/2; }
        if (left+lw > vw-8) left = vw-lw-8;
        if (top+lh  > vh-8) top  = vh-lh-8;
        linkPopover.style.left = Math.max(8,left)+'px';
        linkPopover.style.top  = Math.max(8,top) +'px';
        linkUrlInput.focus();
    }
    window.openLinkPopover = openLinkPopover;

    function closeLinkPopover() { if (linkPopover) linkPopover.style.display = 'none'; editor.chain().focus().run(); }
    document.getElementById('link-insert-btn')?.addEventListener('click', () => {
        const href = linkUrlInput.value.trim();
        if (!href) { linkUrlInput.focus(); return; }
        editor.chain().focus().setLink({ href }).run();
        closeLinkPopover();
    });
    document.getElementById('link-remove-btn')?.addEventListener('click', () => { editor.chain().focus().unsetLink().run(); closeLinkPopover(); });
    document.getElementById('link-cancel-btn')?.addEventListener('click', closeLinkPopover);
    document.addEventListener('mousedown', e => {
        if (linkPopover?.style.display !== 'none' && !linkPopover?.contains(e.target)) {
            const isBtn = document.querySelector('.ttb-btn[data-cmd="link"]')?.contains(e.target)
                       || document.querySelector('.bbl-btn[data-mark="link"]')?.contains(e.target);
            if (!isBtn) closeLinkPopover();
        }
    });
    linkUrlInput?.addEventListener('keydown', e => { if (e.key === 'Enter') document.getElementById('link-insert-btn')?.click(); });

    // ── Auto-save ─────────────────────────────────────────────────────────────
    function setStatus(msg, color = 'var(--muted)') {
        const el = document.getElementById('save-status');
        if (!el) return;
        el.textContent = msg; el.style.color = color; el.style.opacity = '1';
    }
    function scheduleSave() { clearTimeout(saveTimer); setStatus('Editando…'); saveTimer = setTimeout(saveNote, 1200); }
    async function saveNote() {
        if (isSaving) return;
        isSaving = true; setStatus('Salvando…');
        try {
            const title = document.getElementById('note-title')?.value.trim() || '';
            const res   = await fetch(`/notes/${noteId}`, {
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
                // Atualiza label nos atalhos se esta nota estiver fixada
                if (window.Shortcuts) {
                    const url = '/notes/' + noteId;
                    const newTitle = document.getElementById('note-title')?.value.trim() || 'Nota sem título';
                    window.Shortcuts.updateLabel(url, newTitle);
                    // Atualiza também o data-label do botão de atalho
                    const pinBtn = document.getElementById('btn-add-shortcut');
                    if (pinBtn) pinBtn.dataset.label = newTitle;
                }
            } else { setStatus('Erro ao salvar', 'var(--danger)'); }
        } catch { setStatus('Erro de conexão', 'var(--danger)'); }
        finally  { isSaving = false; }
    }

    const titleEl = document.getElementById('note-title');
    if (titleEl) {
        const resize = () => { titleEl.style.height = 'auto'; titleEl.style.height = titleEl.scrollHeight+'px'; };
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

    document.getElementById('note-category')?.addEventListener('change', async function() {
        await fetch(`/notes/${noteId}`, {
            method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ category: this.value || null }),
        });
    });

    document.getElementById('btn-pin')?.addEventListener('click', async function() {
        const isPinned = this.textContent.includes('Fixada');
        const res = await fetch(`/notes/${noteId}`, {
            method: 'PUT', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
            body: JSON.stringify({ pinned: !isPinned }),
        });
        if (res.ok) this.textContent = isPinned ? '📌 Fixar' : '📌 Fixada';
    });

    document.getElementById('btn-delete')?.addEventListener('click', function() {
        const doDelete = async () => {
            const res = await fetch(`/notes/${noteId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': csrf } });
            if (res.ok) window.location.href = '/notes';
        };
        window.confirmDialog
            ? window.confirmDialog('Excluir nota', 'Esta ação não pode ser desfeita.', doDelete)
            : confirm('Excluir esta nota?') && doDelete();
    });

    window.addEventListener('beforeunload', () => { if (saveTimer) saveNote(); });

    // ── Tags ──────────────────────────────────────────────────────────────────
    (function initTagSystem() {
        const chipsWrap  = document.getElementById('tags-chips-wrap');
        const inputWrap  = document.getElementById('tags-input-wrap');
        const tagInput   = document.getElementById('tag-input');
        const suggestBox = document.getElementById('tag-suggestions');
        if (!chipsWrap || !tagInput) return;

        let activeTags = [...(cfg.tags || [])];
        let allTags    = (cfg.allTags || []).map(t => t.tag);
        let suggestIdx = -1;

        chipsWrap.querySelectorAll('.tag-chip-remove').forEach(btn => {
            btn.addEventListener('click', () => removeTag(btn.closest('.tag-chip').dataset.tag));
        });
        inputWrap?.addEventListener('click', () => tagInput.focus());

        tagInput.addEventListener('keydown', e => {
            const items = [...suggestBox.querySelectorAll('.tag-suggestion-item')];
            if (e.key === 'Enter' || e.key === ',') {
                e.preventDefault();
                if (suggestIdx >= 0 && items[suggestIdx]) { addTag(items[suggestIdx].dataset.tag); return; }
                const val = tagInput.value.trim().replace(/,/g,'').toLowerCase();
                if (val) addTag(val);
            } else if (e.key === 'Backspace' && !tagInput.value && activeTags.length) {
                removeTag(activeTags[activeTags.length - 1]);
            } else if (e.key === 'ArrowDown' && items.length) {
                e.preventDefault(); suggestIdx = Math.min(suggestIdx+1, items.length-1); highlightSuggest(items);
            } else if (e.key === 'ArrowUp' && items.length) {
                e.preventDefault(); suggestIdx = Math.max(suggestIdx-1, 0); highlightSuggest(items);
            } else if (e.key === 'Escape') {
                hideSuggestions();
            }
        });

        tagInput.addEventListener('input', () => { suggestIdx = -1; showSuggestions(tagInput.value.trim()); });
        tagInput.addEventListener('blur',  () => setTimeout(hideSuggestions, 150));

        function addTag(raw) {
            const tag = raw.toLowerCase().replace(/[^a-záéíóúãõâêîôûàèç0-9\-_]/g,'').trim();
            if (!tag || activeTags.includes(tag)) { tagInput.value = ''; return; }
            activeTags.push(tag);
            const chip = document.createElement('span');
            chip.className   = 'tag-chip';
            chip.dataset.tag = tag;
            chip.innerHTML   = `${tag}<button class="tag-chip-remove" type="button" aria-label="Remover">×</button>`;
            chip.querySelector('.tag-chip-remove').addEventListener('click', () => removeTag(tag));
            chipsWrap.appendChild(chip);
            tagInput.value = '';
            hideSuggestions(); saveTags();
        }

        function removeTag(tag) {
            activeTags = activeTags.filter(t => t !== tag);
            chipsWrap.querySelectorAll(`.tag-chip[data-tag="${CSS.escape(tag)}"]`).forEach(c => c.remove());
            saveTags();
        }

        function showSuggestions(query) {
            if (!query) { hideSuggestions(); return; }
            const filtered = allTags.filter(t => !activeTags.includes(t) && t.includes(query.toLowerCase())).slice(0,8);
            if (!filtered.length) { hideSuggestions(); return; }
            suggestBox.innerHTML = filtered.map(t => `<div class="tag-suggestion-item" data-tag="${t}">#${t}</div>`).join('');
            suggestBox.querySelectorAll('.tag-suggestion-item').forEach(item => {
                item.addEventListener('mousedown', e => { e.preventDefault(); addTag(item.dataset.tag); });
            });
            suggestBox.style.display = 'block';
        }

        function hideSuggestions() { suggestBox.style.display = 'none'; suggestIdx = -1; }
        function highlightSuggest(items) {
            items.forEach((it, i) => it.classList.toggle('selected', i === suggestIdx));
            items[suggestIdx]?.scrollIntoView({ block: 'nearest' });
        }
        async function saveTags() {
            await fetch(`/notes/${noteId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
                body: JSON.stringify({ tags: activeTags.length ? activeTags.join(',') : null }),
            });
        }
    })();
});
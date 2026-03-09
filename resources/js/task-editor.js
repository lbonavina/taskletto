/**
 * task-editor.js
 * Tiptap editor for the task detail page (tasks/show).
 * Exposes window.TaskEditor so the inline blade script can call it.
 */
import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Underline from '@tiptap/extension-underline';
import Placeholder from '@tiptap/extension-placeholder';

export function createTaskEditor({ element, content, placeholder }) {
    return new Editor({
        element,
        extensions: [
            StarterKit,
            Underline,
            Placeholder.configure({ placeholder: placeholder ?? 'Adicione uma descrição…' }),
        ],
        content: content ?? null,
        editorProps: {
            attributes: {
                class: 'tiptap-task-body',
            },
        },
    });
}

window.createTaskEditor = createTaskEditor;

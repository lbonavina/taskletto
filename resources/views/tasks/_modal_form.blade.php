@php $categories = $categories ?? \App\Models\Category::orderBy('name')->get(); @endphp
<div class="modal-overlay" id="modal-new-task">
    <div class="modal">
        <button class="modal-close"
            onclick="document.getElementById('modal-new-task').classList.remove('open')">×</button>
        <div class="modal-title">Nova Tarefa</div>
        <div id="modal-alert" style="display:none" class="alert"></div>

        <div class="form-group">
            <label>Título *</label>
            <input type="text" id="nt-title" placeholder="Ex: Revisar documentação">
        </div>
        <div class="form-group">
            <label>Descrição</label>
            <textarea id="nt-description" placeholder="Detalhes opcionais..."></textarea>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label>Prioridade</label>
                <div class="select-wrap">
                    <select id="nt-priority">
                        <option value="low">Baixa</option>
                        <option value="medium" selected>Média</option>
                        <option value="high">Alta</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Status</label>
                <div class="select-wrap">
                    <select id="nt-status">
                        <option value="pending" selected>Pendente</option>
                        <option value="in_progress">Em progresso</option>
                    </select>
                </div>
            </div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="form-group">
                <label>Vencimento</label>
                <input type="date" id="nt-due-date">
            </div>
            <div class="form-group">
                <label>Categoria</label>
                <div class="select-wrap">
                    <select id="nt-category">
                        <option value="" data-icon="">— Sem categoria —</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" data-color="{{ $cat->color }}" data-icon="{{ $cat->icon }}">
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:4px">
            <button class="btn btn-ghost"
                onclick="document.getElementById('modal-new-task').classList.remove('open')">Cancelar</button>
            <button class="btn btn-primary" id="btn-save-task">Criar Tarefa</button>
        </div>
    </div>
</div>
@once
    @push('scripts')
        <script>
            document.getElementById('btn-save-task').addEventListener('click', async function () {
                const btn = this;
                const alertEl = document.getElementById('modal-alert');
                const title = document.getElementById('nt-title').value.trim();
                alertEl.style.display = 'none';

                if (!title || title.length < 3) {
                    alertEl.className = 'alert alert-error';
                    alertEl.textContent = 'O título deve ter pelo menos 3 caracteres.';
                    alertEl.style.display = 'block';
                    document.getElementById('nt-title').focus();
                    return;
                }

                btn.innerHTML = '<span class="spinner"></span> Criando...';
                btn.disabled = true;

                const categoryVal = document.getElementById('nt-category').value;
                const payload = {
                    title: title,
                    description: document.getElementById('nt-description').value || null,
                    priority: document.getElementById('nt-priority').value,
                    status: document.getElementById('nt-status').value,
                    due_date: document.getElementById('nt-due-date').value || null,
                    category_id: categoryVal ? parseInt(categoryVal) : null,
                };

                try {
                    const res = await fetch('/api/v1/tasks', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        },
                        body: JSON.stringify(payload),
                    });
                    const data = await res.json();
                    if (res.ok) {
                        toast('Tarefa criada!', 'success');
                        setTimeout(() => window.location.href = '/tasks/' + data.data.id, 400);
                    } else if (res.status === 402) {
                        if (typeof window.showUpgradeModal === 'function') {
                            document.getElementById('modal-new-task').classList.remove('open');
                            window.showUpgradeModal(data.message);
                        } else {
                            alertEl.className = 'alert alert-error';
                            alertEl.textContent = data.message;
                            alertEl.style.display = 'block';
                        }
                        btn.innerHTML = 'Criar Tarefa';
                        btn.disabled = false;
                    } else {
                        const msgs = data.errors
                            ? Object.values(data.errors).flat().join(' ')
                            : (data.message || 'Erro.');
                        alertEl.className = 'alert alert-error';
                        alertEl.textContent = msgs;
                        alertEl.style.display = 'block';
                        btn.innerHTML = 'Criar Tarefa';
                        btn.disabled = false;
                    }
                } catch (e) {
                    toast('Erro de conexão.', 'error');
                    btn.innerHTML = 'Criar Tarefa';
                    btn.disabled = false;
                }
            });

            document.getElementById('modal-new-task').addEventListener('click', function (e) {
                if (e.target === this) this.classList.remove('open');
            });

            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') document.getElementById('modal-new-task').classList.remove('open');
            });
        </script>
    @endpush
@endonce
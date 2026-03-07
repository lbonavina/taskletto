@php $categories = $categories ?? \App\Models\Category::orderBy('name')->get(); @endphp
<div class="modal-overlay" id="modal-new-task">
    <div class="modal">
        <button class="modal-close" onclick="document.getElementById('modal-new-task').classList.remove('open')">×</button>
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
                            <option value="{{ $cat->name }}" data-color="{{ $cat->color }}" data-icon="{{ $cat->icon }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:4px">
            <button class="btn btn-ghost" onclick="document.getElementById('modal-new-task').classList.remove('open')">Cancelar</button>
            <button class="btn btn-primary" id="btn-save-task">Criar Tarefa</button>
        </div>
    </div>
</div>
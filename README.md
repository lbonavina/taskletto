<div align="center">

<img src="https://img.shields.io/badge/Taskletto-v1.0-ff914d?style=for-the-badge&logoColor=white" alt="Taskletto">

# Taskletto

**Gerenciador de tarefas e notas moderno, construído com Laravel + Tiptap**

[![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Vite](https://img.shields.io/badge/Vite-5-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vitejs.dev)
[![TipTap](https://img.shields.io/badge/Tiptap-Editor-1B2631?style=flat-square)](https://tiptap.dev)
[![License](https://img.shields.io/badge/License-MIT-4ade80?style=flat-square)](LICENSE)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](docker-compose.yml)

[✨ Funcionalidades](#-funcionalidades) · [📦 Instalação](#-instalação) · [🐳 Docker](#-docker) · [🖥️ Windows](#-windows-nativephp) · [📡 API](#-api-rest) · [⌨️ Atalhos](#-atalhos-de-teclado)

<br/>

![Taskletto Screenshot](https://placehold.co/900x480/0f0f11/ff914d?text=Taskletto+Screenshot&font=raleway)

</div>

---

## ✨ Funcionalidades

<table>
<tr>
<td width="50%">

### 📝 Notas
- Editor rico com **Tiptap** (headings, listas, checklists, tabelas, blocos de código)
- Inserção de imagem por URL ou upload local
- Inserção de links com popover dedicado
- Comandos **slash** (`/`) para inserir blocos rapidamente
- Auto-save com debounce de 1.2s
- Fixar notas no topo
- Categorias e cores personalizáveis

</td>
<td width="50%">

### ✅ Tarefas
- CRUD completo com prioridades e status
- Filtros por status, prioridade e categoria
- Busca em tempo real com AJAX
- Indicador visual de tarefas vencidas
- Editor de descrição com Quill.js

</td>
</tr>
<tr>
<td>

### 🎨 Interface
- Tema **escuro e claro** com transições suaves
- Design system consistente com variáveis CSS
- Animações sutis e feedback visual em tudo
- Sidebar com navegação por atalhos de teclado
- Custom selects, modais e popovers

</td>
<td>

### ⚙️ Técnico
- API REST documentada com **Swagger/L5-Swagger**
- **Docker** pronto para uso
- Soft deletes em notas e tarefas
- Busca AJAX com filtros combinados
- Suporte a **NativePHP** para app Windows

</td>
</tr>
</table>

---

## 📦 Instalação

### Pré-requisitos

| Requisito | Versão mínima |
|-----------|--------------|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ |
| SQLite / MySQL / PostgreSQL | — |

### Passo a passo

```bash
# 1. Clone o repositório
git clone https://github.com/seu-usuario/taskletto.git
cd taskletto

# 2. Instale as dependências PHP
composer install

# 3. Configure o ambiente
cp .env.example .env
php artisan key:generate

# 4. Banco de dados (SQLite por padrão)
touch database/database.sqlite
php artisan migrate

# 5. Instale as dependências JS e compile os assets
npm install
npm run dev

# 6. Suba o servidor
php artisan serve
```

Acesse: **http://localhost:8000**

---

## 🐳 Docker

```bash
docker-compose up -d
docker-compose exec app php artisan migrate
```

Acesse: **http://localhost:8080**

```env
APP_NAME=Taskletto
APP_ENV=local
DB_CONNECTION=sqlite
```

---

## 🖥️ Windows — NativePHP

```bash
composer require nativephp/electron
php artisan native:install

# Desenvolvimento
php artisan native:serve

# Build instalador .exe
php artisan native:build win
```

**`config/nativephp.php`:**
```php
'windows' => [[
    'title'     => 'Taskletto',
    'width'     => 1280,
    'height'    => 800,
    'minWidth'  => 960,
    'minHeight' => 600,
    'url'       => env('APP_URL', 'http://localhost'),
]],
```

---

## 📡 API REST

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| `GET` | `/api/notes` | Listar notas |
| `POST` | `/api/notes` | Criar nota |
| `PUT` | `/api/notes/{id}` | Atualizar nota |
| `DELETE` | `/api/notes/{id}` | Excluir nota |
| `GET` | `/api/tasks` | Listar tarefas |
| `POST` | `/api/tasks` | Criar tarefa |
| `PUT` | `/api/tasks/{id}` | Atualizar tarefa |
| `DELETE` | `/api/tasks/{id}` | Excluir tarefa |
| `GET` | `/api/categories` | Listar categorias |

Documentação Swagger: `php artisan l5-swagger:generate` → **`/api/documentation`**

---

## ⌨️ Atalhos de teclado

| Atalho | Ação |
|--------|------|
| `?` | Abrir painel de atalhos |
| `G` → `D/T/N/C/S` | Navegar para seção |
| `Ctrl + Shift + L` | Alternar tema |
| `Esc` | Fechar modal |
| `Ctrl + S` | Salvar nota |
| `Ctrl + B/I/U` | Negrito / Itálico / Sublinhado |
| `/` | Menu de comandos no editor |

---

## 📄 Licença

MIT — veja [`LICENSE`](LICENSE).

---

<div align="center">

Feito com ❤️ usando **Laravel**, **Tiptap** e muito ☕

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat-square&logo=docker&logoColor=white)](https://docker.com)

</div>
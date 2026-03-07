<div align="center">

<img src="https://img.shields.io/badge/Taskletto-v1.0-ff914d?style=for-the-badge&logoColor=white" alt="Taskletto">

# Taskletto

![Taskletto](https://github.com/lbonavina/taskletto/blob/main/public/logo-taskeletto.png#gh-dark-mode-only)
![Taskletto](https://github.com/lbonavina/taskletto/blob/main/public/logo-taskletto-light.png#gh-light-mode-only)

**Gerenciador de tarefas e notas moderno, construído com Laravel + Tiptap**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Vite](https://img.shields.io/badge/Vite-5-646CFF?style=flat-square&logo=vite&logoColor=white)](https://vitejs.dev)
[![TipTap](https://img.shields.io/badge/Tiptap-Editor-1B2631?style=flat-square)](https://tiptap.dev)
[![NativePHP](https://img.shields.io/badge/NativePHP-Desktop-ff914d?style=flat-square)](https://nativephp.com)
[![License](https://img.shields.io/badge/License-MIT-4ade80?style=flat-square)](LICENSE)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](docker-compose.yml)

[✨ Funcionalidades](#-funcionalidades) · [📦 Instalação](#-instalação) · [🐳 Docker](#-docker) · [🖥️ App Windows](#-app-windows--nativephp) · [📡 API](#-api-rest) · [⌨️ Atalhos](#-atalhos-de-teclado) · [🔮 Roadmap](#-roadmap)

<br/>

![Taskletto Screenshot](https://github.com/lbonavina/taskletto/blob/main/public/screenshot.png)

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
- Modal de atalhos com busca e abas por categoria
- Custom selects, modais e popovers

</td>
<td>

### ⚙️ Técnico
- API REST documentada com **Swagger/L5-Swagger**
- **Docker** pronto para uso (Nginx + MySQL + Vite)
- Soft deletes em notas e tarefas
- Busca AJAX com filtros combinados
- **App desktop Windows** via NativePHP + Electron

</td>
</tr>
</table>

---

## 📦 Instalação

### Pré-requisitos

| Requisito | Versão mínima |
|-----------|--------------|
| PHP | 8.4+ |
| Composer | 2.x |
| Node.js | 22+ |
| SQLite / MySQL / PostgreSQL | — |

### Passo a passo

```bash
# 1. Clone o repositório
git clone https://github.com/lbonavina/taskletto.git
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
# Subir o ambiente completo
docker compose up -d

# Rodar migrações
docker compose exec app php artisan migrate

# Limpar cache de views
docker compose exec app php artisan view:clear
```

Acesse: **http://localhost:8000** · phpMyAdmin: **http://localhost:8080** (perfil tools)

```bash
# Subir com Vite hot-reload
docker compose --profile dev up

# Subir com phpMyAdmin
docker compose --profile tools up
```

Comandos úteis via `make`:

```bash
make up          # subir em produção
make up-dev      # subir com Vite
make shell       # bash no container
make migrate     # rodar migrations
make cache-clear # limpar todos os caches
```

---

## 🖥️ App Windows — NativePHP

O Taskletto roda como **aplicativo desktop nativo no Windows** usando [NativePHP](https://nativephp.com) + Electron. O usuário final recebe um instalador `.exe` — sem precisar de PHP, Node ou Docker instalados.

### Pré-requisitos para desenvolvimento

- PHP 8.4+ e Node 22+ instalados localmente (recomenda-se [Laravel Herd](https://herd.laravel.com/windows))
- O setup NativePHP deve rodar **fora do Docker**, direto na máquina

### Instalação

```bash
composer require nativephp/electron
php artisan native:install
```

### Desenvolvimento

```bash
php artisan native:serve
```

Abre o Taskletto em uma janela Electron com hot-reload.

### Gerar instalador `.exe`

```bash
npm install
npm run build
php artisan native:build win
```

O instalador é gerado na pasta `dist/`.

### Configuração (`config/nativephp.php`)

```php
'version'     => '1.0.0',
'app_id'      => 'com.lbonavina.taskletto',
'author'      => 'Seu Nome',
'description' => 'Gerenciador de tarefas e notas',
'website'     => 'https://github.com/lbonavina/taskletto',

'prebuild' => [
    'npm run build',
],
```

> **⚠️ Aviso:** O Taskletto desktop ainda não possui sincronização em nuvem. Os dados ficam armazenados localmente via SQLite. Sync via Google Drive, GitHub ou serviço próprio está planejado para versões futuras — veja o [Roadmap](#-roadmap).

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

Documentação Swagger interativa:

```bash
php artisan l5-swagger:generate
```

Acesse: **`/api/documentation`**

---

## ⌨️ Atalhos de teclado

Abra o painel completo de atalhos com **`?`** dentro do app. O painel tem busca em tempo real e abas por categoria.

| Atalho | Ação |
|--------|------|
| `?` | Abrir painel de atalhos |
| `G` → `D` / `T` / `N` / `C` / `S` | Navegar para seção |
| `Ctrl + Shift + L` | Alternar tema claro/escuro |
| `Esc` | Fechar modal / cancelar |
| `Ctrl + S` | Salvar nota |
| `Ctrl + B` / `I` / `U` | Negrito / Itálico / Sublinhado |
| `/` | Menu de comandos no editor |
| `Ctrl + K` | Inserir link |

---

## 🔮 Roadmap

Funcionalidades planejadas para versões futuras:

- [ ] **Sincronização em nuvem** — sync automático via Google Drive, GitHub ou serviço próprio (principal prioridade)
- [ ] **Autenticação** — suporte a múltiplos usuários com login
- [ ] **Tags em notas** — sistema de tags além de categorias
- [ ] **Exportar notas** — PDF, Markdown, HTML
- [ ] **App macOS** — build para macOS via NativePHP
- [ ] **Notificações desktop** — lembretes de tarefas vencidas
- [ ] **Modo offline** — PWA com service worker para uso no navegador sem conexão
- [ ] **Temas customizáveis** — paleta de cores configurável pelo usuário

> Contribuições são bem-vindas! Veja como contribuir abaixo.

---

## 🤝 Contribuindo

```bash
# Fork → clone → nova branch
git checkout -b feature/minha-feature

git commit -m "feat: adiciona X funcionalidade"

git push origin feature/minha-feature
# Abra um Pull Request
```

---

## 📄 Licença

MIT — veja [`LICENSE`](LICENSE).

---

<div align="center">

Feito com ❤️ usando **Laravel**, **Tiptap**, **NativePHP** e muito ☕

[![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Electron](https://img.shields.io/badge/Electron-47848F?style=flat-square&logo=electron&logoColor=white)](https://electronjs.org)
[![Docker](https://img.shields.io/badge/Docker-2496ED?style=flat-square&logo=docker&logoColor=white)](https://docker.com)
[![SQLite](https://img.shields.io/badge/SQLite-003B57?style=flat-square&logo=sqlite&logoColor=white)](https://sqlite.org)

</div>

<div align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/lbonavina/taskletto/main/public/logo-taskletto-light.png">
    <img src="https://raw.githubusercontent.com/lbonavina/taskletto/main/public/logo-taskletto.png" alt="Taskletto" width="300">
  </picture>

  <p><strong>Modern task and notes manager for developers</strong></p>
</div>

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![NativePHP](https://img.shields.io/badge/NativePHP-v2-FF750F?style=flat-square)](https://nativephp.com)
[![License](https://img.shields.io/badge/License-MIT-4ade80?style=flat-square)](LICENSE)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](docker-compose.yml)
[![Website](https://img.shields.io/badge/Website-lbonavina.github.io/taskletto-FF750F?style=flat-square&logo=github&logoColor=white)](https://lbonavina.github.io/taskletto)

<br/>

### [🌐 lucasbonavina.win/taskletto](https://lucasbonavina.win/taskletto)
### [⬇️ Download for Windows (.exe)](https://github.com/lbonavina/taskletto/releases/latest)

</div>

---

## ✨ Features

<table>
<tr>
<td width="50%">

**📝 Notes**
- Rich editor powered by Tiptap — headings, lists, checklists, code blocks
- Slash `/` commands, auto-save and pin notes
- Callout blocks: Info, Success, Warning, Danger, Tip, Note
- Customizable colors and categories

**✅ Tasks**
- Subtasks with nested progress tracking
- Priorities, statuses and categories
- Recurrence: daily, weekly, monthly — auto-spawns on completion
- Time tracking: start/stop timer, estimated vs. tracked time, progress bar
- Real-time search and filters
- Overdue task indicator

</td>
<td width="50%">

**📊 Dashboard**
- Redesigned KPI cards with activity overview
- Today's tasks section and streak tracker
- Interactive activity chart with tooltips
- Weather widget with local forecast

**🔗 GitHub Sync**
- Connect your GitHub account
- Sync tasks and notes across devices
- Keep your workflow tied to your repositories

**🎨 Interface**
- Polished dark and light themes
- Keyboard shortcuts with search panel
- Native desktop app for Windows via NativePHP v2

**⚙️ Technical**
- REST API with Swagger documentation
- Export/import JSON for backup and migration
- Docker ready · SQLite by default

</td>
</tr>
</table>

---

## 🆕 What's new in v2.0.0

### 🔗 GitHub Sync
Tasks and notes can now be synced via GitHub, keeping your data across devices and tying your workflow to your repositories. Connect your account from the settings panel and choose what to sync.

### 🌿 Subtasks
Break down complex tasks into smaller, trackable steps. Each task now supports a nested subtask list with its own completion state, and the parent task shows an aggregated progress indicator.

### 📊 Redesigned Dashboard
The dashboard was rebuilt from scratch with a cleaner layout, improved KPI cards, a reworked activity chart with reliable tooltips, and a weather widget that caches data locally to avoid repeated requests.

### ✏️ Improved Notes Editor
The Tiptap editor received several quality-of-life improvements: better slash command discovery, improved table handling, smoother callout block interactions, and a more consistent toolbar across light and dark themes.

### 🖥️ NativePHP v2
Upgraded from `nativephp/electron` v1 to `nativephp/desktop` v2, bringing Electron 38, improved security defaults, faster startup via build-time caching (`config:cache`, `route:cache`, `view:cache`), and a polished NSIS installer with welcome screen, license acceptance, and directory selection.

---

## 🖥️ Desktop app (NativePHP)

> **Requirements:** PHP 8.2+, Composer, Node.js 18+, npm

```bash
# 1. Clone the repository
git clone https://github.com/lbonavina/taskletto.git
cd taskletto

# 2. Install PHP dependencies
composer install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Create the database and run migrations
touch database/database.sqlite
php artisan migrate

# 5. Install JS dependencies
npm install

# 6. Install NativePHP (downloads Electron)
php artisan native:install

# 7. Launch the desktop app
composer run native:dev
```

The app will open as a native desktop window. ✅

---

## 🚀 Run locally (web)

```bash
git clone https://github.com/lbonavina/taskletto.git
cd taskletto
composer install && npm install
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
php artisan migrate
composer run dev
```

Visit **http://localhost:8000**

---

## 🐳 Docker

```bash
docker compose up -d
docker compose exec app php artisan migrate
```

Visit **http://localhost:8000**

---

## 🔮 Roadmap

- [x] GitHub sync
- [x] Subtasks with progress tracking
- [x] Redesigned dashboard
- [x] Improved notes editor
- [x] NativePHP v2 with polished installer
- [x] Export / import JSON
- [x] Task recurrence (daily, weekly, monthly)
- [x] Time tracking with progress bar
- [x] Callout blocks in the notes editor
- [ ] macOS app
- [ ] Customizable themes
- [ ] Mobile companion app

---

## 📄 License

MIT — see [`LICENSE`](LICENSE).

---

<div align="center">

Made with ❤️ using **Laravel**, **Tiptap** and **NativePHP**

[🌐 Website](https://taskletto.lucasbonavina.win) · [⬇️ Download](https://github.com/lbonavina/taskletto/releases/latest) · [☕ Ko-fi](https://ko-fi.com/lbonavina)

</div>

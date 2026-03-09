<div align="center">
  <picture>
    <source media="(prefers-color-scheme: dark)" srcset="https://raw.githubusercontent.com/lbonavina/taskletto/main/public/logo-taskletto-light.png">
    <img src="https://raw.githubusercontent.com/lbonavina/taskletto/main/public/logo-taskletto.png" alt="Taskletto" width="300">
  </picture>

  <p><strong>Modern task and notes manager</strong></p>
</div>

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![NativePHP](https://img.shields.io/badge/NativePHP-Desktop-ff914d?style=flat-square)](https://nativephp.com)
[![License](https://img.shields.io/badge/License-MIT-4ade80?style=flat-square)](LICENSE)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](docker-compose.yml)
[![Website](https://img.shields.io/badge/Website-lbonavina.github.io/taskletto-ff914d?style=flat-square&logo=github&logoColor=white)](https://lbonavina.github.io/taskletto)

<br/>

### [🌐 lbonavina.github.io/taskletto](https://lbonavina.github.io/taskletto)
### [⬇️ Download for Windows (.exe)](https://github.com/lbonavina/taskletto/releases/latest)

</div>

---

## ✨ Features

<table>
<tr>
<td width="50%">

**📝 Notes**
- Rich editor with Tiptap — headings, lists, checklists, code
- Slash `/` commands, auto-save, pin notes
- Customizable colors and categories
- Callout blocks (Info, Success, Warning, Danger, Tip, Note)

**✅ Tasks**
- Priorities, statuses and categories
- Recurrence: daily, weekly, monthly — auto-spawns on completion
- Time tracking: start/stop timer, estimated vs. tracked time, progress bar
- Real-time search and filters
- Overdue task indicator

</td>
<td width="50%">

**📊 Dashboard**
- KPI cards with daily overview
- Today's tasks section and streak tracker
- Interactive chart with tooltips

**🎨 Interface**
- Dark and light theme
- Keyboard shortcuts with search panel
- Native desktop app for Windows

**⚙️ Technical**
- REST API with Swagger documentation
- Export/import JSON for backup and device migration
- Docker ready
- SQLite by default, MySQL support

</td>
</tr>
</table>

---

## 🆕 What's new in v1.3.0

### 📣 Callout Blocks
Notion-style highlight boxes directly in the notes editor. Available via the slash `/` menu or the new toolbar button, with **6 types**: Info, Success, Warning, Danger, Tip, and Note. Each block has its own color and icon, and the type can be changed by clicking the block's icon.

---

## 🆕 What's new in v1.2.0

### ✅ Task Recurrence
Tasks can now repeat automatically — choose **daily**, **weekly**, or **monthly**. When you complete a recurring task, the next occurrence is spawned automatically.

### ⏱️ Time Tracking
- Start/stop timer directly from the task sidebar
- Set `estimated_minutes` displayed as hours + minutes
- Visual progress bar comparing tracked vs. estimated time

### 📊 Redesigned Dashboard
- KPI cards with background highlights
- **Today** section showing what's due now
- Streak counter to keep you motivated
- Tooltip support on the activity chart

### 💾 JSON Export / Import
Back up all your tasks and notes to a JSON file, or migrate data between devices with a single import.

### 💬 Improved Comments
- Pagination for long comment threads
- Inline editing with Markdown preview
- Comment count displayed per task
- Task sidebar reorganized into stacked cards: Info, Time, and Comments

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

- [ ] Cloud sync (Google Drive / GitHub)
- [x] Export / import JSON for backup and migration
- [x] Export notes as PDF and Markdown
- [x] Task recurrence (daily, weekly, monthly)
- [x] Time tracking with progress bar
- [x] Desktop notifications for overdue tasks
- [x] Callout blocks in the notes editor
- [ ] macOS app
- [ ] Customizable themes

> **ℹ️** Data is currently stored locally. Cloud sync is planned for future releases.

---

## 📄 License

MIT — see [`LICENSE`](LICENSE).

---

<div align="center">

Made with ❤️ using **Laravel**, **Tiptap** and **NativePHP**

[🌐 Website](https://lbonavina.github.io/taskletto) · [⬇️ Download](https://github.com/lbonavina/taskletto/releases/latest) · [☕ Ko-fi](https://ko-fi.com/lbonavina)

</div>

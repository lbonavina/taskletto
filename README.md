<div align="center">

![Taskletto](https://github.com/lbonavina/taskletto/blob/main/public/logo-taskletto-light.png#gh-dark-mode-only)
![Taskletto](https://github.com/lbonavina/taskletto/blob/main/public/logo-taskletto.png#gh-light-mode-only)

**Modern task and notes manager**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![NativePHP](https://img.shields.io/badge/NativePHP-Desktop-ff914d?style=flat-square)](https://nativephp.com)
[![License](https://img.shields.io/badge/License-MIT-4ade80?style=flat-square)](LICENSE)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](docker-compose.yml)

<br/>

![Taskletto Screenshot](https://github.com/lbonavina/taskletto/blob/main/public/screenshot.png)

<br/>

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

**✅ Tasks**
- Priorities, statuses and categories
- Real-time search and filters
- Overdue task indicator

</td>
<td width="50%">

**🎨 Interface**
- Dark and light theme
- Keyboard shortcuts with search panel
- Native desktop app for Windows

**⚙️ Technical**
- REST API with Swagger documentation
- Docker ready
- SQLite by default, MySQL support

</td>
</tr>
</table>

---

## 🚀 Run locally
```bash
git clone https://github.com/lbonavina/taskletto.git
cd taskletto
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate
npm run dev & php artisan serve
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
- [ ] Export notes as PDF and Markdown
- [ ] Desktop notifications for overdue tasks
- [ ] macOS app
- [ ] Customizable themes

> **ℹ️** Data is currently stored locally. Cloud sync is planned for future releases.

---

## 📄 License

MIT — see [`LICENSE`](LICENSE).

---

<div align="center">

Made with ❤️ using **Laravel**, **Tiptap** and **NativePHP**

</div>
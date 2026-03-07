<div align="center">

![Taskletto](https://github.com/lbonavina/taskletto/blob/main/public/logo-taskletto.png#gh-dark-mode-only)
![Taskletto](https://github.com/lbonavina/taskletto/blob/main/public/logo-taskletto-light.png#gh-light-mode-only)

**Gerenciador de tarefas e notas moderno**

[![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![NativePHP](https://img.shields.io/badge/NativePHP-Desktop-ff914d?style=flat-square)](https://nativephp.com)
[![License](https://img.shields.io/badge/License-MIT-4ade80?style=flat-square)](LICENSE)
[![Docker](https://img.shields.io/badge/Docker-Ready-2496ED?style=flat-square&logo=docker&logoColor=white)](docker-compose.yml)

<br/>

![Taskletto Screenshot](https://github.com/lbonavina/taskletto/blob/main/public/screenshot.png)

<br/>

### [⬇️ Download para Windows (.exe)](https://github.com/lbonavina/taskletto/releases/latest)

</div>

---

## ✨ Funcionalidades

<table>
<tr>
<td width="50%">

**📝 Notas**
- Editor rico com Tiptap — headings, listas, checklists, código
- Comandos slash `/`, auto-save, fixar notas
- Cores e categorias personalizáveis

**✅ Tarefas**
- Prioridades, status e categorias
- Busca e filtros em tempo real
- Indicador de tarefas vencidas

</td>
<td width="50%">

**🎨 Interface**
- Tema escuro e claro
- Atalhos de teclado com painel de busca
- App desktop nativo para Windows

**⚙️ Técnico**
- API REST com documentação Swagger
- Docker pronto para uso
- SQLite por padrão, suporte a MySQL

</td>
</tr>
</table>

---

## 🚀 Rodar localmente

```bash
git clone https://github.com/lbonavina/taskletto.git
cd taskletto
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate
npm run dev & php artisan serve
```

Acesse **http://localhost:8000**

---

## 🐳 Docker

```bash
docker compose up -d
docker compose exec app php artisan migrate
```

Acesse **http://localhost:8000**

---

## 🔮 Roadmap

- [ ] Sincronização em nuvem (Google Drive / GitHub)
- [ ] Exportar notas em PDF e Markdown
- [ ] Notificações desktop para tarefas vencidas
- [ ] App para macOS
- [ ] Temas customizáveis

> **ℹ️** No momento os dados são armazenados localmente. Sync em nuvem está planejado para versões futuras.

---

## 📄 Licença

MIT — veja [`LICENSE`](LICENSE).

---

<div align="center">

Feito com ❤️ usando **Laravel**, **Tiptap** e **NativePHP**

</div>

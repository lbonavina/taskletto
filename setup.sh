#!/usr/bin/env bash
# =============================================================================
# Taskletto — Setup Docker (Fase 2: API + Web)
# Uso: ./setup.sh  (rode da raiz do projeto)
# =============================================================================
set -e

GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
CYAN='\033[0;36m'
NC='\033[0m'

log()     { echo -e "${GREEN}[✔]${NC} $1"; }
warn()    { echo -e "${YELLOW}[!]${NC} $1"; }
error()   { echo -e "${RED}[✘]${NC} $1"; exit 1; }
section() { echo -e "\n${CYAN}▶ $1${NC}"; }

cd "$(dirname "${BASH_SOURCE[0]}")"

# ── Verificações ──────────────────────────────────────────────────────────────
section "Verificando dependências"

command -v docker &>/dev/null  || error "Docker não encontrado. Instale em https://docs.docker.com/get-docker/"
[ -f "composer.json" ]         || error "composer.json não encontrado. Rode da raiz do projeto."
[ -f "docker-compose.yml" ]    || error "docker-compose.yml não encontrado."
[ -f "Dockerfile" ]            || error "Dockerfile não encontrado."

log "Docker: $(docker --version)"

DC="docker compose"
$DC version &>/dev/null || DC="docker-compose"
log "Docker Compose detectado"

# ── .env ──────────────────────────────────────────────────────────────────────
section "Configurando .env"

if [ ! -f ".env" ]; then
    cp .env.docker .env
    log ".env criado a partir de .env.docker"
else
    warn ".env já existe — mantendo o atual"
fi

# ── Build e sobe containers ───────────────────────────────────────────────────
section "Fazendo build e subindo containers"

$DC up -d --build
log "Containers iniciados"

# ── Aguarda container app ficar saudável ──────────────────────────────────────
section "Aguardando container app iniciar"

echo -n "   Aguardando"
for i in {1..20}; do
    STATUS=$($DC ps app --format "{{.Status}}" 2>/dev/null || echo "")
    if echo "$STATUS" | grep -qi "running\|up"; then
        echo ""
        log "Container app está rodando"
        break
    fi
    echo -n "."
    sleep 2
    if [ "$i" -eq 20 ]; then
        echo ""
        warn "Timeout aguardando app. Tentando mesmo assim..."
    fi
done

sleep 3

# ── Composer install ──────────────────────────────────────────────────────────
section "Instalando dependências PHP (Composer)"

$DC exec -T app composer install --no-interaction --optimize-autoloader
log "Dependências PHP instaladas"

# ── NPM install + build ───────────────────────────────────────────────────────
section "Instalando dependências JS e compilando assets (Vite + Tailwind)"

$DC exec -T app npm install
log "node_modules instalados"

$DC exec -T app npm run build
log "Assets compilados (public/build/)"

# ── Aguarda MySQL ─────────────────────────────────────────────────────────────
section "Aguardando MySQL iniciar"

echo -n "   Aguardando"
for i in {1..30}; do
    if $DC exec -T db mysqladmin ping -h "localhost" -u taskletto_user -ptaskletto_pass --silent 2>/dev/null; then
        echo ""
        log "MySQL está pronto"
        break
    fi
    echo -n "."
    sleep 2
    if [ "$i" -eq 30 ]; then
        echo ""
        error "MySQL demorou demais. Verifique com: $DC logs db"
    fi
done

# ── Laravel ───────────────────────────────────────────────────────────────────
section "Configurando Laravel"

if grep -q "APP_KEY=$" .env 2>/dev/null || grep -q 'APP_KEY=""' .env 2>/dev/null; then
    $DC exec -T app php artisan key:generate
    log "APP_KEY gerada"
fi

$DC exec -T app php artisan migrate --force
log "Migrations executadas"

read -rp "   Popular banco com dados de exemplo? [s/N] " seed
if [[ "$seed" =~ ^[Ss]$ ]]; then
    $DC exec -T app php artisan db:seed --force
    log "Seed executado"
fi

$DC exec -T app php artisan l5-swagger:generate
log "Swagger gerado"

$DC exec -T app php artisan config:cache
$DC exec -T app php artisan route:cache
$DC exec -T app php artisan view:cache
log "Cache de config/routes/views gerado"

# ── Pronto ────────────────────────────────────────────────────────────────────
echo ""
echo -e "${GREEN}╔══════════════════════════════════════════════════╗${NC}"
echo -e "${GREEN}║          Taskletto — Pronto! 🚀                  ║${NC}"
echo -e "${GREEN}╠══════════════════════════════════════════════════╣${NC}"
echo -e "${GREEN}║${NC}  🌐 Web UI:      http://localhost:8000           "
echo -e "${GREEN}║${NC}  📡 API:         http://localhost:8000/api/v1    "
echo -e "${GREEN}║${NC}  📖 Swagger:     http://localhost:8000/api/documentation"
echo -e "${GREEN}║${NC}  🗄️  phpMyAdmin:  http://localhost:8080           "
echo -e "${GREEN}╠══════════════════════════════════════════════════╣${NC}"
echo -e "${GREEN}║${NC}  Parar:    $DC down                           "
echo -e "${GREEN}║${NC}  Logs:     $DC logs -f app                    "
echo -e "${GREEN}║${NC}  Artisan:  $DC exec app php artisan <cmd>     "
echo -e "${GREEN}║${NC}  Testes:   $DC exec app php artisan test       "
echo -e "${GREEN}╚══════════════════════════════════════════════════╝${NC}"

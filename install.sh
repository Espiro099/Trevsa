#!/bin/bash

# Script de instalación para el proyecto Trevsa (Laravel + MongoDB)
# Ejecutar desde la raíz del proyecto

set -e

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$PROJECT_DIR"

echo "🚀 Instalando proyecto Trevsa..."
echo "📁 Directorio: $PROJECT_DIR"
echo ""

# Colores para output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 1. Verificar herramientas necesarias
echo "🔍 Verificando herramientas necesarias..."

check_command() {
    if command -v "$1" &> /dev/null; then
        echo -e "${GREEN}✓${NC} $1 está instalado: $(command -v $1)"
        if [ "$1" = "php" ]; then
            php --version | head -1
        elif [ "$1" = "composer" ]; then
            composer --version | head -1
        elif [ "$1" = "node" ]; then
            node --version
        elif [ "$1" = "npm" ]; then
            npm --version
        fi
        return 0
    else
        echo -e "${RED}✗${NC} $1 NO está instalado"
        return 1
    fi
}

ALL_OK=true
check_command php || ALL_OK=false
check_command composer || ALL_OK=false
check_command node || ALL_OK=false
check_command npm || ALL_OK=false

if [ "$ALL_OK" = false ]; then
    echo ""
    echo -e "${YELLOW}⚠️  Algunas herramientas faltan. En macOS puedes instalarlas con:${NC}"
    echo "   - PHP: macOS incluye PHP, o usa Homebrew: brew install php"
    echo "   - Composer: brew install composer"
    echo "   - Node.js y npm: brew install node"
    echo ""
    read -p "¿Deseas continuar de todos modos? (y/n) " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        exit 1
    fi
fi

echo ""

# 2. Instalar dependencias de PHP (Composer)
echo "📦 Instalando dependencias de PHP con Composer..."
if [ -f "composer.json" ]; then
    if [ -d "vendor" ] && [ -f "vendor/autoload.php" ]; then
        echo -e "${GREEN}✓${NC} Las dependencias de Composer ya están instaladas"
        echo "   Ejecutando composer install para asegurar que estén actualizadas..."
    fi
    composer install --no-interaction --optimize-autoloader
    echo -e "${GREEN}✓${NC} Dependencias de PHP instaladas correctamente"
else
    echo -e "${RED}✗${NC} composer.json no encontrado"
    exit 1
fi

echo ""

# 3. Instalar dependencias de Node.js (npm)
echo "📦 Instalando dependencias de Node.js con npm..."
if [ -f "package.json" ]; then
    if [ -d "node_modules" ]; then
        echo -e "${GREEN}✓${NC} node_modules ya existe"
        echo "   Ejecutando npm install para asegurar que estén actualizadas..."
    fi
    npm install
    echo -e "${GREEN}✓${NC} Dependencias de Node.js instaladas correctamente"
else
    echo -e "${RED}✗${NC} package.json no encontrado"
    exit 1
fi

echo ""

# 4. Configurar archivo .env
echo "⚙️  Configurando archivo .env..."
if [ -f ".env" ]; then
    echo -e "${GREEN}✓${NC} Archivo .env ya existe"
else
    if [ -f ".env.example" ]; then
        echo "   Copiando .env.example a .env..."
        cp .env.example .env
        echo -e "${GREEN}✓${NC} Archivo .env creado desde .env.example"
    else
        echo -e "${YELLOW}⚠️  .env.example no encontrado, creando archivo .env básico...${NC}"
        cat > .env << 'EOF'
APP_NAME=Trevsa
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_TIMEZONE=UTC
APP_URL=http://localhost

APP_LOCALE=es
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=es_ES

APP_MAINTENANCE_DRIVER=file
APP_MAINTENANCE_STORE=database

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mongodb
DB_HOST=localhost
DB_PORT=27017
DB_DATABASE=trevsa_db
DB_USERNAME=
DB_PASSWORD=
MONGO_DSN=mongodb://localhost:27017/trevsa_db

CACHE_STORE=database
CACHE_PREFIX=

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

QUEUE_CONNECTION=database

FILESYSTEM_DISK=local

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_ENDPOINT=
AWS_USE_PATH_STYLE_ENDPOINT=false

VITE_APP_NAME="${APP_NAME}"
EOF
        echo -e "${GREEN}✓${NC} Archivo .env básico creado"
    fi
fi

echo ""

# 5. Generar clave de aplicación
echo "🔑 Generando clave de aplicación..."
if grep -q "APP_KEY=$" .env 2>/dev/null || ! grep -q "APP_KEY=" .env 2>/dev/null; then
    php artisan key:generate --ansi
    echo -e "${GREEN}✓${NC} Clave de aplicación generada"
else
    echo -e "${GREEN}✓${NC} La clave de aplicación ya está configurada"
fi

echo ""

# 6. Optimizar autoload
echo "🔧 Optimizando autoload de Composer..."
composer dump-autoload --optimize --classmap-authoritative
echo -e "${GREEN}✓${NC} Autoload optimizado"

echo ""

# 7. Compilar assets (opcional)
echo "📦 Compilando assets de frontend..."
if [ -f "vite.config.js" ]; then
    echo "   Esto puede tardar un momento..."
    npm run build
    echo -e "${GREEN}✓${NC} Assets compilados"
else
    echo -e "${YELLOW}⚠️  vite.config.js no encontrado, saltando compilación${NC}"
fi

echo ""
echo -e "${GREEN}✅ Instalación completada exitosamente!${NC}"
echo ""
echo "📝 Próximos pasos:"
echo "   1. Edita el archivo .env con tus credenciales de MongoDB"
echo "   2. Configura MONGO_DSN en .env si usas MongoDB Atlas o conexión remota"
echo "   3. Ejecuta: php artisan migrate (para ejecutar migraciones)"
echo "   4. Ejecuta: php artisan serve (para iniciar el servidor de desarrollo)"
echo "   5. Ejecuta: npm run dev (en otra terminal para hot-reload de assets)"
echo ""
echo "💡 O usa el script 'dev' de Composer:"
echo "   composer run dev"
echo ""


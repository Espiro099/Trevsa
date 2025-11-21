#!/bin/bash
# Script para aumentar límites de PHP para uploads

PHP_INI_DIR="/usr/local/etc/php/8.2"
CONF_DIR="$PHP_INI_DIR/conf.d"
CONF_FILE="$CONF_DIR/99-upload-limits.ini"

echo "📝 Configurando límites de PHP para uploads grandes..."
echo ""

# Crear el archivo de configuración
cat > /tmp/upload_limits.ini << 'EOF'
; Configuración para aumentar límites de carga de archivos
; Archivo creado para el proyecto Trevsa
upload_max_filesize = 100M
post_max_size = 200M
max_file_uploads = 50
memory_limit = 256M
max_execution_time = 300
max_input_time = 300
EOF

echo "✅ Archivo de configuración creado en /tmp/upload_limits.ini"
echo ""
echo "Para aplicar los cambios, ejecuta:"
echo "sudo cp /tmp/upload_limits.ini $CONF_FILE"
echo ""
echo "O edita manualmente el archivo php.ini:"
echo "sudo nano $PHP_INI_DIR/php.ini"
echo ""
echo "Busca y cambia estas líneas:"
echo "  upload_max_filesize = 2M   ->  upload_max_filesize = 100M"
echo "  post_max_size = 8M         ->  post_max_size = 200M"
echo ""
echo "Después de hacer los cambios, reinicia el servidor PHP:"
echo "  - Si usas 'php artisan serve', detén y reinicia el comando"
echo "  - Si usas otro servidor web, reinícialo"

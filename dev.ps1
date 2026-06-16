# Script para iniciar el entorno de desarrollo
# Ejecuta PHP server, Vite y Queue en paralelo

Write-Host "Iniciando entorno de desarrollo..." -ForegroundColor Green

# Ejecutar npm run dev y php artisan serve en paralelo
npx concurrently -c "#93c5fd,#c4b5fd,#fdba74" "php artisan serve --host=0.0.0.0 --port=8000" "php artisan queue:listen --tries=1" "npm run dev" --names=server,queue,vite --kill-others

$env:LESSCHARSET = "utf-8"
php artisan migrate --path=database/migrations/2025_10_09_000001_create_factro_configurations_table.php --force
Write-Host "Migración completada"


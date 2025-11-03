# Script de verificación rápida del webhook de WhatsApp
# Uso: .\check_whatsapp_status.ps1

Write-Host "`n========================================================" -ForegroundColor Cyan
Write-Host "   VERIFICACION DEL WEBHOOK DE WHATSAPP" -ForegroundColor Cyan
Write-Host "========================================================`n" -ForegroundColor Cyan

# 1. Verificar URL en .env
Write-Host "1. Configuracion en .env:" -ForegroundColor Yellow
$envContent = Select-String -Pattern "N8N_WHATSAPP_WEBHOOK_URL" .env -ErrorAction SilentlyContinue
if ($envContent) {
    $url = $envContent.Line -replace "N8N_WHATSAPP_WEBHOOK_URL=", ""
    if ($url -like "*dokploy*") {
        Write-Host "   [OK] URL correcta: $url" -ForegroundColor Green
    } else {
        Write-Host "   [ERROR] URL incorrecta: $url" -ForegroundColor Red
        Write-Host "   Deberia contener 'dokploy'" -ForegroundColor Red
    }
} else {
    Write-Host "   [AVISO] No encontrada en .env (usara valor por defecto)" -ForegroundColor Yellow
}

# 2. Verificar logs recientes
Write-Host "`n2. Logs de WhatsApp:" -ForegroundColor Yellow
$logFiles = Get-ChildItem storage/logs/whatsapp*.log -ErrorAction SilentlyContinue | Sort-Object LastWriteTime -Descending | Select-Object -First 1
if ($logFiles) {
    Write-Host "   Log mas reciente: $($logFiles.Name)" -ForegroundColor Green
    Write-Host "   Ultima modificacion: $($logFiles.LastWriteTime)" -ForegroundColor Cyan
    Write-Host "   Tamanio: $([math]::Round($logFiles.Length / 1KB, 2)) KB" -ForegroundColor Cyan
    
    # Mostrar últimas 5 líneas
    Write-Host "`n   Ultimas 5 lineas del log:" -ForegroundColor Yellow
    Get-Content $logFiles.FullName -Tail 5 | ForEach-Object {
        if ($_ -match "ERROR") {
            Write-Host "      $_" -ForegroundColor Red
        } elseif ($_ -match "WARNING") {
            Write-Host "      $_" -ForegroundColor Yellow
        } elseif ($_ -match "INFO") {
            Write-Host "      $_" -ForegroundColor Gray
        } else {
            Write-Host "      $_"
        }
    }
} else {
    Write-Host "   [AVISO] No se encontraron archivos de log" -ForegroundColor Yellow
}

# 3. Verificar configuración cacheada
Write-Host "`n3. Cache de configuracion:" -ForegroundColor Yellow
if (Test-Path "bootstrap/cache/config.php") {
    Write-Host "   [AVISO] Configuracion en cache" -ForegroundColor Yellow
    Write-Host "   Ejecuta: php artisan config:clear" -ForegroundColor Cyan
} else {
    Write-Host "   [OK] No hay cache (bueno para desarrollo)" -ForegroundColor Green
}

# 4. Comandos disponibles
Write-Host "`n4. Comandos disponibles:" -ForegroundColor Yellow
Write-Host "   - Probar webhook:" -ForegroundColor Cyan
Write-Host "     php artisan whatsapp:test <numero>" -ForegroundColor Gray
Write-Host "`n   - Verificar configuracion:" -ForegroundColor Cyan
Write-Host "     php artisan n8n:check" -ForegroundColor Gray
Write-Host "`n   - Ver logs en tiempo real:" -ForegroundColor Cyan
Write-Host "     Get-Content storage/logs/whatsapp-$(Get-Date -Format 'yyyy-MM-dd').log -Wait" -ForegroundColor Gray
Write-Host "`n   - Limpiar cache:" -ForegroundColor Cyan
Write-Host "     php artisan config:clear" -ForegroundColor Gray

Write-Host "`n========================================================" -ForegroundColor Cyan
Write-Host "   Documentacion: CAMBIOS_WEBHOOK_WHATSAPP.md" -ForegroundColor Cyan
Write-Host "========================================================`n" -ForegroundColor Cyan


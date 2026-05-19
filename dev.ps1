#$laravelCommand = "php -S 172.20.10.3:8000 -t public"
$laravelCommand = "php -S 192.168.1.89:8000 -t public"
$reverbCommand  = "php artisan reverb:start --host=0.0.0.0 --port=8080 --debug"
$queueCommand   = "php artisan queue:work --tries=3 --timeout=60"

# Obtener la ruta absoluta actual de forma robusta
$currentPath = Get-Location

# Lanzar Windows Terminal con pestañas separadas
wt -w 0 nt -d "$currentPath" --title "Laravel Server"     powershell -NoExit -Command "$laravelCommand" `; `
          nt -d "$currentPath" --title "Reverb WebSockets" powershell -NoExit -Command "$reverbCommand" `; `
          nt -d "$currentPath" --title "Queue Worker"      powershell -NoExit -Command "$queueCommand"

Write-Host "Entorno iniciado correctamente en la ruta: $currentPath"

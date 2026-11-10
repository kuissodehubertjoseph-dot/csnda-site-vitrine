@echo off
rem Partage le site par un lien public (tunnel Cloudflare).
rem Garder cette fenetre ouverte : le lien https://xxxx.trycloudflare.com s'affiche plus bas.
cd /d "%~dp0"
start "Serveur du site" /min php -S 127.0.0.1:8001 -t public deploiement\serveur-tunnel.php
timeout /t 3 >nul
"C:\Program Files (x86)\cloudflared\cloudflared.exe" tunnel --url http://127.0.0.1:8001 --no-autoupdate --protocol http2 --edge-ip-version 4

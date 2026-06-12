@echo off
title SAPA E-Antrian - Peluncur Monitor TV
echo ========================================================
echo Membuka Layar Monitor Antrean di Google Chrome...
echo Mode: Layar Penuh (Kiosk) dan Otomatis Putar Suara (Autoplay)
echo ========================================================

:: Pastikan mengubah URL ini sesuai dengan URL instance/monitor yang sebenarnya
set MONITOR_URL="http://127.0.0.1:8000"

:: Menjalankan Chrome dengan parameter khusus untuk TV/Signage
start chrome --autoplay-policy=no-user-gesture-required --kiosk %MONITOR_URL%

echo Selesai. Chrome telah dijalankan.
echo Tekan ALT + F4 pada keyboard jika ingin keluar dari mode layar penuh Chrome.
pause

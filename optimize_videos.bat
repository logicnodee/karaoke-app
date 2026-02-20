@echo off
setlocal enabledelayedexpansion

echo ========================================================
echo      OPTIMASI VIDEO WEB & GENERATE SPRITE PREVIEW
echo ========================================================
echo.

:: Cek apakah FFmpeg terinstall
where ffmpeg >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] FFmpeg tidak ditemukan!
    echo Harap install FFmpeg terlebih dahulu dan pastikan sudah masuk ke System PATH.
    echo Download: https://ffmpeg.org/download.html
    echo.
    
    exit /b
)

set "TARGET_DIR=public\assets\lagu"
set "TEMP_SUFFIX=_optimized"

if not exist "%TARGET_DIR%" (
    echo [ERROR] Folder Lagu tidak ditemukan: %TARGET_DIR%
    
    exit /b
)

echo Sedang memproses video di: %TARGET_DIR% ...
echo 1. Kompresi ulang (Faststart + Keyframes)
echo 2. Generate Sprite Sheet untuk Preview (160x90, tiap 5 detik)
echo --------------------------------------------------------

cd /d "%TARGET_DIR%"

for %%f in (*.mp4) do (
    echo [PROSES] %%f ...
    
    :: 1. Optimasi Video
    ffmpeg -y -i "%%f" -c:v libx264 -preset fast -g 48 -movflags +faststart -c:a copy "%%~nf%TEMP_SUFFIX%.mp4" -loglevel error

    if exist "%%~nf%TEMP_SUFFIX%.mp4" (
        :: Ganti file asli dengan file yang sudah dioptimasi
        move /y "%%~nf%TEMP_SUFFIX%.mp4" "%%f" >nul
        echo    [OK] Video optimized.
    ) else (
        echo    [GAGAL] Optimasi video %%f
    )

    :: 2. Generate Sprite
    :: fps=1/5: 1 frame every 5 seconds
    :: scale=160:90: Thumbnail size
    :: tile=10x10: Grid size (100 thumbs max, approx 8 mins duration coverage)
    ffmpeg -y -i "%%f" -vf "fps=1/5,scale=160:90,tile=10x10" "%%~nf_sprite.jpg" -loglevel error

    if exist "%%~nf_sprite.jpg" (
        echo    [OK] Sprite generated: %%~nf_sprite.jpg
    ) else (
        echo    [GAGAL] Generate sprite %%~nf
    )
    echo.
)

echo.
echo ========================================================
echo                 SEMUA SELESAI!
echo ========================================================
pause

const ffmpeg = require('ffmpeg-static');
const { execSync } = require('child_process');
const fs = require('fs');
const path = require('path');

const targetDir = path.join(__dirname, 'public/assets/lagu');

if (!fs.existsSync(targetDir)) {
    console.error(`Directory not found: ${targetDir}`);
    process.exit(1);
}

console.log(`=========================================`);
console.log(`OPTIMASI VIDEO & GENERATE SPRITE (Node.js)`);
console.log(`=========================================`);
console.log(`Folder Target: ${targetDir}`);
console.log(`FFmpeg Path: ${ffmpeg}`);
console.log(`-----------------------------------------`);

try {
    const files = fs.readdirSync(targetDir);

    files.forEach(file => {
        if (!file.endsWith('.mp4')) return;

        const filePath = path.join(targetDir, file);
        const fileName = path.parse(file).name;
        // Output sprite name exactly as requested
        const spritePath = path.join(targetDir, `${fileName}_sprite.jpg`);

        console.log(`[PROSES] ${file}`);

        try {
            if (fs.existsSync(spritePath)) {
                console.log(`  - Sprite sudah ada. Skip.`);
            } else {
                console.log(`  - Membuat sprite preview...`);
                // fps=1/5 (1 frame tiap 5 detik), scale 160x90, tile 10x10
                execSync(`"${ffmpeg}" -y -i "${filePath}" -vf "fps=1/5,scale=160:90,tile=10x10" "${spritePath}" -loglevel error`, { stdio: 'inherit' });
                console.log(`    [OK] Sprite berhasil dibuat.`);
            }
        } catch (error) {
            console.error(`  [ERROR] Gagal memproses ${file}:`, error.message);
        }
        console.log('');
    });
} catch (err) {
    console.error("Error utama:", err);
}

console.log(`=========================================`);
console.log(`SELESAI! Silakan refresh halaman.`);

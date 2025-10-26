# Stream Relay

**Self-hosted streaming relay**:  
`.m3u8`, `.mp4`, `.mpd` → **RTMP** (Twitch, YouTube, Kick, Telegram, etc)

## Features
- HLS / MP4 / DASH input
- MPD + **ClearKey decryption**
- Real-time **duration counter** (days → hours → mins)
- Live **FPS, bitrate, dropped frames**
- One-click **stop**
- Works with **any RTMP server**

---

## Deploy on VPS (Ubuntu/Debian)

```bash
# 1. Clone
git clone https://github.com/YOURNAME/stream-relay.git /var/www/streamer
cd /var/www/streamer

# 2. Install
sudo apt update && sudo apt install nginx php-fpm php-sqlite3 ffmpeg -y

# 3. Setup
sudo mkdir -p logs && sudo chown www-data:www-data logs
php -r "$db=new PDO('sqlite:db.sqlite'); $db->exec(file_get_contents('schema.sql'));"

# 4. Nginx config → see nginx.conf

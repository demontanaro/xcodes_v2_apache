#! /bin/bash
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 1
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 1
kill $(ps aux | grep 'xtreamcodes' | grep -v grep | grep -v 'start_services.sh' | awk '{print $2}') 2>/dev/null
sleep 4
sudo -u xtreamcodes /home/xtreamcodes/iptv_xtream_codes/php/bin/php /home/xtreamcodes/iptv_xtream_codes/crons/setup_cache.php
sudo -u xtreamcodes /home/xtreamcodes/iptv_xtream_codes/php/bin/php /home/xtreamcodes/iptv_xtream_codes/tools/signal_receiver.php >/dev/null 2>/dev/null &
sudo -u xtreamcodes /home/xtreamcodes/iptv_xtream_codes/php/bin/php /home/xtreamcodes/iptv_xtream_codes/tools/pipe_reader.php >/dev/null 2>/dev/null &
chown -R xtreamcodes:xtreamcodes /sys/class/net
chown -R xtreamcodes:xtreamcodes /home/xtreamcodes
sleep 4
/home/xtreamcodes/iptv_xtream_codes/nginx_rtmp/sbin/nginx_rtmp
/home/xtreamcodes/iptv_xtream_codes/nginx/sbin/nginx
start-stop-daemon --start --quiet --pidfile /home/xtreamcodes/iptv_xtream_codes/php/f3rD1w.pid --exec /home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm -- --daemonize --fpm-config /home/xtreamcodes/iptv_xtream_codes/php/etc/f3rD1w.conf
start-stop-daemon --start --quiet --pidfile /home/xtreamcodes/iptv_xtream_codes/php/Rgv8i1.pid --exec /home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm -- --daemonize --fpm-config /home/xtreamcodes/iptv_xtream_codes/php/etc/Rgv8i1.conf
start-stop-daemon --start --quiet --pidfile /home/xtreamcodes/iptv_xtream_codes/php/rmsC3z.pid --exec /home/xtreamcodes/iptv_xtream_codes/php/sbin/php-fpm -- --daemonize --fpm-config /home/xtreamcodes/iptv_xtream_codes/php/etc/rmsC3z.conf

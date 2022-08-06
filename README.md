subsmanager
===========

A Symfony project created on October 22, 2016, 10:42 am.

Subscriber manager application.


## Requirements for Local Development

* PHP 5.6 cli
* php5.6-gd
* php5.6-mbstring
* php5.6-xml
* php5-mysql
* Composer 1
* php5.6-fpm
* nginx 1
* mysql 5.7
* npm for bower install
* mysql dumps for databases


services:
```shell
systemctl status nginx.service      
nginx.service - A high performance web server and a reverse proxy server
     Loaded: loaded (/lib/systemd/system/nginx.service; disabled; vendor preset: enabled)
     Active: active (running) since Sat 2022-08-06 16:33:54 CEST; 34min ago

systemctl status php5.6-fpm.service 
php5.6-fpm.service - The PHP 5.6 FastCGI Process Manager
     Loaded: loaded (/lib/systemd/system/php5.6-fpm.service; enabled; vendor preset: enabled)
     Active: active (running) since Sat 2022-08-06 16:33:58 CEST; 34min ago

systemctl status mysql.service     
mysql.service - MySQL Community Server
     Loaded: loaded (/lib/systemd/system/mysql.service; disabled; vendor preset: enabled)
     Active: active (running) since Sat 2022-08-06 13:33:42 CEST; 3h 35min ago
```

make sure port 80 and 3306 are free:
```sh
sudo lsof -i :80
sudo lsof -i :3306
```

set user in fpm config:
```shell
/etc/php/5.6/fpm/pool.d/www.conf

user = linuxuser
group = linuxuser

listen.owner = linuxuser
listen.group = linuxuser

```

set nginx.conf:
```shell
cat /etc/nginx/nginx.conf 
#user www-data;
user linuxuser;
worker_processes auto;
pid /run/nginx.pid;
include /etc/nginx/modules-enabled/*.conf;

error_log  /var/log/nginx/error.log warn;

events {
	worker_connections 768;
	# multi_accept on;
}

http {
    
    ##
    # Basic Settings
    ##
    
    sendfile on;
    tcp_nopush on;
    tcp_nodelay on;
    keepalive_timeout 65;
    types_hash_max_size 2048;
    # server_tokens off;
    
    server_names_hash_bucket_size 64;
    # server_name_in_redirect off;
    
    include /etc/nginx/mime.types;
    default_type application/octet-stream;
    
    proxy_buffer_size   16k;
    proxy_buffers   64 4k;
    proxy_busy_buffers_size   24k;
    
    fastcgi_buffers 16 16k;
    fastcgi_buffer_size 32k; 
    
    log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
                  '$status $body_bytes_sent "$http_referer" '
                  '"$http_user_agent" "$http_x_forwarded_for"';
    
    ##
    # SSL Settings
    ##
    
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3; # Dropping SSLv3, ref: POODLE
    ssl_prefer_server_ciphers on;
    
    ##
    # Logging Settings
    ##
    
    access_log /var/log/nginx/access.log;
    error_log /var/log/nginx/error.log;
    
    ##
    # Gzip Settings
    ##
    
    gzip on;
    
    # gzip_vary on;
    # gzip_proxied any;
    # gzip_comp_level 6;
    # gzip_buffers 16 8k;
    # gzip_http_version 1.1;
    # gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
    
    ##
    # Virtual Host Configs
    ##
    
    include /etc/nginx/conf.d/*.conf;
    include /etc/nginx/sites-enabled/*;
}

```

sites-available:
```shell
cat /etc/nginx/sites-available/tornazzvelem.localhost 
server {
    listen localhost:80;
    server_name tornazzvelem.localhost www.tornazzvelem.localhost;
    root /home/linuxuser/Work/tornazzvelem/web;
    port_in_redirect off;

    location / {
        try_files $uri @rewriteapp;
    }

    location @rewriteapp {
        rewrite ^(.*)$ /app_dev.php/$1 last;
    }
    
    location ~ ^/(app|app_dev|config)\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php5.6-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;
    }

    error_log /var/log/nginx/tornazzvelemhu_error.log;
    access_log /var/log/nginx/tornazzvelemhu_access.log;
}

```
```shell
cat /etc/nginx/sites-available/berletek.tornazzvelem.localhost 
server {
    listen localhost:80;
    server_name berletek.tornazzvelem.localhost www.berletek.tornazzvelem.localhost;
    root /home/linuxuser/Work/subsmanager/web;
    port_in_redirect off;

    location / {
        try_files $uri @rewriteapp;
    }

    location @rewriteapp {
        rewrite ^(.*)$ /app_dev.php/$1 last;
    }

    location ~ ^/(app|app_dev|config)\.php(/|$) {
        fastcgi_pass unix:/var/run/php/php5.6-fpm.sock;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTPS off;
    }

    error_log /var/log/nginx/subsmanager_error.log;
    access_log /var/log/nginx/subsmanager_access.log;
} 

```

run:

```shell
sudo ln -s /etc/nginx/sites-available/berletek.tornazzvelem.localhost /etc/nginx/sites-enabled/
sudo ln -s /etc/nginx/sites-available/tornazzvelem.localhost /etc/nginx/sites-enabled/

remove the default vhost
```

`composer install`

`npm install -g bower`

`bower install`

set values in
`parameters.yaml`

remove the `https` config:
```shell
app/config/routing.yml
app:
    resource: "@AppBundle/Controller/"
    type:     annotation
#    defaults:
#        schemes: [https]

app/config/config_dev.yml
parameters:
  frontend_host: http://tornazzvelem.localhost
  login_faliure: http://berletek.tornazzvelem.localhost/login?embeded=true

```

You can watch the `app/logs` folder for further ``debugging``!!!

(
For using PUGXAutocompleterBundle don't forget to install smylink in web/bundles directory:
pugxautocompleter -> /var/www/subsmanager/vendor/pugx/autocompleter-bundle/Resources/public/
)

FROM node:25.2.1-alpine3.23 AS nodejs

FROM tangramor/nginx-php8-fpm:php8.5.1_withoutNodejs

# Instalar Nginx y dependencias del sistema
RUN apk add --no-cache \
  nginx \
  git \
  curl \
  libpng-dev \
  libxml2-dev \
  libzip-dev \
  zip \
  unzip \
  oniguruma-dev \
  supervisor

# Instalar extensiones de PHP
RUN docker-php-ext-install \
  pdo \
  pdo_mysql \
  mysqli \
  mbstring \
  exif \
  pcntl \
  bcmath \
  gd \
  zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos del proyecto
COPY . .

# Image config
ENV SKIP_COMPOSER 1
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1

# Laravel config
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
#ENV APP_KEY base64:xezR3eGeEbhmx0sQjF6JKSoO72QyBE9ScmoUzMbgkwI=

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache \
  &&  chmod -R 775  /var/www/html/storage/logs/laravel.log


# Copiar .env.example a .env si no existe
RUN cp -n .env.example .env || true

# Instalar dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Generar clave de aplicación
RUN php artisan key:generate --force || true

RUN apk add --no-cache \
  nodejs \
  npm

# Dar permisos
RUN chown -R nobody:nobody /var/www/html/storage /var/www/html/bootstrap/cache && \
  chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Configurar Nginx
RUN mkdir -p /etc/nginx/http.d
COPY <<'EOF' /etc/nginx/http.d/default.conf
# ============================================
# Configuración Nginx Multi-Tenant - Docker
# ============================================
# Archivo: nginx-multitenant-docker.conf
# Uso: Para contenedores Docker

# Servidor para dominio central (sin subdominios)
server {
    listen 8080;
    server_name adminpos.dokploy.movete.cloud;
    
    root /var/www/html/public;
    index index.php index.html index.htm;
    
    charset utf-8;
    
    # Logs específicos para dominio central
    access_log /var/log/nginx/central-access.log;
    error_log /var/log/nginx/central-error.log;
    
    # Headers de seguridad
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    
    # Aumentar tamaños máximos
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    
    # Bloquear acceso a archivos sensibles
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    location /.git {
        deny all;
        return 403;
    }
    
    # Favicon y robots
    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }
    
    location = /robots.txt  { 
        access_log off; 
        log_not_found off; 
    }
    
    # Assets estáticos con cache
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|tiff|ttf|svg|woff|woff2|eot)$ {
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Directorio build (Vite assets)
    location /build/ {
        alias /var/www/html/public/build/;
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Directorio vendor
    location /vendor/ {
        alias /var/www/html/public/vendor/;
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Storage público
    location /storage/ {
        alias /var/www/html/storage/app/public/;
        expires 7d;
        add_header Cache-Control "public";
    }
    
    # Ruta principal - Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Manejo de PHP
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;
        
        # Aumentar timeouts para procesos largos
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        
        # Buffer settings
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }
    
    # Denegar acceso a archivos PHP en storage
    location ~* ^/storage/.*\.php$ {
        deny all;
    }
}

# Servidor wildcard para TODOS los subdominios (tenants)
server {
    listen 8080;
    # Wildcard: captura cualquier subdominio
    server_name ~^(?<tenant>.+)\.adminpos\.dokploy\.movete\.cloud$;
    
    root /var/www/html/public;
    index index.php index.html index.htm;
    
    charset utf-8;
    
    # Logs específicos para tenants
    access_log /var/log/nginx/tenants-access.log;
    error_log /var/log/nginx/tenants-error.log;
    
    # Headers de seguridad
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    
    # Aumentar tamaños máximos
    client_max_body_size 100M;
    client_body_buffer_size 128k;
    
    # Pasar el subdominio como variable a PHP
    fastcgi_param TENANT_SUBDOMAIN $tenant;
    
    # Bloquear acceso a archivos sensibles
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    location /.git {
        deny all;
        return 403;
    }
    
    # Favicon y robots
    location = /favicon.ico { 
        access_log off; 
        log_not_found off; 
    }
    
    location = /robots.txt  { 
        access_log off; 
        log_not_found off; 
    }
    
    # Assets estáticos con cache
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|tiff|ttf|svg|woff|woff2|eot)$ {
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Directorio build (Vite assets)
    location /build/ {
        alias /var/www/html/public/build/;
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Directorio vendor
    location /vendor/ {
        alias /var/www/html/public/vendor/;
        expires 365d;
        add_header Cache-Control "public, no-transform";
        add_header Access-Control-Allow-Origin "*";
        access_log off;
    }
    
    # Storage por tenant
    location /storage/ {
        alias /var/www/html/storage/app/public/;
        expires 7d;
        add_header Cache-Control "public";
    }
    
    # Ruta principal - Laravel
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # Manejo de PHP
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
        include fastcgi_params;
        
        # Aumentar timeouts para procesos largos
        fastcgi_read_timeout 300;
        fastcgi_send_timeout 300;
        
        # Buffer settings
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
    }
    
    # Denegar acceso a archivos PHP en storage
    location ~* ^/storage/.*\.php$ {
        deny all;
    }
}
EOF


RUN mkdir -p /etc/nginx/sites-available
COPY <<'EOF' /etc/nginx/sites-available/default-ssl.conf
server {
	listen 443 ssl http2;
        listen [::]:443 ssl http2 ipv6only=on; ## listen for ipv6

	root /var/www/html;
	index index.php index.html index.htm;

	# Make site accessible from http://localhost/
        server_name _;
        ssl_certificate     /etc/letsencrypt/live/##DOMAIN##/fullchain.pem;
        ssl_certificate_key /etc/letsencrypt/live/##DOMAIN##/privkey.pem;
        ssl_protocols       TLSv1 TLSv1.1 TLSv1.2;
        ssl_ciphers         HIGH:!aNULL:!MD5;

	# Make site accessible from http://localhost/
	server_name _;
	
	# Disable sendfile as per https://docs.vagrantup.com/v2/synced-folders/virtualbox.html
	sendfile off;

	# Add stdout logging
	error_log /dev/stdout info;
	access_log /dev/stdout;

        # Add option for x-forward-for (real ip when behind elb)
        #real_ip_header X-Forwarded-For;
        #set_real_ip_from 172.16.0.0/12;

	# block access to sensitive information about git
	location /.git {
           deny all;
           return 403;
        }

	location / {
		# First attempt to serve request as file, then
		# as directory, then fall back to index.html
		try_files $uri $uri/ =404;
	}

	error_page 404 /404.html;
        location = /404.html {
                root /var/www/errors;
                internal;
        }

        location ^~ /ngd-style.css {
            alias /var/www/errors/style.css;
            access_log off;
        }

        location ^~ /ngd-sad.svg {
            alias /var/www/errors/sad.svg;
            access_log off;
        }

	# pass the PHP scripts to FastCGI server listening on socket
	#
	location ~ \.php$ {
                try_files $uri =404;
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass unix:/var/run/php-fpm.sock;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    		fastcgi_param SCRIPT_NAME $fastcgi_script_name;
		fastcgi_index index.php;
#        fastcgi_param GEOIP2_LONGITUDE $geoip2_data_longitude;
#        fastcgi_param GEOIP2_LATITUDE $geoip2_data_latitude;
#        fastcgi_param GEOIP2_CONTINENT_CODE $geoip2_data_continent_code;
#        fastcgi_param GEOIP2_CONTINENT_NAME $geoip2_data_continent_name;
#        fastcgi_param GEOIP2_COUNTRY_CODE $geoip2_data_country_code;
#        fastcgi_param GEOIP2_COUNTRY_NAME $geoip2_data_country_name;
#        fastcgi_param GEOIP2_STATE_CODE $geoip2_data_state_code;
#        fastcgi_param GEOIP2_STATE_NAME $geoip2_data_state_name;
#        fastcgi_param GEOIP2_CITY_NAME $geoip2_data_city_name;
#        fastcgi_param GEOIP2_POSTAL_CODE $geoip2_data_postal_code;
		include fastcgi_params;
	}

        location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|tiff|ttf|svg)$ {
                expires           5d;
        }

	# deny access to . files, for security
	#
	location ~ /\. {
    		log_not_found off; 
    		deny all;
	}
        
	location ^~ /.well-known {
                allow all;
                auth_basic off;
        }

}
EOF

RUN mkdir -p /etc/nginx/sites-available
COPY <<'EOF' /etc/nginx/sites-available/nginx-site.conf
server {
	listen   80; ## listen for ipv4; this line is default and implied
	listen   [::]:80 default ipv6only=on; ## listen for ipv6

	root /var/www/html;
	index index.php index.html index.htm;

	# Make site accessible from http://localhost/
	server_name _;
	
	# Disable sendfile as per https://docs.vagrantup.com/v2/synced-folders/virtualbox.html
	sendfile off;

	# Add stdout logging
	error_log /dev/stdout info;
	access_log /dev/stdout;

        # Add option for x-forward-for (real ip when behind elb)
        #real_ip_header X-Forwarded-For;
        #set_real_ip_from 172.16.0.0/12;

	# block access to sensitive information about git
	location /.git {
           deny all;
           return 403;
        }

	location / {
		# First attempt to serve request as file, then
		# as directory, then fall back to index.html
		try_files $uri $uri/ =404;
	}

	error_page 404 /404.html;
        location = /404.html {
                root /var/www/errors;
                internal;
        }

        location ^~ /sad.svg {
            alias /var/www/errors/sad.svg;
            access_log off;
        }
        location ^~ /twitter.svg {
            alias /var/www/errors/twitter.svg;
            access_log off;
        }
        location ^~ /github.svg {
            alias /var/www/errors/github.svg;
            access_log off;
        }

	# pass the PHP scripts to FastCGI server listening on socket
	#
	location ~ \.php$ {
                try_files $uri =404;
		fastcgi_split_path_info ^(.+\.php)(/.+)$;
		fastcgi_pass unix:/var/run/php-fpm.sock;
		fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    		fastcgi_param SCRIPT_NAME $fastcgi_script_name;
		fastcgi_index index.php;
#        fastcgi_param GEOIP2_LONGITUDE $geoip2_data_longitude;
#        fastcgi_param GEOIP2_LATITUDE $geoip2_data_latitude;
#        fastcgi_param GEOIP2_CONTINENT_CODE $geoip2_data_continent_code;
#        fastcgi_param GEOIP2_CONTINENT_NAME $geoip2_data_continent_name;
#        fastcgi_param GEOIP2_COUNTRY_CODE $geoip2_data_country_code;
#        fastcgi_param GEOIP2_COUNTRY_NAME $geoip2_data_country_name;
#        fastcgi_param GEOIP2_STATE_CODE $geoip2_data_state_code;
#        fastcgi_param GEOIP2_STATE_NAME $geoip2_data_state_name;
#        fastcgi_param GEOIP2_CITY_NAME $geoip2_data_city_name;
#        fastcgi_param GEOIP2_POSTAL_CODE $geoip2_data_postal_code;
		include fastcgi_params;
	}

        location ~* \.(jpg|jpeg|gif|png|css|js|ico|webp|tiff|ttf|svg)$ {
                expires           5d;
        }

	# deny access to . files, for security
	#
	location ~ /\. {
    		log_not_found off; 
    		deny all;
	}
        
	location ^~ /.well-known {
                allow all;
                auth_basic off;
        }

}
EOF


COPY <<'EOF' /etc/nginx/nginx.conf
#user  nobody;
worker_processes auto;

#error_log  logs/error.log;
#error_log  logs/error.log  notice;
#error_log  logs/error.log  info;

#pid        run/nginx.pid;


events {
    worker_connections  1024;
}


http {
    include       mime.types;
    default_type  application/octet-stream;

    #log_format  main  '$remote_addr - $remote_user [$time_local] "$request" '
    #                  '$status $body_bytes_sent "$http_referer" '
    #                  '"$http_user_agent" "$http_x_forwarded_for"';

    #access_log  logs/access.log  main;

    sendfile        on;
    #tcp_nopush     on;

    #keepalive_timeout  0;
    keepalive_timeout 2;
	client_max_body_size 100m;

    server_tokens off;
    #gzip  on;

# Disabled due to license
#    geoip2 /etc/nginx/GeoLite2-Country.mmdb {
#        auto_reload 1h;
#
#        $geoip2_metadata_country_build metadata build_epoch;
#
#        # populate the country
#        $geoip2_data_country_code source=$remote_addr country iso_code;
#        $geoip2_data_country_name source=$remote_addr country names en;
#
#        # populate the continent
#        $geoip2_data_continent_code source=$remote_addr continent code;
#        $geoip2_data_continent_name source=$remote_addr continent names en;
#    }
#
#    geoip2 /etc/nginx/GeoLite2-City.mmdb {
#        auto_reload 1h;
#
#        # City name itself
#        $geoip2_data_city_name source=$remote_addr city names en;
#
#        # Postal code will be an approximation, probably the first one in the list that covers an area
#        $geoip2_data_postal_code source=$remote_addr postal code;
#
#        # State in code and long form
#        $geoip2_data_state_code source=$remote_addr subdivisions 0 iso_code;
#        $geoip2_data_state_name source=$remote_addr subdivisions 0 names en;
#
#        # Lat and Lng
#        $geoip2_data_latitude source=$remote_addr location latitude;
#        $geoip2_data_longitude source=$remote_addr location longitude;
#    }

    include /etc/nginx/sites-enabled/*;
}
#daemon off;
EOF


# Configurar supervisor
COPY <<'EOF' /etc/supervisord.conf
[supervisord]
logfile=/tmp/supervisord.log ; (main log file;default $CWD/supervisord.log)
logfile_maxbytes=50MB        ; (max main logfile bytes b4 rotation;default 50MB)
logfile_backups=10           ; (num of main logfile rotation backups;default 10)
loglevel=info                ; (log level;default info; others: debug,warn,trace)
pidfile=/tmp/supervisord.pid ; (supervisord pidfile;default supervisord.pid)
nodaemon=false               ; (start in foreground if true;default false)
minfds=1024                  ; (min. avail startup file descriptors;default 1024)
minprocs=200                 ; (min. avail process descriptors;default 200)
user=root		     ;

[program:php-fpm]
command = /usr/local/sbin/php-fpm --force-stderr --nodaemonize --fpm-config /usr/local/etc/php-fpm.d/www.conf
autostart=true
autorestart=true
priority=5
stdout_events_enabled=true
stderr_events_enabled=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
stopsignal=QUIT

[program:nginx]
command=/usr/sbin/nginx -g "daemon off; error_log /dev/stderr info;"
autostart=true
autorestart=true
priority=10
stdout_events_enabled=true
stderr_events_enabled=true
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
stopsignal=QUIT

[include]
files = /etc/supervisor/conf.d/*.conf
EOF

# Add Scripts
COPY <<'EOF' /start.sh
#!/bin/bash

# Disable Strict Host checking for non interactive git clones

mkdir -p -m 0700 /root/.ssh
# Prevent config files from being filled to infinity by force of stop and restart the container 
echo "" > /root/.ssh/config
echo -e "Host *\n\tStrictHostKeyChecking no\n" >> /root/.ssh/config

if [[ "$GIT_USE_SSH" == "1" ]] ; then
  echo -e "Host *\n\tUser ${GIT_USERNAME}\n\n" >> /root/.ssh/config
fi

if [ ! -z "$SSH_KEY" ]; then
 echo $SSH_KEY > /root/.ssh/id_rsa.base64
 base64 -d /root/.ssh/id_rsa.base64 > /root/.ssh/id_rsa
 chmod 600 /root/.ssh/id_rsa
fi

# Set custom webroot
if [ ! -z "$WEBROOT" ]; then
 sed -i "s#root /var/www/html;#root ${WEBROOT};#g" /etc/nginx/sites-available/default.conf
else
 webroot=/var/www/html
fi

# Enables 404 pages through php index
if [ ! -z "$PHP_CATCHALL" ]; then
 sed -i 's#try_files $uri $uri/ =404;#try_files $uri $uri/ /index.php?$args;#g' /etc/nginx/sites-available/default.conf
fi

# Disable opcache
if [ ! -z "$OPcache" ]; then
 sed -i 's#zend_extension=opcache#;zend_extension=opcache#g' /usr/local/etc/php/php.ini
fi

# Setup git variables
if [ ! -z "$GIT_EMAIL" ]; then
 git config --global user.email "$GIT_EMAIL"
fi
if [ ! -z "$GIT_NAME" ]; then
 git config --global user.name "$GIT_NAME"
 git config --global push.default simple
fi

# Dont pull code down if the .git folder exists
if [ ! -d "/var/www/html/.git" ]; then
 # Pull down code from git for our site!
 if [ ! -z "$GIT_REPO" ]; then
   # Remove the test index file if you are pulling in a git repo
   if [ ! -z ${REMOVE_FILES} ] && [ ${REMOVE_FILES} == 0 ]; then
     echo "skiping removal of files"
   else
     rm -Rf /var/www/html/*
   fi
   GIT_COMMAND='git clone '
   if [ ! -z "$GIT_BRANCH" ]; then
     GIT_COMMAND=${GIT_COMMAND}" -b ${GIT_BRANCH}"
   fi

   if [ -z "$GIT_USERNAME" ] && [ -z "$GIT_PERSONAL_TOKEN" ]; then
     GIT_COMMAND=${GIT_COMMAND}" ${GIT_REPO}"
   else
    if [[ "$GIT_USE_SSH" == "1" ]]; then
      GIT_COMMAND=${GIT_COMMAND}" ${GIT_REPO}"
    else
      GIT_COMMAND=${GIT_COMMAND}" https://${GIT_USERNAME}:${GIT_PERSONAL_TOKEN}@${GIT_REPO}"
    fi
   fi
   ${GIT_COMMAND} /var/www/html || exit 1
   if [ ! -z "$GIT_TAG" ]; then
     git checkout ${GIT_TAG} || exit 1
   fi
   if [ ! -z "$GIT_COMMIT" ]; then
     git checkout ${GIT_COMMIT} || exit 1
   fi
   if [ -z "$SKIP_CHOWN" ]; then
     chown -Rf nginx.nginx /var/www/html
   fi
 fi
fi

# Enable custom nginx config files if they exist
if [ -f /var/www/html/conf/nginx/nginx.conf ]; then
  cp /var/www/html/conf/nginx/nginx.conf /etc/nginx/nginx.conf
fi

if [ -f /var/www/html/conf/nginx/nginx-site.conf ]; then
  cp /var/www/html/conf/nginx/nginx-site.conf /etc/nginx/sites-available/default.conf
fi

if [ -f /var/www/html/conf/nginx/nginx-site-ssl.conf ]; then
  cp /var/www/html/conf/nginx/nginx-site-ssl.conf /etc/nginx/sites-available/default-ssl.conf
fi


# Prevent config files from being filled to infinity by force of stop and restart the container
lastlinephpconf="$(grep "." /usr/local/etc/php-fpm.conf | tail -1)"
if [[ $lastlinephpconf == *"php_flag[display_errors]"* ]]; then
 sed -i '$ d' /usr/local/etc/php-fpm.conf
fi

# Display PHP error's or not
if [[ "$ERRORS" != "1" ]] ; then
 echo php_flag[display_errors] = off >> /usr/local/etc/php-fpm.d/www.conf
else
 echo php_flag[display_errors] = on >> /usr/local/etc/php-fpm.d/www.conf
fi

# Display Version Details or not
if [[ "$HIDE_NGINX_HEADERS" == "0" ]] ; then
 sed -i "s/server_tokens off;/server_tokens on;/g" /etc/nginx/nginx.conf
else
 sed -i "s/expose_php = On/expose_php = Off/g" /usr/local/etc/php-fpm.conf
fi

# Pass real-ip to logs when behind ELB, etc
if [[ "$REAL_IP_HEADER" == "1" ]] ; then
 sed -i "s/#real_ip_header X-Forwarded-For;/real_ip_header X-Forwarded-For;/" /etc/nginx/sites-available/default.conf
 sed -i "s/#set_real_ip_from/set_real_ip_from/" /etc/nginx/sites-available/default.conf
 if [ ! -z "$REAL_IP_FROM" ]; then
  sed -i "s#172.16.0.0/12#$REAL_IP_FROM#" /etc/nginx/sites-available/default.conf
 fi
fi
# Do the same for SSL sites
if [ -f /etc/nginx/sites-available/default-ssl.conf ]; then
 if [[ "$REAL_IP_HEADER" == "1" ]] ; then
  sed -i "s/#real_ip_header X-Forwarded-For;/real_ip_header X-Forwarded-For;/" /etc/nginx/sites-available/default-ssl.conf
  sed -i "s/#set_real_ip_from/set_real_ip_from/" /etc/nginx/sites-available/default-ssl.conf
  if [ ! -z "$REAL_IP_FROM" ]; then
   sed -i "s#172.16.0.0/12#$REAL_IP_FROM#" /etc/nginx/sites-available/default-ssl.conf
  fi
 fi
fi

# Set the desired timezone
echo date.timezone=Europe/London > /usr/local/etc/php/conf.d/timezone.ini

# Display errors in docker logs
if [ ! -z "$PHP_ERRORS_STDERR" ]; then
  echo "log_errors = On" >> /usr/local/etc/php/conf.d/docker-vars.ini
  echo "error_log = /dev/stderr" >> /usr/local/etc/php/conf.d/docker-vars.ini
fi

# Increase the memory_limit
if [ ! -z "$PHP_MEM_LIMIT" ]; then
 sed -i "s/memory_limit = 128M/memory_limit = ${PHP_MEM_LIMIT}M/g" /usr/local/etc/php/conf.d/docker-vars.ini
fi

# Increase the post_max_size
if [ ! -z "$PHP_POST_MAX_SIZE" ]; then
 sed -i "s/post_max_size = 100M/post_max_size = ${PHP_POST_MAX_SIZE}M/g" /usr/local/etc/php/conf.d/docker-vars.ini
fi

# Increase the upload_max_filesize
if [ ! -z "$PHP_UPLOAD_MAX_FILESIZE" ]; then
 sed -i "s/upload_max_filesize = 100M/upload_max_filesize= ${PHP_UPLOAD_MAX_FILESIZE}M/g" /usr/local/etc/php/conf.d/docker-vars.ini
fi

# Enable xdebug
XdebugFile='/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini'
if [[ "$ENABLE_XDEBUG" == "1" ]] ; then
  if [ -f $XdebugFile ]; then
  	echo "Xdebug enabled"
  else
  	echo "Enabling xdebug"
  	echo "If you get this error, you can safely ignore it: /usr/local/bin/docker-php-ext-enable: line 83: nm: not found"
  	# see https://github.com/docker-library/php/pull/420
    docker-php-ext-enable xdebug
    # see if file exists
    if [ -f $XdebugFile ]; then
        # See if file contains xdebug text.
        if grep -q xdebug.remote_enable "$XdebugFile"; then
            echo "Xdebug already enabled... skipping"
        else
            echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > $XdebugFile # Note, single arrow to overwrite file.
            echo "xdebug.start_with_request=yes"  >> $XdebugFile
            echo "xdebug.client_host=host.docker.internal" >> $XdebugFile
            echo "xdebug.mode=debug" >> $XdebugFile
            echo "xdebug.remote_log=/tmp/xdebug.log"  >> $XdebugFile
            echo "xdebug.remote_autostart=false "  >> $XdebugFile # I use the xdebug chrome extension instead of using autostart
            # NOTE: xdebug.remote_host is not needed here if you set an environment variable in docker-compose like so `- XDEBUG_CONFIG=remote_host=192.168.111.27`.
            #       you also need to set an env var `- PHP_IDE_CONFIG=serverName=docker`
        fi
    fi
  fi
else
    if [ -f $XdebugFile ]; then
        echo "Disabling Xdebug"
      rm $XdebugFile
    fi
fi

if [ ! -z "$PUID" ]; then
  if [ -z "$PGID" ]; then
    PGID=${PUID}
  fi
  deluser nginx
  addgroup -g ${PGID} nginx
  adduser -D -S -h /var/cache/nginx -s /sbin/nologin -G nginx -u ${PUID} nginx
else
  if [ -z "$SKIP_CHOWN" ]; then
    chown -Rf nginx.nginx /var/www/html
  fi
fi

# Run custom scripts
if [[ "$RUN_SCRIPTS" == "1" ]] ; then
  scripts_dir="${SCRIPTS_DIR:-/var/www/html/scripts}"
  if [ -d "$scripts_dir" ]; then
    if [ -z "$SKIP_CHMOD" ]; then
      # make scripts executable incase they aren't
      chmod -Rf 750 $scripts_dir; sync;
    fi
    # run scripts in number order
    for i in `ls $scripts_dir`; do $scripts_dir/$i ; done
  else
    echo "Can't find script directory"
  fi
fi

if [ -z "$SKIP_COMPOSER" ]; then
    # Try auto install for composer
    if [ -f "/var/www/html/composer.lock" ]; then
        if [ "$APPLICATION_ENV" == "development" ]; then
            composer global require hirak/prestissimo
            composer install --working-dir=/var/www/html
        else
            composer global require hirak/prestissimo
            composer install --no-dev --working-dir=/var/www/html
        fi
    fi
fi

# Start supervisord and services
exec /usr/bin/supervisord -n -c /etc/supervisord.conf
EOF
RUN chmod 755 /start.sh

COPY start.sh /start.sh


EXPOSE 443 80 8080

CMD ["/start.sh"]

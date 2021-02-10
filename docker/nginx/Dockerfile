FROM nginx:stable-alpine

ENV NGINX_ENTRYPOINT_QUIET_LOGS=1 \
	DNS_RESOLVER=127.0.0.11 \
	PHPFPM_UPSTREAM=127.0.0.1:9000

RUN set -xe; \
    rm -f /etc/nginx/conf.d/*; \
    mkdir -p /etc/nginx/templates; \
    { \
        echo 'user nginx;'; \
        echo 'worker_processes auto;'; \
        echo 'pid /var/run/nginx.pid;'; \
        echo ; \
        echo 'events {'; \
        echo '    worker_connections 1024;'; \
        echo '}'; \
        echo ; \
        echo 'http {'; \
        echo '    server_tokens off;'; \
        echo ; \
        echo '    sendfile on;'; \
        echo '    tcp_nopush on;'; \
        echo '    tcp_nodelay on;'; \
        echo ; \
        echo '    keepalive_requests 100;'; \
        echo '    keepalive_timeout 65;'; \
        echo ; \
        echo '    types_hash_max_size 2048;'; \
        echo '    server_names_hash_bucket_size 128;'; \
        echo ; \
        echo '    include /etc/nginx/mime.types;'; \
        echo '    default_type application/octet-stream;'; \
        echo ; \
        echo '    map $http_x_request_id $request_uid {'; \
        echo '        default   $http_x_request_id;'; \
        echo '        ""        $request_id;'; \
        echo '    }'; \
        echo ; \
        echo '    log_format main '\''$remote_addr - [$time_local] "$request" $status $body_bytes_sent "$http_referer" "$http_user_agent" "$http_x_forwarded_for" $request_time $request_uid'\'';'; \
        echo '    access_log /var/log/nginx/access.log main;'; \
        echo '    error_log /var/log/nginx/error.log error;'; \
        echo ; \
        echo '    gzip on;'; \
        echo '    gzip_disable "msie6";'; \
        echo ; \
        echo '    include /etc/nginx/conf.d/*.conf;'; \
        echo '}'; \
    } | tee /etc/nginx/nginx.conf; \
    { \
        echo 'server {'; \
        echo '    root /var/www/html/public;'; \
        echo ; \
        echo '    add_header X-Request-Id $request_uid;'; \
        echo ; \
        echo '    location / {'; \
        echo '        try_files $uri /index.php$is_args$args;'; \
        echo '    }'; \
        echo ; \
        echo '     location ~ ^/index\.php(/|$) {'; \
        echo '        resolver ${DNS_RESOLVER};'; \
        echo '        set $upstream ${PHPFPM_UPSTREAM};'; \
        echo '        fastcgi_pass $upstream;'; \
        echo '        include fastcgi_params;'; \
        echo '        fastcgi_split_path_info ^(.+\.php)(/.*)$;'; \
        echo '        fastcgi_param HTTP_X_REQUEST_ID $request_uid;'; \
        echo '        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;'; \
        echo '        fastcgi_param DOCUMENT_ROOT $realpath_root;'; \
        echo '        internal;'; \
        echo '    }'; \
        echo ; \
        echo '    location ~ \.php$ {'; \
        echo '        return 404;'; \
        echo '    }'; \
        echo '}'; \
    } | tee /etc/nginx/templates/default.conf.template;

FROM ubuntu:22.04

# 환경 변수 설정
ENV DEBIAN_FRONTEND=noninteractive

# 기본 패키지 업데이트 및 필요한 패키지 설치
RUN apt-get update && apt-get install -y \
    nginx \
    php8.1-fpm \
    php8.1-cli \
    php8.1-common \
    supervisor \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# PHP-FPM 설정 수정 (127.0.0.1:9000에서 리슨하도록)
RUN sed -i 's/listen = \/run\/php\/php8.1-fpm.sock/listen = 127.0.0.1:9000/' /etc/php/8.1/fpm/pool.d/www.conf

# nginx 기본 설정 제거 및 커스텀 설정 복사
RUN rm -f /etc/nginx/sites-enabled/default
COPY nginx.conf /etc/nginx/sites-available/webshell
RUN ln -s /etc/nginx/sites-available/webshell /etc/nginx/sites-enabled/webshell

# 웹 루트 디렉토리 생성
RUN mkdir -p /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 777 /var/www/html/uploads

# 웹 파일 복사
COPY www/ /var/www/html/

# Supervisor 설정
RUN mkdir -p /var/log/supervisor
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 포트 노출
EXPOSE 80

# Supervisor 실행
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

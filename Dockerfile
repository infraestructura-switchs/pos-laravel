FROM ubuntu:jammy

RUN apt-get -qq update \
    && apt-get -qq install -y --no-install-recommends nginx \
    && rm -rf /var/lib/apt/lists/*

COPY html/index.html /var/www/html/index.html

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

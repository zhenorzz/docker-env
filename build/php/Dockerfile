FROM php:7.4.8-fpm

COPY resource /home/resource

ARG CHANGE_SOURCE=false
ARG TIME_ZONE=UTC

ENV TIME_ZONE=${TIME_ZONE} LC_ALL=C.UTF-8

RUN \
    # ⬇ 修改时区
    ln -snf /usr/share/zoneinfo/$TIME_ZONE /etc/localtime ; \
    echo $TIME_ZONE > /etc/timezone ; \
    \
    # ⬇ 安装 PHP Composer
    mv /home/resource/composer.phar /usr/local/bin/composer ; \
    chmod 755 /usr/local/bin/composer ; \
    \
    # ⬇ 替换源
    rm -rf /etc/apt/sources.list.d/buster.list ; \
    if [ ${CHANGE_SOURCE} = true ]; then \
        mv /etc/apt/sources.list /etc/apt/source.list.bak; \
        mv /home/resource/sources.list /etc/apt/sources.list; \
        composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/ ; \
    fi; \
    \
    # ⬇ 更新、安装基础组件
    apt-get update && apt-get install -y --allow-remove-essential --no-install-recommends \
    procps \
    git \
    zip \
    unzip \
    zlib1g-dev \
    openssl \
    nasm\
    yasm \
    libx264-dev \
    libx265-dev \
    libssl-dev \
    libtinfo5 \
    libpq-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libmcrypt-dev \
    libmhash-dev \
    libzip-dev \
    libxml2-dev \
    libfdk-aac-dev\
    ntpdate \
    cron;

####################################################################################
# 安装 FFMPEG 扩展
####################################################################################
RUN mkdir -p ~/ffmpeg_sources && cd ~/ffmpeg_sources && \
    tar zxvf /home/resource/ffmpeg-4.2.2.tar.gz && \
    cd ffmpeg-4.2.2 && \
    ./configure \
       --enable-gpl \
       --enable-libx264 \
       --extra-libs=-ldl \
       --enable-nonfree \
       --enable-libfdk-aac && \
    make -j$(nproc) && \
    make install -j$(nproc)

####################################################################################
# 安装 PHP 扩展
####################################################################################

RUN docker-php-ext-configure gd \
    --with-freetype-dir=/usr/include/ \
    --with-jpeg-dir=/usr/include/ ; \
    docker-php-ext-configure zip --with-zlib-dir=/usr ; \
    docker-php-ext-configure sysvmsg --enable-sysvmsg; \
    docker-php-ext-configure sysvsem --enable-sysvsem; \
    docker-php-ext-configure sysvshm --enable-sysvshm; \
    docker-php-ext-install -j$(nproc) gd pcntl pdo_mysql mysqli soap zip bcmath sockets sysvmsg sysvsem sysvshm ; \
    \
    # ⬇ Redis
    pecl install /home/resource/redis-5.0.2.tgz ; \ 
    echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini ; \
    \
    # ⬇ Swoole
    pecl install /home/resource/swoole-4.5.2.tgz ; \
    echo "extension=swoole.so" > /usr/local/etc/php/conf.d/swoole.ini ; \
    \
    # ⬇ Mcrypt
    pecl install /home/resource/mcrypt-1.0.3.tgz ; \
    echo "extension=mcrypt.so" > /usr/local/etc/php/conf.d/mcrypt.ini ; \
    \
    # ⬇ Mongodb
    pecl install /home/resource/mongodb-1.6.0.tgz ; \
    echo "extension=mongodb.so" > /usr/local/etc/php/conf.d/mongodb.ini ; \
    \
     # ⬇ Protobuf
    pecl install /home/resource/protobuf-3.12.2.tgz ; \
    echo "extension=protobuf.so" > /usr/local/etc/php/conf.d/protobuf.ini ; \
    \
     # ⬇ Inotify
    pecl install /home/resource/inotify-3.0.0.tgz ; \
    echo "extension=inotify.so" > /usr/local/etc/php/conf.d/inotify.ini ; \
    \
    # ⬇ 清理
    rm -rf /var/lib/apt/lists/* ; \
    apt-get purge -y --auto-remove -o APT::AutoRemove::RecommendsImportant=false ; \
    rm -rf /home/resource ;

####################################################################################
# 最后才换 dash to bash 
####################################################################################
RUN ln -fs /bin/bash /bin/sh
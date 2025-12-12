# Usando a imagem oficial do PHP 8.1 com Apache 
FROM php:8.1-apache

# Atualiza pacotes e instala extensões necessárias + dependências para Dompdf
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libfontconfig1 libzip-dev unzip curl git vim cron \
    libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli sockets pdo_pgsql zip

# Instala Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copia os arquivos do projeto para a pasta do Apache
COPY . /var/www/html/

# Instala dependências do projeto e AWS SDK
RUN composer require aws/aws-sdk-php \
    && composer install || true

# Define diretório de trabalho e instala dependências do projeto (php-jwt, aws-sdk, dompdf etc)
WORKDIR /var/www/html
RUN composer install || true

# Define permissões corretas para o Apache
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Copia e ativa a crontab, se existir
COPY crontab.txt /var/spool/cron/crontabs/root
RUN chmod 600 /var/spool/cron/crontabs/root || true && \
    crontab /var/spool/cron/crontabs/root || true

# Habilita o módulo rewrite do Apache
RUN a2enmod rewrite

# Ajusta configurações do php.ini
RUN echo "upload_max_filesize = 50M" >> /usr/local/etc/php/php.ini && \
    echo "post_max_size = 50M" >> /usr/local/etc/php/php.ini

# Configura o Apache para servir a pasta /var/www/html
RUN echo "DocumentRoot /var/www/html" > /etc/apache2/sites-available/000-default.conf && \
    echo "<Directory /var/www/html>" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    AllowOverride All" >> /etc/apache2/sites-available/000-default.conf && \
    echo "    Require all granted" >> /etc/apache2/sites-available/000-default.conf && \
    echo "</Directory>" >> /etc/apache2/sites-available/000-default.conf

# Configura timezone
RUN ln -sf /usr/share/zoneinfo/America/Sao_Paulo /etc/localtime && \
    echo "America/Sao_Paulo" > /etc/timezone

# Expõe a porta padrão do Apache
EXPOSE 80

# Inicia o Apache no foreground
CMD ["apache2-foreground"]

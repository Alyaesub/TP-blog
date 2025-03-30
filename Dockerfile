# Image de base PHP 8.2 avec Apache
FROM php:8.2-apache

# Installation des dépendances système
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  zip \
  unzip

# Nettoyage du cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Installation des extensions PHP
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Activation du module Apache rewrite
RUN a2enmod rewrite

# Installation de Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Définition du répertoire de travail
WORKDIR /var/www/html

# Copie des fichiers du projet
COPY . .

# Installation des dépendances PHP via Composer
RUN composer install

# Configuration des permissions
RUN chown -R www-data:www-data /var/www/html \
  && chmod -R 755 /var/www/html/storage

# Exposition du port 80
EXPOSE 80

# Démarrage d'Apache en premier plan
CMD ["apache2-foreground"] 
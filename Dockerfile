FROM jenkins/jenkins:lts

USER root

# Installer PHP CLI, extensions, Apache et Docker CLI
RUN apt-get update && \
    apt-get install -y php-cli php-xml php-mbstring php-curl php-zip unzip git curl libzip-dev apt-transport-https ca-certificates gnupg lsb-release apache2 libapache2-mod-php && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    rm -rf /var/lib/apt/lists/*

# Installer Docker CLI
RUN curl -fsSL https://download.docker.com/linux/debian/gpg | gpg --dearmor -o /usr/share/keyrings/docker-archive-keyring.gpg && \
    echo "deb [arch=$(dpkg --print-architecture) signed-by=/usr/share/keyrings/docker-archive-keyring.gpg] https://download.docker.com/linux/debian $(lsb_release -cs) stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null && \
    apt-get update && \
    apt-get install -y docker-ce-cli

# Configurer Apache pour Laravel (mod rewrite)
RUN a2enmod rewrite && \
    sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Définir le répertoire de travail pour Laravel
WORKDIR /var/www/html

# Copier tout le projet Laravel
COPY . .

# Supprimer vendor existant pour éviter les conflits
RUN rm -rf vendor || true

# Installer les dépendances Laravel
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-reqs

# Copier l'exemple de .env et générer la clé
RUN cp .env.example .env && php artisan key:generate

# Donner les droits corrects à Apache
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Exposer les ports Apache et Jenkins
EXPOSE 8080 50000 80

# Commande finale : démarrer Apache et Jenkins
CMD service apache2 start && /usr/bin/jenkins.sh

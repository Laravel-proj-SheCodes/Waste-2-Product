FROM jenkins/jenkins:lts

USER root

# Installer PHP CLI et extensions nécessaires
RUN apt-get update && \
    apt-get install -y php-cli php-xml php-mbstring php-curl php-zip unzip git curl libzip-dev && \
    docker-php-ext-install zip || true && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    rm -rf /var/lib/apt/lists/*

USER jenkins

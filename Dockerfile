FROM jenkins/jenkins:lts
USER root
RUN apt-get update && \
    apt-get install -y docker.io php-cli unzip git curl libzip-dev && \
    docker-php-ext-install zip || true && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
    rm -rf /var/lib/apt/lists/*
USER jenkins

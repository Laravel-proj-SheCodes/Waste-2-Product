FROM jenkins/jenkins:lts-jdk11

# Switch to root user to install packages
USER root

# Installer dépendances et Docker CLI
RUN apt-get update && apt-get install -y \
    git \
    curl \
    ca-certificates \
    unzip \
    && curl -fsSL https://download.docker.com/linux/debian/dists/bullseye/pool/stable/amd64/docker-ce-cli_20.10.24~3-0~debian-bullseye_amd64.deb -o docker-ce-cli.deb \
    && dpkg -i docker-ce-cli.deb \
    && rm docker-ce-cli.deb \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Docker Compose (v2.x)
RUN curl -L "https://github.com/docker/compose/releases/download/v2.28.0/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose \
    && chmod +x /usr/local/bin/docker-compose

# Ajouter jenkins au groupe docker pour pouvoir exécuter Docker sans sudo
# Créer le groupe docker si inexistant et ajouter jenkins à ce groupe
RUN groupadd -g 999 docker || true \
    && usermod -aG docker jenkins

# Repasser à l'utilisateur Jenkins
USER jenkins

# Exposer ports
EXPOSE 8080 50000

# Utiliser l'image officielle de Jenkins LTS
FROM jenkins/jenkins:lts

# Passer en utilisateur root pour installer Docker
USER root

# Installer Docker CLI et Docker Compose (pour que Jenkins puisse lancer des conteneurs)
RUN apt-get update && apt-get install -y \
    docker.io \
    docker-compose \
    git \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Ajouter Jenkins au groupe Docker pour pouvoir exécuter les commandes Docker
RUN usermod -aG docker jenkins

# Revenir à l'utilisateur Jenkins
USER jenkins

# Exposer le port 8080 pour l'interface Jenkins
EXPOSE 8080

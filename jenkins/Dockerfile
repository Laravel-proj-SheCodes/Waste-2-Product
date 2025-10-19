FROM jenkins/jenkins:lts-jdk11

# Switch to root user to install packages
USER root

# Install dependencies and Docker CLI from the official Docker repository
RUN apt-get update && apt-get install -y \
    git \
    curl \
    ca-certificates \
    && curl -fsSL https://download.docker.com/linux/debian/dists/bullseye/pool/stable/amd64/docker-ce-cli_20.10.24~3-0~debian-bullseye_amd64.deb -o docker-ce-cli.deb \
    && dpkg -i docker-ce-cli.deb \
    && rm docker-ce-cli.deb \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Switch back to the Jenkins user
USER jenkins

# Expose Jenkins web interface and agent ports
EXPOSE 8080 50000
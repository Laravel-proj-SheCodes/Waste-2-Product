pipeline {
    agent any

    environment {
        IMAGE_NAME = "mounambr/waste2product-laravel"
        DOCKER_CRED = "DOCKER_CREDENTIALS_ID"
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'DevOps', url: 'https://github.com/Laravel-proj-SheCodes/Waste-2-Product'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    docker.build("${IMAGE_NAME}:latest")
                }
            }
        }

        stage('Run Container') {
            steps {
                script {
                    // Supprime l'ancien conteneur si existe
                    sh "docker rm -f laravel_app || true"
                    sh "docker run -d -p 8088:80 --name laravel_app ${IMAGE_NAME}:latest"
                }
            }
        }

        stage('Artisan Commands') {
            steps {
                sh "docker exec laravel_app php artisan migrate --force"
                sh "docker exec laravel_app php artisan config:cache"
            }
        }

        stage('Push Docker Image') {
            steps {
                withDockerRegistry([credentialsId: "${DOCKER_CRED}", url: "https://index.docker.io/v1/"]) {
                    script {
                        docker.image("${IMAGE_NAME}:latest").push()
                    }
                }
            }
        }
    }

    post {
        always {
            echo "Pipeline finished"
        }
    }
}

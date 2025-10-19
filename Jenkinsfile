pipeline {
    agent any

    environment {
        IMAGE_NAME = "mounambr/waste2product-laravel"
    }

    stages {
        stage('Checkout') {
            steps {
                git 'https://github.com/Laravel-proj-SheCodes/Waste-2-Product.git'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    docker.build("${IMAGE_NAME}:latest")
                }
            }
        }

        stage('Push Docker Image') {
            steps {
                script {
                    docker.withRegistry('https://index.docker.io/v1/', 'DOCKER_CREDENTIALS_ID') {
                        docker.image("${IMAGE_NAME}:latest").push()
                    }
                }
            }
        }
    }
}

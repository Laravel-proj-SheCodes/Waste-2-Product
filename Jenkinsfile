pipeline {
    agent any

    environment {
        APP_NAME = "waste2product"
        DB_HOST = "mysql-laravel"
        DB_DATABASE = "laravel"
        DB_USERNAME = "root"
        DB_PASSWORD = "root"
        DOCKER_CRED = "DOCKER_CREDENTIALS_ID"
        IMAGE_NAME = "waste2product-laravel"
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
    }

    post {
        always {
            echo "Pipeline finished"
        }
    }
}

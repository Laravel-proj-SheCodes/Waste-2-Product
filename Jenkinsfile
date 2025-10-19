pipeline {
    agent any
    stages {
        stage('Build Docker Image') {
            steps {
                script {
                    docker.build("waste2product-laravel:latest")
                }
            }
        }
    }
}

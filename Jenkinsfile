pipeline {
    agent any

    stages {
        stage('Checkout') {
            steps {
                git branch: 'DevOps', url: 'https://github.com/Laravel-proj-SheCodes/Waste-2-Product'
            }
        }

        stage('Build Docker Image') {
            steps {
                script {
                    sh 'docker build -t waste2product-laravel:latest .'
                }
            }
        }

        stage('Run Docker Compose (CI/CD)') {
            steps {
                script {
                    // Stopper d'anciens conteneurs
                    sh '''
                    docker-compose -f docker-compose.cicd.yml down || true
                    docker system prune -f || true
                    '''

                    // Lancer le nouveau setup
                    sh 'docker-compose -f docker-compose.cicd.yml up -d --build'
                }
            }
        }
    }

    post {
        success {
            echo '✅ Pipeline terminé avec succès !'
            echo '🌐 Application disponible sur : http://localhost:8030'
        }
        failure {
            echo '❌ La pipeline a échoué.'
        }
    }
}

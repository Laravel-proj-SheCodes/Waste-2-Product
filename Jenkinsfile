pipeline {
    agent any

    environment {
        MYSQL_PASSWORD = "root"
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
                    sh 'docker build -t waste2product-laravel:latest .'
                }
            }
        }

        stage('Run Docker Compose') {
            steps {
                script {
                    sh '''
                    docker-compose -f docker-compose.cicd.yml down || true
                    docker system prune -f -f || true
                    docker-compose -f docker-compose.cicd.yml up -d --build
                    '''
                }
            }
        }

        stage('Prepare Laravel') {
            steps {
                script {
                    // Installer MySQL client dans le conteneur Laravel
                    sh 'docker exec laravel_cicd bash -c "apt-get update && apt-get install -y default-mysql-client"'

                    // Attendre que MySQL soit prêt
                    sh '''
                    docker exec laravel_cicd bash -c "
                        until mysql -h mysql-cicd -u root -proot --ssl-mode=DISABLED -e 'SELECT 1'; do
                            echo 'Waiting for MySQL...'
                            sleep 3
                        done
                    "
                    '''

                    // Exécuter les migrations
                    sh 'docker exec laravel_cicd bash -c "php artisan migrate --force"'
                }
            }
        }

        stage('Build Frontend') {
            steps {
                script {
                    // Installer Node.js et npm dans le conteneur Laravel
                    sh 'docker exec laravel_cicd bash -c "apt-get update && apt-get install -y nodejs npm"'

                    // Installer dépendances npm et builder le front
                    sh 'docker exec laravel_cicd bash -c "npm install && npm run build"'
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

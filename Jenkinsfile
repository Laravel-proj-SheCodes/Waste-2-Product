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

        stage('Run Docker Container') {
            steps {
                script {
                    // Supprimer l'ancien conteneur s'il existe
                    sh '''
                    if [ $(docker ps -aq -f name=test_laravel) ]; then
                        docker rm -f test_laravel
                    fi
                    '''
                    // Lancer le nouveau conteneur
                    sh 'docker run -d -p 8030:80 --name test_laravel waste2product-laravel:latest'
                }
            }
        }
    }

    post {
        success {
            echo 'Pipeline terminé avec succès ! L\'application tourne sur http://localhost:8030'
        }
        failure {
            echo 'La pipeline a échoué.'
        }
    }
}

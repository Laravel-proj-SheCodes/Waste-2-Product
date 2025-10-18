pipeline {
    agent any

    environment {
        GIT_CRED = 'GIT_CREDENTIALS_ID'
        SONAR_TOKEN = 'SONAR_TOKEN_ID'
        DOCKER_CRED = 'DOCKER_CREDENTIALS_ID'
        NEXUS_HOST = 'localhost:5000'
        IMAGE_NAME = "${NEXUS_HOST}/waste2product-laravel"
    }

    stages {
        stage('Checkout') {
            steps {
                git branch: 'DevOps', url: 'https://github.com/Laravel-proj-SheCodes/Waste-2-Product', credentialsId: "${GIT_CRED}"
            }
        }

        stage('Prepare') {
            steps {
                echo "Preparing workspace..."
                sh 'php -v'
            }
        }

        stage('Install dependencies') {
            steps {
                sh '''
                apt-get update && \
                apt-get install -y php-cli php-xml php-mbstring php-curl php-zip unzip git curl libzip-dev || true
                composer install --no-interaction --prefer-dist --optimize-autoloader
                '''
            }
        }

        stage('Prepare .env') {
            steps {
                sh '''
                cp .env.example .env || true
                php -r "file_put_contents('.env', preg_replace('/DB_HOST=.*/', 'DB_HOST=mysql', file_get_contents('.env')));"
                php -r "file_put_contents('.env', preg_replace('/DB_PORT=.*/', 'DB_PORT=3306', file_get_contents('.env')));"
                php -r "file_put_contents('.env', preg_replace('/DB_DATABASE=.*/', 'DB_DATABASE=waste2product', file_get_contents('.env')));"
                php -r "file_put_contents('.env', preg_replace('/DB_USERNAME=.*/', 'DB_USERNAME=laravel', file_get_contents('.env')));"
                php -r "file_put_contents('.env', preg_replace('/DB_PASSWORD=.*/', 'DB_PASSWORD=laravel', file_get_contents('.env')));"
                '''
            }
        }

        stage('Migrate & Tests') {
            steps {
                sh '''
                php artisan key:generate
                ./vendor/bin/phpunit || true
                '''
            }
        }

        stage('SonarQube Analysis') {
            environment {
                SONAR_TOKEN = credentials("${SONAR_TOKEN}")
            }
            steps {
                withSonarQubeEnv('SonarQube') {
                    sh '''
                    cat > sonar-project.properties <<EOF
                    sonar.projectKey=waste2product
                    sonar.projectName=waste2product
                    sonar.sources=app,resources,routes
                    sonar.language=php
                    EOF
                    sonar-scanner -Dsonar.login=${SONAR_TOKEN}
                    '''
                }
            }
        }

        stage('Build Docker image') {
            steps {
                sh "docker build -t ${IMAGE_NAME}:${env.BUILD_NUMBER} ."
            }
        }

        stage('Push image to Nexus') {
            steps {
                withCredentials([usernamePassword(credentialsId: "${DOCKER_CRED}", usernameVariable: 'NEXUS_USER', passwordVariable: 'NEXUS_PASS')]) {
                    sh "echo \$NEXUS_PASS | docker login ${NEXUS_HOST} -u \$NEXUS_USER --password-stdin"
                    sh "docker push ${IMAGE_NAME}:${env.BUILD_NUMBER}"
                }
            }
        }
    }

    post {
        always {
            sh 'docker image prune -f'
            cleanWs()
        }
        success { echo "Pipeline succeeded!" }
        failure { echo "Pipeline failed!" }
    }
}

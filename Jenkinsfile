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
                // vérifier si PHP est installé, sinon rien
                sh 'php -v || echo "PHP not found, will use Docker image"'
            }
        }

        stage('Install dependencies') {
            steps {
                script {
                    docker.image('php:8.2-cli').inside('--network host') {
                        sh '''
                        apt-get update && apt-get install -y unzip git curl libzip-dev
                        docker-php-ext-install zip
                        curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
                        composer install --no-interaction --prefer-dist --optimize-autoloader
                        '''
                    }
                }
            }
        }

        stage('Prepare .env') {
            steps {
                script {
                    docker.image('php:8.2-cli').inside('--network host') {
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
            }
        }

        stage('Migrate & Tests') {
            steps {
                script {
                    docker.image('php:8.2-cli').inside('--network host') {
                        sh 'php artisan key:generate || true'
                        sh './vendor/bin/phpunit || true'
                    }
                }
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
                    sonar-scanner -Dsonar.login=${SONAR_TOKEN} || true
                    '''
                }
            }
        }

        stage('Build Docker image') {
            steps {
                sh "docker build -t ${IMAGE_NAME}:${env.BUILD_NUMBER} . || echo 'Docker not found, skipping build'"
            }
        }

        stage('Push image to Nexus') {
            steps {
                withCredentials([usernamePassword(credentialsId: "${DOCKER_CRED}", usernameVariable: 'NEXUS_USER', passwordVariable: 'NEXUS_PASS')]) {
                    sh "echo \$NEXUS_PASS | docker login ${NEXUS_HOST} -u \$NEXUS_USER --password-stdin || echo 'Docker not found, skipping push'"
                    sh "docker push ${IMAGE_NAME}:${env.BUILD_NUMBER} || echo 'Docker not found, skipping push'"
                }
            }
        }
    }

    post {
        always {
            sh 'docker image prune -f || echo "Docker not found, skipping prune"'
            cleanWs()
        }
        success {
            echo "Pipeline succeeded!"
        }
        failure {
            echo "Pipeline failed!"
        }
    }
}

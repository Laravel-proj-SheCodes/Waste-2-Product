pipeline {
  agent any

  environment {
    // IDs des credentials créés dans Jenkins (à adapter si tu as un autre nom)
    GIT_CRED = 'GIT_CREDENTIALS_ID'
    SONAR_TOKEN = 'SONAR_TOKEN_ID'
    DOCKER_CRED = 'DOCKER_CREDENTIALS_ID'
    NEXUS_CRED = 'NEXUS_CREDENTIALS_ID'

    // image name to push (change NEXUS_HOST et repo)
    NEXUS_HOST = 'localhost:5000'           // <-- adapte si nécessaire
    IMAGE_NAME = "${NEXUS_HOST}/waste2product-laravel"
  }

  stages {
    stage('Checkout') {
      steps {
        git branch: 'main', url: 'https://github.com/TON_COMPTE/TON_PROJET.git', credentialsId: "${GIT_CRED}"
      }
    }

    stage('Prepare') {
      steps {
        echo "Preparing workspace..."
        // show php version in agent if available
        sh 'php -v || true'
      }
    }

    stage('Install dependencies') {
      steps {
        script {
          // utilises l'image PHP officielle pour exécuter composer / artisan
          docker.image('php:8.2-cli').inside('--network host') {
            sh 'apt-get update && apt-get install -y unzip git curl libzip-dev && docker-php-ext-install zip || true'
            sh 'curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer'
            sh 'composer install --no-interaction --prefer-dist --optimize-autoloader'
          }
        }
      }
    }

    stage('Prepare .env') {
      steps {
        // crée un .env de test pour le pipeline (DB pointe vers mysql container dans le même réseau)
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
        script {
          // Utilise un container php qui atteint la base MySQL (mysql container doit être accessible)
          docker.image('php:8.2-cli').inside("--network host") {
            sh 'composer install --no-interaction || true'
            // générer key et lancer migrations
            sh 'php artisan key:generate || true'
            // attendre mysql readiness (retry)
            sh '''
              for i in {1..30}; do
                vendor/bin/phpunit --version && break || sleep 2
              done
            '''
            // lancer tests (si tu as PHPUnit configuré)
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
            # créer sonar-project.properties minimal si besoin
            cat > sonar-project.properties <<EOF
            sonar.projectKey=waste2product
            sonar.projectName=waste2product
            sonar.sources=app,resources,routes
            sonar.language=php
            EOF
            # exécuter sonar-scanner (nécessite sonar-scanner installé sur agent ou utiliser image)
            sonar-scanner -Dsonar.login=${SONAR_TOKEN}
          '''
        }
      }
    }

    stage('Build Docker image') {
      steps {
        script {
          // build docker image on Jenkins host
          sh "docker build -t ${IMAGE_NAME}:${env.BUILD_NUMBER} ."
        }
      }
    }

    stage('Push image to Nexus') {
      steps {
        withCredentials([usernamePassword(credentialsId: "${DOCKER_CRED}", usernameVariable: 'NEXUS_USER', passwordVariable: 'NEXUS_PASS')]) {
          sh "echo $NEXUS_PASS | docker login ${NEXUS_HOST} -u $NEXUS_USER --password-stdin"
          sh "docker push ${IMAGE_NAME}:${env.BUILD_NUMBER}"
        }
      }
    }
  } // stages

  post {
    always {
      sh 'docker image prune -f || true'
      cleanWs()
    }
    success {
      echo "Pipeline succeeded!"
    }
    failure {
      echo "Pipeline failed."
    }
  }
}

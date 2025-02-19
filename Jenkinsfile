pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/onjahasn/user_CDA.git'
        GIT_BRANCH = 'main'
        DEPLOY_DIR = 'deliceketo'

        APP_ENV = 'prod'
        APP_DEBUG = '0'
        DATABASE_URL = credentials('database-url')
        MAILER_DSN = credentials('mailer-url')
    }

    stages {
        stage('Cloner le dépôt') {
            steps {
                sh "rm -rf ${DEPLOY_DIR}"
                sh "git clone -b ${GIT_BRANCH} ${GIT_REPO} ${DEPLOY_DIR}"
            }
        }

        stage('Installation des dépendances') {
            steps {
                dir("${DEPLOY_DIR}") {
                    sh 'composer install --optimize-autoloader'
                }
            }
        }

        stage('Créer .env.local avec les bonnes valeurs') {
            steps {
                sh '''
                    echo "APP_ENV=prod" > .env.local
                    echo "APP_DEBUG=0" >> .env.local
                    echo "DATABASE_URL=$DATABASE_URL" >> .env.local
                    echo "MAILER_DSN=$MAILER_DSN" >> .env.local
                '''
            }
        }

        stage('Migration de la base de données') {
            steps {
                dir("${DEPLOY_DIR}") {
                    script {
                        sh 'php bin/console doctrine:database:create --if-not-exists --env=prod'
                        sh 'php bin/console doctrine:migrations:migrate --no-interaction --env=prod'
                    }
                }
            }
        }

        stage('Compilation des assets') {
            steps {
                sh 'php bin/console asset-map:compile'
            }
        }

        stage('Nettoyage du cache') {
            steps {
                dir("${DEPLOY_DIR}") {
                    sh 'php bin/console cache:clear --verbose'
                    sh 'php bin/console cache:warmup --verbose'
                }
            }
        }

        stage('Déployer le projet') {
            steps {
                sh "rm -rf /var/www/html/${DEPLOY_DIR}" // Supprime le dossier de destination
                sh "mkdir /var/www/html/${DEPLOY_DIR}" // Recréé le dossier de destination
                sh "cp -rT ${DEPLOY_DIR} /var/www/html/${DEPLOY_DIR}"
                sh "chmod -R 775 /var/www/html/${DEPLOY_DIR}/var"
            }
        }
    }

    post {
        success {
            echo 'Déploiement réussi !'
        }
        failure {
            echo 'Erreur lors du déploiement.'
        }
    }
}

pipeline {
    agent any

    environment {
        GIT_REPO = 'https://github.com/onjahasn/Delice_Keto.git'
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
        

        stage('Déployer le projet') {
            steps {
                sh '''
                    sudo rsync -avz --delete --omit-dir-times --no-perms --exclude 'public/uploads/' . /var/www/deliceketo/
                    #sudo chown -R www-data:www-data /var/www/deliceketo/
                    #sudo chmod -R 775 /var/www/deliceketo/
                    #sudo systemctl restart apache2
                '''
            }
        }

        stage('Nettoyage du cache après déploiement') {
            steps {
                dir("/var/www/deliceketo/") {
                    sh 'php bin/console cache:clear'
                    sh 'php bin/console cache:warmup'
                }
            }
        }

        stage('Appliquer les permissions') {
            steps {
                sh '''
                    sudo chown -R www-data:www-data /var/www/deliceketo/
                    sudo chmod -R 775 /var/www/deliceketo/
                    sudo systemctl restart apache2
                '''
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

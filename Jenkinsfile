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

    // Étape 1 : Cloner le dépôt Git dans le répertoire défini
    // Suppression de l'ancien répertoire pour éviter les conflits
    stages {
        stage('Cloner le dépôt') {
            steps {
                sh "rm -rf ${DEPLOY_DIR}"
                sh "git clone -b ${GIT_BRANCH} ${GIT_REPO} ${DEPLOY_DIR}"
            }
        }

        // Étape 2 : Installer les dépendances PHP
        stage('Installation des dépendances') {
            steps {
                dir("${DEPLOY_DIR}") {
                    sh 'composer install --optimize-autoloader'
                }
            }
        }

        // Étape 3 : Configuration de l'environnement
        // Création du fichier .env.local avec les bonnes valeurs
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

        // Étape 4 : Migration de la base de données
        // Création de la base si elle n'existe pas et application des migrations
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

        // Étape 5 : Compilation des assets du projet
        stage('Compilation des assets') {
            steps {
                sh 'php bin/console asset-map:compile'
            }
        }

        stage('Temporisation 1') {
            steps {
                sh 'echo "Attente avant le cache clear..."'
                sh 'sleep 10' // Attendre 10 secondes (ajuste selon le besoin)
            }
        }

        // Étape 6 : Nettoyage et optimisation du cache Symfony
        stage('Nettoyage du cache') {
            steps {
                dir("${DEPLOY_DIR}") {
                    sh 'php bin/console cache:clear --verbose'
                    sh 'php bin/console cache:warmup --verbose'
                }
            }
        }

        stage('Temporisation 2') {
            steps {
                sh 'echo "Attente apres le cache clear..."'
                sh 'sleep 10' // Attendre 10 secondes (ajuste selon le besoin)
            }
        }

        // Étape 7 : Déploiement du projet
        // Synchronisation des fichiers avec le serveur, configuration des permissions et redémarrage d'Apache
        stage('Déployer le projet') {
            steps {
                sh '''
                    sudo rsync -avz --delete --omit-dir-times --no-perms --exclude 'public/uploads/' . /var/www/deliceketo/
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

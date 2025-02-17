pipeline {
    agent any

    environment {
        APP_ENV = 'prod'  // 🔥 Définit Symfony en mode production
        APP_DEBUG = '0'
        DATABASE_URL = credentials('database-url')  // Injecte la base de données
        MAILER_DSN = credentials('mailer-url')  // Injecte le Mailer DSN
    }

    stages {
        stage('Cloner le dépôt') {
            steps {
                git branch: 'main', url: 'https://github.com/onjahasn/user_CDA.git'
            }
        }

        stage('Installer les dépendances Symfony') {
            steps {
                sh '''
                    composer install --no-dev --optimize-autoloader
                '''
            }
        }

        stage('Créer .env.local avec les bonnes valeurs') {
            steps {
                sh '''
                    echo "APP_ENV=$APP_ENV" > .env.local
                    echo "APP_DEBUG=$APP_DEBUG" >> .env.local
                    echo "DATABASE_URL=$DATABASE_URL" >> .env.local
                    echo "MAILER_DSN=$MAILER_DSN" >> .env.local
                '''
            }
        }

        stage('Effacer et réchauffer le cache Symfony') {
            steps {
                sh '''
                    rm -rf var/cache/*
                    php bin/console cache:clear --env=prod --no-debug
                    php bin/console cache:warmup --env=prod
                '''
            }
        }

        stage('Déployer le projet') {
            steps {
                sh '''
                    rsync -avz --delete . /var/www/deliceketo/
                    chown -R www-data:www-data /var/www/deliceketo/
                '''
            }
        }

        stage('Redémarrer Apache') {
            steps {
                sh '''
                    sudo systemctl restart apache2
                '''
            }
        }
    }
}

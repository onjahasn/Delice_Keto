pipeline {
    agent any

    environment {
        APP_ENV = 'prod'
        APP_DEBUG = '0'
        DATABASE_URL = credentials('database-url')
        MAILER_DSN = credentials('mailer-url')
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
                    echo "APP_ENV=prod" > .env.local
                    echo "APP_DEBUG=0" >> .env.local
                    echo "DATABASE_URL=$DATABASE_URL" >> .env.local
                    echo "MAILER_DSN=$MAILER_DSN" >> .env.local
                '''
            }
        }

        stage('Générer les assets Webpack Encore') {
            steps {
                sh '''
                    if [ -f package.json ]; then
                        npm install
                        npm run build
                    else
                        echo "Pas de package.json, étape ignorée"
                    fi
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
                    sudo rsync -avz --delete --omit-dir-times --no-perms . /var/www/deliceketo/
                    sudo chown -R www-data:www-data /var/www/deliceketo/
                    sudo chmod -R 775 /var/www/deliceketo/
                    sudo systemctl restart apache2
                '''
            }
        }
    }
}

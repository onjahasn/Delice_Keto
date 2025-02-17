pipeline {
    agent any

    environment {
        DATABASE_URL = credentials('database-url')  // Injecte la base de données
        MAILER_DSN = credentials('mailer-url')  // Injecte le Mailer DSN depuis Jenkins
    }

    stages {
        stage('Cloner le dépôt') {
            steps {
                git branch: 'main', url: 'git@github.com:onjahasn/user_CDA.git'
            }
        }

        stage('Installer les dépendances Symfony') {
            steps {
                sh '''
                    composer install --no-dev --optimize-autoloader
                '''
            }
        }

        stage('Créer .env.local') {
            steps {
                sh '''
                    touch .env.local
                    echo "DATABASE_URL=$DATABASE_URL" > .env.local
                    echo "MAILER_DSN=$MAILER_DSN" >> .env.local
                '''
            }
        }

        stage('Préparer la base de test') {
            steps {
                sh '''
                    php bin/console doctrine:database:create --env=test || true
                    php bin/console doctrine:schema:update --force --env=test
                '''
            }
        }

        stage('Exécuter les tests unitaires') {
            steps {
                sh '''
                    php bin/phpunit --testdox
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

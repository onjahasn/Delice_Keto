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
                sh "rm -rf ${DEPLOY_DIR}" // Nettoyage du précédent build
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

        stage('Configuration de l\'environnement') {
            steps {
                script {
                    def envLocal = """
                    APP_ENV=${APP_ENV}
                    APP_DEBUG=${APP_DEBUG}
                    DATABASE_URL=${DATABASE_URL}
                    MAILER_DSN=${MAILER_DSN}
                    """.stripIndent()
                    
                    writeFile file: '.env.local', text: envLocal
                }
            }
        }

        stage('Migration de la base de données') {
            steps {
                dir("${DEPLOY_DIR}") {
                    script {
                        // Création de la base si elle n'existe pas
                        sh 'php bin/console doctrine:database:create --if-not-exists --env=prod'
                        
                        // Exécution des migrations
                        sh 'php bin/console doctrine:migrations:migrate --no-interaction --env=prod'
                    }
                }
            }
        }

        stage('Nettoyage du cache') {
            steps {
                dir("${DEPLOY_DIR}") {
                    sh 'php bin/console cache:clear --env=prod'
                    sh 'php bin/console cache:warmup'
                }
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

    post {
        success {
            echo 'Déploiement réussi !'
        }
        failure {
            echo 'Erreur lors du déploiement.'
        }
    }
}



// pipeline {
//     agent any

//     environment {
//         APP_ENV = 'prod'
//         APP_DEBUG = '0'
//         DATABASE_URL = credentials('database-url')
//         MAILER_DSN = credentials('mailer-url')
//     }

//     stages {
//         stage('Cloner le dépôt') {
//             steps {
//                 git branch: 'main', url: 'https://github.com/onjahasn/user_CDA.git'
//             }
//         }

//         stage('Installer les dépendances Symfony') {
//             steps {
//                 sh '''
//                     composer install --no-dev --optimize-autoloader
//                 '''
//             }
//         }

//         stage('Créer .env.local avec les bonnes valeurs') {
//             steps {
//                 sh '''
//                     echo "APP_ENV=prod" > .env.local
//                     echo "APP_DEBUG=0" >> .env.local
//                     echo "DATABASE_URL=$DATABASE_URL" >> .env.local
//                     echo "MAILER_DSN=$MAILER_DSN" >> .env.local
//                 '''
//             }
//         }

//         stage('Générer les assets Webpack Encore') {
//             steps {
//                 sh '''
//                     if [ -f package.json ]; then
//                         npm install
//                         npm run build
//                     else
//                         echo "Pas de package.json, étape ignorée"
//                     fi
//                 '''
//             }
//         }

//         stage('Effacer et réchauffer le cache Symfony') {
//             steps {
//                 sh '''
//                     rm -rf var/cache/*
//                     php bin/console cache:clear --env=prod --no-debug
//                     php bin/console cache:warmup --env=prod
//                 '''
//             }
//         }

//         stage('Préparer la base de test') {
//             steps {
//                 sh '''
//                     php bin/console doctrine:database:create --env=test || true
//                     php bin/console doctrine:schema:update --force --env=test
//                 '''
//             }
//         }

//         stage('Exécuter les tests unitaires') {
//             steps {
//                 sh '''
//                     php bin/phpunit --testdox
//                 '''
//             }
//         }

        // stage('Déployer le projet') {
        //     steps {
        //         sh '''
        //             sudo rsync -avz --delete --omit-dir-times --no-perms . /var/www/deliceketo/
        //             sudo chown -R www-data:www-data /var/www/deliceketo/
        //             sudo chmod -R 775 /var/www/deliceketo/
        //             sudo systemctl restart apache2
        //         '''
        //     }
        // }
//     }
// }


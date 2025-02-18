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
                git branch: "${GIT_BRANCH}", url: "${GIT_REPO}"
            }
        }

        stage('Installation des dépendances') {
            steps {
                sh '''
                    composer install --no-dev --optimize-autoloader
                '''
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

        stage('Migration de la base de données') {
            steps {
                script {
                    def migration_status = sh(script: 'php bin/console doctrine:migrations:status --env=prod | grep "New migrations" || true', returnStatus: true)
                    if (migration_status == 0) {
                        echo 'Nouvelles migrations détectées. Exécution...'
                        sh 'php bin/console doctrine:migrations:migrate --no-interaction --env=prod'
                    } else {
                        echo 'Aucune nouvelle migration à appliquer.'
                    }
                }
            }
        }

        stage('Nettoyage et optimisation du cache') {
            steps {
                sh '''
                    php bin/console cache:clear --env=prod --no-debug
                    php bin/console cache:warmup --env=prod
                '''
            }
        }

        // stage('Préparer la base de test') {
        //     steps {
        //         sh '''
        //             php bin/console doctrine:database:create --env=test || true
        //             php bin/console doctrine:schema:update --force --env=test
        //         '''
        //     }
        // }

        // stage('Exécuter les tests unitaires') {
        //     steps {
        //         sh '''
        //             php bin/phpunit --testdox
        //         '''
        //     }
        // }

        stage('Déploiement') {
            steps {
                sh '''
                    sudo rsync -avz --delete --omit-dir-times --no-perms . /var/www/${DEPLOY_DIR}/
                    sudo chown -R www-data:www-data /var/www/${DEPLOY_DIR}/
                    sudo chmod -R 775 /var/www/${DEPLOY_DIR}/
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

//         stage('Déployer le projet') {
//             steps {
//                 sh '''
//                     sudo rsync -avz --delete --omit-dir-times --no-perms . /var/www/deliceketo/
//                     sudo chown -R www-data:www-data /var/www/deliceketo/
//                     sudo chmod -R 775 /var/www/deliceketo/
//                     sudo systemctl restart apache2
//                 '''
//             }
//         }
//     }
// }

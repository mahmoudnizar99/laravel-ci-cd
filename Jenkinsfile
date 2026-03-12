pipeline {
    agent any
    environment {
        HERD_PHP = '"C:\\Users\\Mahmoud Nizar\\.config\\herd\\bin\\php83\\php.exe"'
        PATH = "${env.PATH}"
        CACHE_DRIVER = "file"
    }
    stages {

        stage('Clone Repository') {
            steps {
                git branch: 'dev', url: 'https://github.com/mahmoudnizar99/laravel-ci-cd.git'
            }
        }

        stage('Install PHP Dependencies') {
            steps {
                bat 'composer install --no-interaction --prefer-dist --no-progress'
            }
        }

        stage('Install Node Dependencies') {
            steps {
                bat 'npm install'
            }
        }

        stage('Build Frontend') {
            steps {
                bat 'npm run build'
            }
        }

        stage('Prepare Laravel Environment') {
            steps {
                bat 'if not exist .env copy .env.example .env'
                bat 'php artisan key:generate'
            }
        }

        stage('Clear Laravel Cache') {
            steps {
                bat 'touch database/database.sqlite'
                bat 'php artisan migrate --force'
                bat 'php artisan cache:clear'
                bat 'php artisan config:clear'
                bat 'php artisan route:clear'
                bat 'php artisan view:clear'
            }
        }

    }
}

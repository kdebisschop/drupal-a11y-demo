pipeline {

  agent { 'any'}

  options {
    ansiColor('xterm')
  }

  stages {

    stage('Build:') {
      sh """
        composer install
        vendor/bin/drush -y site:install standard --no-interaction --db-url sqlite://var/tmp/demo.sqlite \
        --site-name "NJ Meetup" --site-mail admin@example.com \
        --account-name admin --account-mail admin@example.com --account-pass admin
      """
    }

    stage ('Scan:') {
    }

    stage('Test:') {
      steps {
        sh """
          cd web
          php -S localhost:8089 .ht.router.php &
          cd ..

        """
      }
    }

  }

}

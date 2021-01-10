pipeline {

  options {
    ansiColor('xterm')
  }

  stages {

    stage('Build:') {
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

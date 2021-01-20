pipeline {

  agent any

  stages {

    stage('Build:') {
      steps {
        script {
          baseImage = docker.build('drupa11y:latest', '.')
        }
      }
      post { always { sh 'mkdir ci' } }
    }

    stage('Test:') {
      steps {
        script {
          baseImage.withRun() { c ->
            baseImage.inside("--link ${c.id}:drupa11y.test") {
              sh '''
                cd /var/www/web
                drush -n --yes site:install
                drush pm:enable devel devel_generate
                drush devel-generate:content 10 --bundles=article
                drush devel-generate:content 10 --bundles=page
              '''
            }
            docker.image('florenttorregrosa/pa11y-ci').inside("--link ${c.id}:drupa11y.test -v ${env.WORKSPACE}:/workspace") {
              sh 'pa11y-ci --sitemap https://drupa11y.test/drupa11y-map.xml --config /workspace/.pa11yci'
            }
          }
        }
        sh 'pa11y-ci-reporter-html --source <file> --destination <directory>'
      }
    }

  }

}

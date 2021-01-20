pipeline {

  agent any

  stages {

    stage('Build:') {
      steps {
        script {
          baseImage = docker.build('drupa11y:latest', '.')
        }
        sh """
          composer install --ignore-platform-reqs --prefer-dist --no-progress --no-suggest
          cd web
          ../vendor/bin/drush -n --yes site:install
          ../vendor/bin/drush pm:enable devel devel_generate
          ../vendor/bin/drush devel-generate:content 10 --bundles=article
          ../vendor/bin/drush devel-generate:content 10 --bundles=page
        """
      }
      post { always { sh 'mkdir ci' } }
    }

    stage('Test:') {
      steps {
        script {
          baseImage.withRun() { c ->
            baseImage.inside("--link ${c.id}:drupa11y.test") {
              sh 'cd web ; php -S 0.0.0.0:80 .ht.router.php & ; sleep 1'
            }
            docker.image('florenttorregrosa/pa11y-ci').inside("--link ${c.id}:drupa11y.test -v ${env.WORKSPACE}:/workspace") {
              docker-compose run pa11y_ci /bin/sh -c "pa11y-ci --sitemap https://drupa11y.test/drupa11y-map.xml --config /workspace/.pa11yci"
            }
          }
        }
      }
    }

  }

}

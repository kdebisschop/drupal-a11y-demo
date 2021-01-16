pipeline {

  agent { 'any'}

  options {
    ansiColor('xterm')
  }

  stages {

    stage('Build:') {
      sh """
        composer install --ignore-platform-reqs --prefer-dist --no-progress --no-suggest
        cd web
        ../vendor/bin/drush -n --yes site:install
        ../vendor/bin/drush pm:enable devel devel_generate
        ../vendor/bin/drush devel-generate:content 10 --bundles=article
        ../vendor/bin/drush devel-generate:content 10 --bundles=page
      """
    }

    stage ('Scan:') {
    }

    stage('Test:') {
      steps {
        sh """
          cd web
          php -S 127.0.0.1:8099 .ht.router.php 2> /dev/null &
          php ../scripts/spider.php > map-source.xml
          ../node_modules/.bin/pa11y-ci --sitemap http://127.0.0.1:8099/map-source.xml || echo "Errors Found!"
          head -n 2 map-source.xml > map.xml
          printf "  <url><loc>%s</url></loc>\n" "$(../vendor/bin/drush --uri=http://127.0.0.1:8099/ --no-browser uli /)" >> map.xml
          grep '^ ' map-source.xml >> map.xml
          echo '</urlset>' >> map.xml
          ../node_modules/.bin/pa11y-ci --sitemap http://127.0.0.1:8099/map.xml || echo "Errors Found!"
        """
      }
    }

  }

}

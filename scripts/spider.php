<?php

/**
 * @file
 * Creates a sitemap for the site.
 *
 * If crawl need to be authenticated, add code like this to credentials.inc:
 *
 * ```
 * if ($_SERVER['REMOTE_ADDR'] === '127.0.0.1') {
 *   $conf['session_inc'] = './includes/pa11y-session.inc';
 *   define('PA11Y_SESSION_INC_SESSION_ID', 'VyxK4FU6uN5Ivr4kAWjAc56sXS3z2u9PzHZ,QmTbjvlyLVXcFjOrbGoQHFKx39jH');
 *   define('PA11Y_SESSION_INC_USER_ID', 1215);
 * }
 * ```
 *
 * A handy way to generate a session ID is
 *
 * ```
 * openssl rand -base64 48 | tr /+= ,-
 * ```
 */

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\Crawler;
use Spatie\Crawler\CrawlInternalUrls;
use Spatie\Crawler\CrawlObserver;
use Spatie\Crawler\CrawlProfile;
use Spatie\Crawler\CrawlUrl;

$url = 'http://localhost:8099';


class RaftCrawlObserver extends CrawlObserver
{
  /**
   * Called when the crawler has crawled the given url successfully.
   *
   * @param \Psr\Http\Message\UriInterface $url
   * @param \Psr\Http\Message\ResponseInterface $response
   * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
   */
  public function crawled(
    UriInterface $url,
    ResponseInterface $response,
    ?UriInterface $foundOnUrl = null
  ): void {
    $location = "{$url->getScheme()}://{$url->getHost()}:{$url->getPort()}{$url->getPath()}";
    if ($url->getQuery()) {
      $location .= "?{$url->getQuery()}";
    }
    echo "<url><loc>$location</loc></url>\n";
  }


  /**
   * Called when the crawler had a problem crawling the given url.
   *
   * @param \Psr\Http\Message\UriInterface $url
   * @param \GuzzleHttp\Exception\RequestException $requestException
   * @param \Psr\Http\Message\UriInterface|null $foundOnUrl
   */
  public function crawlFailed(
    UriInterface $url,
    RequestException $requestException,
    ?UriInterface $foundOnUrl = null
  ): void {
  }
}

$spec  = [
  'concurrency:',
  'depth:',
  'help',
  'url:',
];
$options = getopt('h', $spec);

if (isset($options['h']) || isset($options['help'])) {
  echo "sitemap.php - generate local sitemap.

  --concurrency: Set number of crawlers (default: 4).
  --depth: Sets crawl depth (default: 1).
  --url: set base of scan (default: $url)
";
  exit(0);
}

echo <<<XML
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
   xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">

XML;

Crawler::create([RequestOptions::ALLOW_REDIRECTS => false])
  ->setCrawlProfile(new CrawlInternalUrls($url))
  ->ignoreRobots()
  ->setUserAgent('Mozilla/5.0 (Spatie Crawler)')
  ->setCrawlObserver(new RaftCrawlObserver())
  ->setConcurrency($options['concurrency'] ?? 1)
  ->setMaximumDepth($options['depth'] ?? 20)
  ->startCrawling($options['url'] ?? $url);

echo "\n</urlset>\n";

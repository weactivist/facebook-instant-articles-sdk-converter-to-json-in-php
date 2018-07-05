<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Facebook\InstantArticles\JSON\JSONArticle;

// Load instant article file into string
$instant_article_string = file_get_contents(__DIR__.'/instant-article-example.html');

// Converts it into JSON
$json_string =
  JSONArticle::create($instant_article_string)->render();

print_r($json_string);

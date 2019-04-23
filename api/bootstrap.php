<?php

use GraphAware\Neo4j\OGM\EntityManager;
use Dotenv\Dotenv;

require_once('vendor/autoload.php');

$dotenv = Dotenv::create($_SERVER["DOCUMENT_ROOT"]);
$dotenv->load();

$entityManager = EntityManager::create(getenv('NEO4J_CONNECT_STRING'));
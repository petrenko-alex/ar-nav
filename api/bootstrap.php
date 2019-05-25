<?php

use GraphAware\Neo4j\OGM\EntityManager;
use Dotenv\Dotenv;

require_once('vendor/autoload.php');

error_reporting(E_ALL ^ E_NOTICE);

if (!$_SERVER["DOCUMENT_ROOT"]) {
	$_SERVER["DOCUMENT_ROOT"] = realpath(__DIR__ . '/../');
}

$dotenv = Dotenv::create($_SERVER["DOCUMENT_ROOT"]);
$dotenv->load();

$entityManager = EntityManager::create(getenv('NEO4J_CONNECT_STRING'));
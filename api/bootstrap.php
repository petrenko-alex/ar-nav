<?php
use GraphAware\Neo4j\OGM\EntityManager;

require_once('vendor/autoload.php');

$entityManager = EntityManager::create('http://neo4j:123456@localhost:7474');

<?php

use Petrenko\ArNav\Model\Place;
use GraphAware\Neo4j\OGM\EntityManager;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class GetAllPlaces
{
    public static function run(EntityManager $entityManager)
    {
        $placesRepo = $entityManager->getRepository(Place::class);
        $places = $placesRepo->findAll();

        $places = array_map(function ($place) {
            /** @var Place $place */
            return $place->toArray();
        }, $places);

        $jsonPlaces = json_encode($places, JSON_UNESCAPED_UNICODE);

        header('Access-Control-Allow-Origin: *');
        echo $jsonPlaces;
    }
}

GetAllPlaces::run($entityManager);
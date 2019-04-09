<?php

use Petrenko\ArNav\Model\PlaceObject;
use GraphAware\Neo4j\OGM\EntityManager;
use Petrenko\ArNav\Model\Place;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class GetPlaceObjects
{
    public static function run(EntityManager $entityManager)
    {
        $placeId = $_GET['placeId'];

        $placesRepo = $entityManager->getRepository(Place::class);
        /** @var Place place */
        $place = $placesRepo->find($placeId);
        $placeObjects = $place->getPlaceObjects();

        $arPlaceObjects = [];
        foreach	($placeObjects as $placeObject) {
            /** @var PlaceObject $placeObject */
            $arPlaceObjects[$placeObject->getId()] = $placeObject->toArray();
        }

        $jsonPlaceObjects = json_encode($arPlaceObjects, JSON_UNESCAPED_UNICODE);

        header('Access-Control-Allow-Origin: *');
        echo $jsonPlaceObjects;
    }
}

GetPlaceObjects::run($entityManager);
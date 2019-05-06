<?php

use Petrenko\ArNav\Model\Place;
use Petrenko\ArNav\Model\Marker;
use Petrenko\ArNav\Model\PlaceObject;
use GraphAware\Neo4j\OGM\EntityManager;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class GetPlaceMarkers
{
    public static function run(EntityManager $entityManager)
    {
        $arPlaceMarkers = [];
        $placeId = $_GET['placeId'];

        $placesRepo = $entityManager->getRepository(Place::class);
        /** @var Place place */
        $place = $placesRepo->find($placeId);

        $placeObjects = $place->getPlaceObjects();
        foreach	($placeObjects as $placeObject) {
            /** @var PlaceObject $placeObject */

            $placeObjectMarkers = $placeObject->getMarkers()->toArray();
            foreach ($placeObjectMarkers as $placeObjectMarker) {
                /** @var Marker $placeObjectMarker */

                $arPlaceMarkers[$placeObjectMarker->getId()] = $placeObjectMarker->toArray();
            }
        }

        $jsonPlaceMarkers = json_encode($arPlaceMarkers, JSON_UNESCAPED_UNICODE);

        header('Access-Control-Allow-Origin: *');
        echo $jsonPlaceMarkers;
    }
}

GetPlaceMarkers::run($entityManager);
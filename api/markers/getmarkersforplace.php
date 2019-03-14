<?php

use Petrenko\ArNav\Model\Marker;
use GraphAware\Neo4j\OGM\EntityManager;
use Petrenko\ArNav\Model\Place;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class GetMarkersForPlace
{
    public static function run(EntityManager $entityManager)
    {
		$placeId = $_GET['placeId'];

    	$placesRepo = $entityManager->getRepository(Place::class);
		/** @var Place place */
		$place = $placesRepo->find($placeId);
     	$placeMarkers = $place->getMarkers();

		$arPlaceMarkers = [];
		foreach	($placeMarkers as $placeMarker) {
			/** @var Marker $placeMarker */
			$arPlaceMarker = $placeMarker->toArray();
        	$arPlaceMarkers[$arPlaceMarker['id']] = [
        		'id' => $arPlaceMarker['id'],
        		'info' => $arPlaceMarker['info'],
			];
		}

        $jsonMarkers = json_encode($arPlaceMarkers, JSON_UNESCAPED_UNICODE);

		header('Access-Control-Allow-Origin: *');
        echo $jsonMarkers;
	}
}

GetMarkersForPlace::run($entityManager);
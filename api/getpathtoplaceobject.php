<?php

use GraphAware\Neo4j\OGM\EntityManager;
use Petrenko\ArNav\Model\Marker;
use Petrenko\ArNav\Model\Place;
use Petrenko\ArNav\Model\PlaceObject;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class GetPathToPlaceObject
{
	/**
	 * @var EntityManager $entityManager
	 */
	private static $entityManager;

	public static function run(EntityManager $entityManager)
	{
		static::$entityManager = $entityManager;

		$placeId = $_GET['placeId'];
		$startMarkerId = $_GET['startMarkerId'];
		$targetPlaceObjectId = $_GET['targetPlaceObjectId'];

		$place = static::getPlace($placeId);
		if ($place) {
			$targetPlaceObject = static::getPlaceObjectFromPlace($place, $targetPlaceObjectId);
			if ($targetPlaceObject) {
				$primaryMarker = static::getPrimaryMarkerForPlaceObject($targetPlaceObject);
				if ($primaryMarker) {
					echo '<pre>' . var_dump($primaryMarker->getTitle()) . '</pre>';
					// Выполнить запрос на получение кратчайшего пути
					try {
						$query = static::$entityManager->createQuery(
							"MATCH (start:Marker), (end:Marker) " .
							"WHERE start.title={startMarker} AND end.title={endMarker} " .
							"CALL algo.shortestPath.stream(start, end, null, {nodeQuery:'Marker', direction:'OUTGOING'}) " .
							"YIELD nodeId, cost " .
							"RETURN algo.asNode(nodeId), cost 	"
						);
						$query->addEntityMapping('start', Marker::class);
						$query->addEntityMapping('end', Marker::class);
						//$query->setParameter('startMarker', 'm_908');
						$query->setParameter('endMarker', $primaryMarker->getTitle());
						$result = $query->execute();

					} catch (Exception $e) {
						echo $e->getMessage();
						return;
					}
					// TODO: get start marker title
					// TODO: need flush in finally?

				}
			}
		}


		// Форматировать и вернуть результат

		$jsonResult = json_encode(array(), JSON_UNESCAPED_UNICODE);

		header('Access-Control-Allow-Origin: *');
		echo $jsonResult;
	}

	/**
	 * @param $placeId
	 * @return null|object|Place
	 */
	protected static function getPlace($placeId)
	{
		$placesRepo = static::$entityManager->getRepository(Place::class);
		return $placesRepo->find($placeId);
	}

	/**
	 * @param Place $place
	 * @param int $placeObjectId
	 * @return PlaceObject|null
	 */
	protected static function getPlaceObjectFromPlace(Place $place, $placeObjectId)
	{
		$placeObjects = $place->getPlaceObjects();
		if ($placeObjects) {
			return $placeObjects->filter(function ($placeObject) use ($placeObjectId) {
				/**
				 * @var PlaceObject $placeObject
				 */
				return $placeObject->getId() == $placeObjectId;
			})->first();
		}
		return null;
	}

	/**
	 * @param PlaceObject $placeObject
	 * @return Marker|null
	 */
	protected static function getPrimaryMarkerForPlaceObject(PlaceObject $placeObject)
	{
		$markers = $placeObject->getMarkers();
		if ($markers) {
			return $markers->filter(function ($marker) {
				/**
				 * @var Marker $marker
				 */
				return $marker->getPrimary() === true;
			})->first();
		}
		return null;
	}
}

GetPathToPlaceObject::run($entityManager);
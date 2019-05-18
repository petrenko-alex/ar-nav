<?php

use GraphAware\Neo4j\Client\Formatter\Type\Relationship;
use GraphAware\Neo4j\OGM\EntityManager;
use Petrenko\ArNav\Model\Marker;
use Petrenko\ArNav\Model\Place;
use Petrenko\ArNav\Model\PlaceObject;
use GraphAware\Neo4j\Client\Formatter\Type\Node;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class GetPathToPlaceObject
{
	/**
	 * @var EntityManager $entityManager
	 */
	private static $entityManager;

	public static function run(EntityManager $entityManager)
	{
		header('Access-Control-Allow-Origin: *');
		$resultPath = array();
		static::$entityManager = $entityManager;

		// Get and validate input data
		$placeId = $_GET['placeId'];
		$startMarkerId = $_GET['startMarkerId'];
		$targetPlaceObjectId = $_GET['targetPlaceObjectId'];
		if (!$placeId || !$startMarkerId || !$targetPlaceObjectId) {
			echo static::getError('Not enough data for request');
		}

		// Get start Marker
		$startMarker = static::getMarker($startMarkerId);
		if (!$startMarker) {
			echo static::getError('Start Marker not found for id=' . $startMarkerId);
		}

		// Get Place
		$place = static::getPlace($placeId);
		if (!$place) {
			echo static::getError('Place not found for id=' . $placeId);
		}

		// Find target PlaceObject
		$targetPlaceObject = static::getPlaceObjectFromPlace($place, $targetPlaceObjectId);
		if (!$targetPlaceObject) {
			echo static::getError('PlaceObject not found for id=' . $targetPlaceObjectId);
		}

		// Get primary Marker for PlaceObject
		$primaryMarker = static::getPrimaryMarkerForPlaceObject($targetPlaceObject);
		if (!$primaryMarker) {
			echo static::getError('Primary Marker not found for PlaceObject with id=' . $targetPlaceObjectId);
		}

		// Get path
		try {
			$path = static::getPath($startMarker, $primaryMarker);

			// Build result array
			$pathLength = count($path);
			for($i = 0; $i < ($pathLength - 1); $i++) {
				/**
				 * @var Node $firstMarker
				 */
				$firstMarker = $path[$i]['node'];
				/**
				 * @var Node $secondMarker
				 */
				$secondMarker = $path[$i + 1]['node'];

				$rel = static::getMarkersRelationship($firstMarker->identity(), $secondMarker->identity());
				if ($rel) {
					$key = 'm_' . $firstMarker->identity();
					$resultPath[$key] = static::getResultArrayItem($firstMarker, $secondMarker, $rel);
					if ($i == 0) {
						$resultPath[$key]['pathStart'] = true;
					}
				}
			}
			/**
			 * @var Node $endPathPoint
			 */
			$endPathPoint = end($path)['node'];
			$resultPath['m_' . $endPathPoint->identity()] = static::getResultArrayItem($endPathPoint, null, null);
		} catch (Exception $e) {
			echo static::getError('Error building path: ' . $e->getMessage());
		}

		$jsonResult = json_encode($resultPath, JSON_UNESCAPED_UNICODE);

		echo $jsonResult;
	}

	protected static function getResultArrayItem(Node $firstMarker, ?Node $secondMarker, ?Relationship $relationship)
	{
		$resultArray = array(
			'id' => $firstMarker->identity(),
			'title' => $firstMarker->values()['title'],
		);

		if ($secondMarker && $relationship) {
			$resultArray['next'] = array(
				'node' => array(
					'id' => $secondMarker->identity(),
					'title' => $secondMarker->values()['title']
				),
				'path' => array(
					'id' => $relationship->identity(),
					'directions' => $relationship->values()['directions'],
					'startId' => $relationship->startNodeIdentity(),
					'endId' => $relationship->endNodeIdentity()
				)
			);
		} else {
			$resultArray['pathEnd'] = true;
		}

		return $resultArray;
	}

	/**
	 * @param int $firstMarkerId
	 * @param int $secondMarkerId
	 * @return Relationship|null
	 * @throws Exception cypher query exception
	 */
	protected static function getMarkersRelationship(int $firstMarkerId, int $secondMarkerId) : ?Relationship
	{
		$rel = null;
		$query = static::$entityManager->createQuery(
			"MATCH (first:Marker)-[rel:CONNECTED_WITH]->(second:Marker)" .
			"WHERE ID(first)={firstMarkerId} AND ID(second)={secondMarkerId}" .
			"RETURN rel"
		);
		$query->addEntityMapping('first', Marker::class);
		$query->addEntityMapping('second', Marker::class);
		$query->setParameter('firstMarkerId', $firstMarkerId);
		$query->setParameter('secondMarkerId', $secondMarkerId);

		$rel = $query->execute();
		if ($rel) {
			$rel = current($rel)['rel'];
		}

		return $rel;
	}

	/**
	 * @param Marker $startMarker
	 * @param Marker $endMarker
	 * @return array|mixed
	 * @throws Exception cypher query exception
	 */
	protected static function getPath(Marker $startMarker, Marker $endMarker)
	{
		$query = static::$entityManager->createQuery(
			"MATCH (start:Marker), (end:Marker) " .
			"WHERE start.title={startMarker} AND end.title={endMarker} " .
			"CALL algo.shortestPath.stream(start, end, null, {nodeQuery:'Marker', direction:'OUTGOING'}) " .
			"YIELD nodeId, cost " .
			"RETURN algo.asNode(nodeId) as node, cost 	"
		);
		$query->addEntityMapping('start', Marker::class);
		$query->addEntityMapping('end', Marker::class);
		$query->setParameter('startMarker', $startMarker->getTitle());
		$query->setParameter('endMarker', $endMarker->getTitle());
		return $query->execute();
	}

	/**
	 * @param $errorText
	 * @return string json error
	 */
	protected static function getError($errorText)
	{
		return json_encode(array(
			'result' => 'error',
			'error' => $errorText
		));
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
	 * @param $markerId
	 * @return null|object|Marker
	 */
	protected static function getMarker($markerId)
	{
		$markerRepo = static::$entityManager->getRepository(Marker::class);
		return $markerRepo->find($markerId);
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
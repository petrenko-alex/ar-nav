<?php

use GraphAware\Neo4j\OGM\EntityManager;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class GetPathToPlaceObject
{
    public static function run(EntityManager $entityManager)
    {
    	$cypherQueryText = "MATCH (start:Marker{title:'m_908'}), (end:Marker{title:'m_906'})
							CALL algo.shortestPath.stream(start, end, null, {nodeQuery:'Marker', direction:'OUTGOING'})
							YIELD nodeId, cost
							RETURN algo.asNode(nodeId), cost";

        // Получить параметры из request (title текущего маркера пользователя, id целевого PlaceObject)
		// Найти главный маркер для PlaceObject (primary=true)
		// Выполнить запрос на получение кратчайшего пути
		// Форматировать и вернуть результа

        $jsonResult = json_encode(array(), JSON_UNESCAPED_UNICODE);

        header('Access-Control-Allow-Origin: *');
        echo $jsonResult;
    }
}

GetPathToPlaceObject::run($entityManager);
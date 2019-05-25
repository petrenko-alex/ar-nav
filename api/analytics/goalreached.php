<?php

use GraphAware\Neo4j\OGM\EntityManager;
use Petrenko\ArNav\Model\PlaceObject;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class GoalReached
{
    public static function run(EntityManager $entityManager)
    {
        header('Access-Control-Allow-Origin: *');

        $goalId = $_GET['goalId'];
        if (!$goalId) {
            echo static::getError('No data provided');
            return;
        }

        // Get PlaceObject
        $placeObjectsRepo = $entityManager->getRepository(PlaceObject::class);
        /**
         * @var PlaceObject $placeObject
         */
        $placeObject = $placeObjectsRepo->find($goalId);

        if (!$placeObject) {
            echo static::getError('PlaceObject not found for goalId=' . $goalId);
            return;
        }

        try {
            $placeObject->incReachCounter();
        } catch (Exception $e) {

        } finally {
            $entityManager->flush();
        }

        echo json_encode(['result' => 'success',]);
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
}

GoalReached::run($entityManager);
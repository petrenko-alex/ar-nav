<?php

use GraphAware\Neo4j\OGM\EntityManager;
use Petrenko\ArNav\Model\PlaceObject;
use Petrenko\ArNav\Model\PollResult;

require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');

class SubmitPoll
{
    public static function run(EntityManager $entityManager)
    {
        header('Access-Control-Allow-Origin: *');

        $pollData = json_decode(file_get_contents('php://input'), true);
        if (!$pollData) {
            echo static::getError('No data provided');
            return;
        }

        $placeObject = null;
        if ($pollData['goalId']) {
            $placeObjectsRepo = $entityManager->getRepository(PlaceObject::class);
            /**
             * @var PlaceObject $placeObject
             */
            $placeObject = $placeObjectsRepo->find($pollData['goalId']);
        }

        if (!$placeObject) {
            echo static::getError('PlaceObject not found for goalId=' . $pollData['goalId']);
            return;
        }


        $pollResultObj = null;
        try {
            $pollResultObj = new PollResult($pollData);
            $pollResultObj->setPlaceObject($placeObject);
            $entityManager->persist($pollResultObj);
        } catch (Exception $e) {
            echo static::getError($e->getMessage());
            return;
        } finally {
            $entityManager->flush();
        }

        if ($pollResultObj && $pollResultObj->getId()) {
            echo json_encode([
                'result' => 'success',
                'addedId' => $pollResultObj->getId(),
            ]);
            return;
        } else {
            echo static::getError('PollResult wasn\'t created');
            return;
        }
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

SubmitPoll::run($entityManager);
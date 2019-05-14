<?php

use GraphAware\Neo4j\OGM\EntityManager;
use Petrenko\ArNav\Model\Marker;
use Petrenko\ArNav\Model\Place;
use Petrenko\ArNav\Model\PlaceObject;

require_once('bootstrap.php');

class Console
{
	/**
	 * @var GraphAware\Neo4j\OGM\EntityManager
	 */
	private $entityManager;

	public function __construct($entityManager)
	{
		$this->entityManager = $entityManager;
	}

	public function run()
	{
		$stop = false;

		while (!$stop) {
			$this->printMenu();
			$userChoice = trim(fgets(STDIN));
			$userChoice = intval($userChoice);

			switch ($userChoice) {
				case 1:
					{
						$this->createTestDbCommand();
						echo PHP_EOL . PHP_EOL . 'Done!' . PHP_EOL . PHP_EOL;
						break;
					}
				case 2:
					{
						$this->clearDbCommand();
						echo PHP_EOL . PHP_EOL . 'Done!' . PHP_EOL . PHP_EOL;
						break;
					}
				case 0:
					{
						$stop = true;
						break;
					}
				default:
					{
						break;
					}
			}
		}
	}

	protected function printMenu()
	{
		echo '*********** Manage ar-nav database ***********' . PHP_EOL;
		echo '1 - Create test db' . PHP_EOL;
		echo '2 - Clear db' . PHP_EOL;
		echo '0 - Exit' . PHP_EOL;
		echo 'Enter your choice: ';
	}

	protected function createTestDbCommand()
	{
		/** @var  GraphAware\Neo4j\OGM\EntityManager $entityManager */
		$dbArray = $this->getData();

        // Create Places
		foreach ($dbArray as $place)
		{
			try
            {
				$placeObj = new Place(
					$place['name'],
					$place['description'],
					$place['image']
				);

				// Create PlaceObjects for Place
				$placeObjects = [];
				foreach ($place['placeObjects'] as $placeObject)
				{
                    $placeObjectObj = new PlaceObject(
                        $placeObject['title'],
                        $placeObject['description'],
                        $placeObject['type'],
                        $placeObj
                    );

                    // Create Markers for PlaceObject
                    $placeObjectMarkers = [];
                    foreach ($placeObject['markers'] as $marker)
                    {
                        $markerObj = new Marker($marker['title'], $placeObjectObj);
                        $this->entityManager->persist($markerObj);

                        $placeObjectMarkers[] = $markerObj;
                    }

                    $placeObjectObj->setMarkers($placeObjectMarkers);
                    $this->entityManager->persist($placeObjectObj);

                    $placeObjects[] = $placeObjectObj;
                }

				$placeObj->setPlaceObjects($placeObjects);
				$this->entityManager->persist($placeObj);
			} catch (Exception $e) {
				echo $e->getMessage();
				return;
			} finally {
				$this->entityManager->flush();
			}
		}


        // Add relationships between markers in POAS place
        // TODO: Make it bidirectional
		// TODO: Add real marker relationships
		// TODO: Marker without place object (PP_1)
//        try {
//            $query = $this->entityManager->createQuery(
//                "MATCH (m:Marker)-[:ASSIGNED_TO]->(:PlaceObject)-[:IS_IN]->(p:Place) WHERE p.title={placeName} "
//                . "RETURN m"
//            );
//            $query->addEntityMapping('m', Marker::class);
//            $query->addEntityMapping('p', Place::class);
//            $query->setParameter('placeName', 'Кафедра ПОАС');
//
//            /** @var Marker[] $markers */
//            $markers = $query->execute();
//
//            $amountOfMarkers = count($markers);
//            for($i = 0; $i < $amountOfMarkers - 1; $i++)
//            {
//                $curMarker = $markers[$i];
//                $nextMarker = $markers[$i + 1];
//                $curMarker->setNext($nextMarker, rand(1, 360) . ' degrees');
//            }
//        } catch (Exception $e) {
//            echo $e->getMessage();
//            return;
//        } finally {
//            $this->entityManager->flush();
//        }
	}

	protected function clearDbCommand()
	{
		try {
			$query = $this->entityManager->createQuery(
				'MATCH (n) DETACH DELETE n'
			);
			$query->execute();
		} catch (Exception $e) {
			echo $e->getMessage();
			return;
		}
	}

	protected function getData()
	{
		return [
			$this->getPoasData(),
			$this->getEvmData(),
			$this->getSaprData(),
		];
	}

	protected function getPoasData()
	{
		return [
			'id' => 1,
			'name' => 'Кафедра ПОАС',
			'description' => 'Этаж кафедры ПОАС. Учебно-лабораторный корпус "В". 9 этаж.',
			'image' => 'https://source.unsplash.com/random/400x400?sig=' . rand(1, 1000),
			'placeObjects' => [[
				'id' => 1,
				'title' => 'Ауд. 901',
				'description' => 'room #901',
				'type' => 'room',
				'markers' => [[
					'id' => 1,
					'title' => 'm_901_1',
                    'relationships' => [[
                        'to' => 'm_Ladder',
                        'directions' => '[270]'
                    ], [
                        'to' => 'm_908',
                        'directions' => '[170]'
                    ], [
                        'to' => 'm_901_2',
                        'directions' => '[90](along the wall)'
                    ]]
				], [
					'id' => 2,
					'title' => 'm_901_2',
                    'relationships' => [[
                        'to' => 'm_901_1',
                        'directions' => '[270](along the wall)'
                    ], [
                        'to' => 'm_907',
                        'directions' => '[190]'
                    ], [
                        'to' => 'm_906',
                        'directions' => '[170]'
                    ], [
                        'to' => 'm_902_1',
                        'directions' => '[90](along the wall)'
                    ]]
				]],
			], [
				'id' => 2,
				'title' => 'Ауд. 902',
				'description' => 'room #902',
				'type' => 'room',
				'markers' => [[
					'id' => 3,
					'title' => 'm_902_1',
                    'relationships' => [[
                        'to' => 'm_901_2',
                        'directions' => '[270](along the wall)'
                    ], [
                        'to' => 'm_902_2',
                        'directions' => '[90](along the wall)'
                    ], [
                        'to' => 'm_w_WC',
                        'directions' => '[190]'
                    ]]
				], [
					'id' => 4,
					'title' => 'm_902_2',
                    'relationships' => [[
                        'to' => 'm_902_1',
                        'directions' => '[90](along the wall)'
                    ], [
                        'to' => 'm_902_3',
                        'directions' => '[270](along the wall)'
                    ], [
                        'to' => 'm_Elevator',
                        'directions' => '[180]'
                    ]]
				], [
					'id' => 5,
					'title' => 'm_902_3',
                    'relationships' => [[
                        'to' => 'm_902_2',
                        'directions' => '[270](along the wall)'
                    ], [
                        'to' => 'm_903',
                        'directions' => '[90](along the wall)'
                    ], [
                        'to' => 'm_m_WC',
                        'directions' => '[170]'
                    ]]
				]],
			], [
				'id' => 3,
				'title' => 'Ауд. 903',
				'description' => 'room #903',
				'type' => 'room',
				'markers' => [[
					'id' => 6,
					'title' => 'm_903',
                    'relationships' => [[
                        'to' => 'm_902_3',
                        'directions' => '[270](along the wall)'
                    ], [
                        'to' => 'm_PP_1',
                        'directions' => '[90]'
                    ]]
				]],
			], [
				'id' => 4,
				'title' => 'Ауд. 904',
				'description' => 'room #904',
				'type' => 'room',
				'markers' => [[
					'id' => 7,
					'title' => 'm_904',
                    'relationships' => [[
                        'to' => 'm_905',
                        'directions' => '[180]'
                    ], [
                        'to' => 'm_PP_1',
                        'directions' => '[270]'
                    ]]
				]],
			], [
				'id' => 5,
				'title' => 'Ауд. 905',
				'description' => 'room #905',
				'type' => 'room',
				'markers' => [[
					'id' => 8,
					'title' => 'm_905',
                    'relationships' => [[
                        'to' => 'm_904',
                        'directions' => '[270]'
                    ], [
                        'to' => 'm_PP_1',
                        'directions' => '[90]'
                    ]]
				]],
			], [
				'id' => 6,
				'title' => 'Ауд. 906',
				'description' => 'room #906',
				'type' => 'room',
				'markers' => [[
					'id' => 9,
					'title' => 'm_906',
                    'relationships' => [[
                        'to' => 'm_w_WC',
                        'directions' => '[270](along the wall)'
                    ], [
                        'to' => 'm_907',
                        'directions' => '[90](along the wall)'
                    ], [
                        'to' => 'm_901_2',
                        'directions' => '[170]'
                    ]]
				]],
			], [
				'id' => 7,
				'title' => 'Ауд. 907',
				'description' => 'room #907',
				'type' => 'room',
				'markers' => [[
					'id' => 10,
					'title' => 'm_907',
                    'relationships' => [[
                        'to' => 'm_906',
                        'directions' => '[270](along the wall)'
                    ], [
                        'to' => 'm_908',
                        'directions' => '[90](along the wall)'
                    ], [
                        'to' => 'm_901_2',
                        'directions' => '[190]'
                    ]]
				]],
			], [
				'id' => 8,
				'title' => 'Ауд. 908',
				'description' => 'room #908',
				'type' => 'room',
				'markers' => [[
					'id' => 11,
					'title' => 'm_908',
                    'relationships' => [[
                        'to' => 'm_907',
                        'directions' => '[270](along the wall)'
                    ], [
                        'to' => 'm_Ladder',
                        'directions' => '[90]'
                    ], [
                        'to' => 'm_901_1',
                        'directions' => '[170]'
                    ]]
				]],
			], [
				'id' => 9,
				'title' => 'Мужской туалет',
				'description' => 'Male WC',
				'type' => 'wc',
				'markers' => [[
					'id' => 12,
					'title' => 'm_m_WC',
                    'relationships' => [[
                        'to' => 'm_902_3',
                        'directions' => '[170]'
                    ]]
				]],
			], [
				'id' => 10,
				'title' => 'Женский туалет',
				'description' => 'Female WC',
				'type' => 'wc',
				'markers' => [[
					'id' => 13,
					'title' => 'm_w_WC',
                    'relationships' => [[
                        'to' => 'm_906',
                        'directions' => '[90](along the wall)'
                    ], [
                        'to' => 'm_902_1',
                        'directions' => '[190]'
                    ]]
				]],
			], [
				'id' => 11,
				'title' => 'Лифтовая',
				'description' => 'Elevator room',
				'type' => 'elevator',
				'markers' => [[
					'id' => 14,
					'title' => 'm_Elevator',
                    'relationships' => [[
                        'to' => 'm_902_2',
                        'directions' => '[0](through the door)'
                    ]]
				]],
			], [
				'id' => 12,
				'title' => 'Лестница',
				'description' => 'Ladder',
				'type' => 'ladder',
				'markers' => [[
					'id' => 15,
					'title' => 'm_Ladder',
                    'relationships' => [[
                        'to' => 'm_901_1',
                        'directions' => '[90]'
                    ], [
                        'to' => 'm_908',
                        'directions' => '[210]'
                    ]]
				]],
			]]
		];
	}

	protected function getEvmData()
	{
		return [
			'id' => 2,
			'name' => 'Кафедра ЭВМ',
			'description' => 'Этаж кафедры ЭВМ. Учебно-лабораторный корпус "В". 12 этаж.',
			'image' => 'https://source.unsplash.com/random/400x400?sig=' . rand(1, 1000),
			'placeObjects' => [[
				'id' => 12,
				'title' => 'Ауд. 1201',
				'description' => 'room #1201',
				'type' => 'room',
			], [
				'id' => 13,
				'title' => 'Ауд. 1203',
				'description' => 'room #1203',
				'type' => 'room',
			]],
		];
	}

	protected function getSaprData()
	{
		return [
			'id' => 3,
			'name' => 'Кафедра САПР',
			'description' => 'Этаж кафедры САПРиПК. Учебно-лабораторный корпус "В". 14 этаж.',
			'image' => 'https://source.unsplash.com/random/400x400?sig=' . rand(1, 1000),
			'placeObjects' => [[
				'id' => 14,
				'title' => 'Ауд. 1404',
				'description' => 'room #1404',
				'type' => 'room',
			], [
				'id' => 15,
				'title' => 'Ауд. 1406',
				'description' => 'room #1406',
				'type' => 'room',
			]],
		];
	}
}

if (!$entityManager) {
	$entityManager = EntityManager::create(getenv('NEO4J_CONNECT_STRING'));
}

$console = new Console($entityManager);
$console->run();
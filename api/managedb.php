<?php

use GraphAware\Neo4j\OGM\EntityManager;
use Petrenko\ArNav\Model\Marker;
use Petrenko\ArNav\Model\Place;

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
						echo 'Done!' . PHP_EOL . PHP_EOL;
						break;
					}
				case 2:
					{
						$this->clearDbCommand();
						echo 'Done!' . PHP_EOL . PHP_EOL;
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
		$dbArray = $this->getTestDbAsArray();

		foreach ($dbArray as $place) {
			try {
				$placeObj = new Place(
					$place['name'],
					$place['description'],
					$place['image']
				);

				$markers = [];
				foreach ($place['markers'] as $marker) {
					$marker = new Marker($marker['info'], $placeObj);
					$markers[] = $marker;

					$this->entityManager->persist($marker);
				}

				$placeObj->setMarkers($markers);
				$this->entityManager->persist($placeObj);
			} catch (Exception $e) {
				echo $e->getMessage();
				return;
			} finally {
				$this->entityManager->flush();
			}
		}
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

	protected function getTestDbAsArray()
	{
		// Places info
		$places = [
			[
				'id' => 1,
				'name' => 'Кафедра ПОАС',
				'description' => 'Этаж кафедры ПОАС. Учебно-лабораторный корпус "В". 9 этаж.',
				'image' => 'https://source.unsplash.com/random/400x400?sig=' . rand(1, 1000),
			],
			[
				'id' => 2,
				'name' => 'Кафедра ЭВМ',
				'description' => 'Этаж кафедры ЭВМ. Учебно-лабораторный корпус "В". 12 этаж.',
				'image' => 'https://source.unsplash.com/random/400x400?sig=' . rand(1, 1000),
			],
			[
				'id' => 3,
				'name' => 'Кафедра САПР',
				'description' => 'Этаж кафедры САПРиПК. Учебно-лабораторный корпус "В". 14 этаж.',
				'image' => 'https://source.unsplash.com/random/400x400?sig=' . rand(1, 1000),
			],
		];

		// Add markers to places
		foreach ($places as &$place) {
			$placeName = $place['name'];
			for ($i = 0; $i < rand(1, 5); ++$i) {
				$place['markers'][] = [
					'id' => $i,
					'info' => 'marker ' . ($i + 1) . ' in place ' . $placeName
				];
			}
		}

		return $places;
	}
}

if (!$entityManager) {
	$entityManager = EntityManager::create('http://neo4j:123456@localhost:7474');
}

$console = new Console($entityManager);
$console->run();
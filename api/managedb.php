<?php
use GraphAware\Neo4j\OGM\EntityManager;
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

                        $this->createDefaultPlacesCommand();
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

    protected function createDefaultPlacesCommand()
    {
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

        foreach ($places as $place) {
            $placeObj = new Place();
            $placeObj->setTitle($place['name']);
            $placeObj->setDescription($place['description']);
            $placeObj->setImagePath($place['image']);

            try {
                /** @var  GraphAware\Neo4j\OGM\EntityManager $entityManager */
                $this->entityManager->persist($placeObj);
            }
            catch (Exception $e) {
                echo $e->getMessage();
                return;
            }
            $this->entityManager->flush();
        }
    }

    protected function printMenu()
    {
        echo '*********** Manage ar-nav database ***********' . PHP_EOL;
        echo '1 - Create default places' . PHP_EOL;
        echo '0 - Exit' . PHP_EOL;
        echo 'Enter your choice: ';
    }
}

if (!$entityManager) {
    $entityManager = EntityManager::create('http://neo4j:123456@localhost:7474');
}

$console = new Console($entityManager);
$console->run();
<?php
use Petrenko\ArNav\Model\Place;

//require_once($_SERVER['DOCUMENT_ROOT'] . '/api/bootstrap.php');
require_once('../bootstrap.php');

class GetAllPlaces
{
	public static function run()
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

		$jsonPlaces = json_encode($places, JSON_UNESCAPED_UNICODE);

		header('Access-Control-Allow-Origin: *');
		echo $jsonPlaces;
	}
}

//GetAllPlaces::run();

//$placeName = $argv[1];
//$placeDesc = $argv[2];
//$placeImagePath = $argv[3];
//
//$place = new Place();
//$place->setTitle($placeName);
//$place->setDescription($placeDescription);
//$place->setImagePath($placeImagePath);
//
//try {
//	$entityManager->persist($place);
//	$entityManager->flush();
//}
//catch (Exception $e) {
//	echo $e->getMessage();
//}
//
//echo sprintf('Created Person with ID "%d"', $place->getId());

$placesRepo = $entityManager->getRepository(Place::class);
$places = $placesRepo->findAll();

foreach ($places as $place) {
	echo sprintf("- %s\n", $place->getTitle());
}
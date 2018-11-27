<?php
namespace Petrenko\ArNav\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @package Petrenko\Model
 *
 * @OGM\Node(label="Place")
 */
class Place
{
	/**
	 * @var int
	 *
	 * @OGM\GraphId()
	 */
	private $id;

	/**
	 * @var string
	 *
	 * @OGM\Property(type="string")
	 */
	private $title;

	/**
	 * @var string
	 *
	 * @OGM\Property(type="string")
	 */
	private $description;

	/**
	 * @var string
	 *
	 * @OGM\Property(type="string")
	 */
	private $imagePath;

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getImagePath()
	{
		return $this->imagePath;
	}

	/**
	 * @param string $imagePath
	 */
	public function setImagePath($imagePath)
	{
		$this->imagePath = $imagePath;
	}
}
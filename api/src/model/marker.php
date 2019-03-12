<?php

namespace Petrenko\ArNav\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @package Petrenko\Model
 *
 * @OGM\Node(label="Marker")
 */
class Marker
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
	private $info;

	/**
	 * @var Place
	 *
	 * @OGM\Relationship(type="IS_IN", direction="OUTGOING", mappedBy="markers", targetEntity="Place")
	 */
	private $place;

	/**
	 * Marker constructor.
	 * @param string $info
	 * @param Place $place
	 */
	public function __construct($info, Place $place = null)
	{
		$this->info = $info;
		$this->place = $place;
	}

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
	public function getInfo()
	{
		return $this->info;
	}

	/**
	 * @param string $info
	 *
	 * @return Marker
	 */
	public function setInfo($info)
	{
		$this->info = $info;

		return $this;
	}

	/**
	 * @return array
	 */
	public function toArray()
	{
		return get_object_vars($this);
	}
}
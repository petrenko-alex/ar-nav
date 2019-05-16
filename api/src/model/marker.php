<?php

namespace Petrenko\ArNav\Model;

use GraphAware\Bolt\Result\Type\Path;
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
	private $title;

	/**
	 * @var bool
	 *
	 * @OGM\Property(type="boolean")
	 */
	private $primary;

	/**
	 * @var PlaceObject
	 *
	 * @OGM\Relationship(
     *     type="ASSIGNED_TO",
     *     direction="OUTGOING",
     *     mappedBy="markers",
     *     targetEntity="PlaceObject"
     * )
	 */
	private $placeObject;

    /**
     * @var PathUnit
     *
     * @OGM\Relationship(
     *     relationshipEntity="PathUnit",
     *     type="CONNECTED_WITH",
     *     direction="BOTH",
     *     mappedBy="endMarker"
     * )
     */
	private $next;

    /**
     * @var PathUnit
     *
     * @OGM\Relationship(
     *     relationshipEntity="PathUnit",
     *     type="CONNECTED_WITH",
     *     direction="BOTH",
     *     mappedBy="startMarker"
     * )
     */
    private $prev;

    /**
     * Marker constructor.
     * @param string $title
     * @param PlaceObject $placeObject
     */
	public function __construct(string $title, PlaceObject $placeObject = null)
	{
	    $this->title = $title;
		$this->placeObject = $placeObject;
		$this->primary = true;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Marker
     */
    public function setTitle(string $title): Marker
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return PathUnit
     */
    public function getNext(): PathUnit
    {
        return $this->next;
    }

    /**
     * @return PathUnit
     */
    public function getPrev(): PathUnit
    {
        return $this->prev;
    }

	/**
	 * @return bool
	 */
	public function getPrimary(): bool
	{
		return $this->primary;
	}

	/**
	 * @param bool $primary
	 * @return Marker
	 */
	public function setPrimary(bool $primary): Marker
	{
		$this->primary = $primary;

		return $this;
	}

    /**
     * @param Marker $next
     * @param string $directions
     * @return PathUnit created path
	 */
    public function setNext(Marker $next, string $directions): PathUnit
    {
        $pathUnit = new PathUnit($this, $next, $directions);
        $this->next = $pathUnit;

        return $pathUnit;
    }

	/**
	 * @return array
	 */
	public function toArray()
	{
		return [
		    'id' => $this->id,
            'title' => $this->title,
            'placeObject' => $this->placeObject->toArray(),
        ];
	}
}
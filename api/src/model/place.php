<?php

namespace Petrenko\ArNav\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;
use GraphAware\Neo4j\OGM\Common\Collection;

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
	 * @var Marker[]|Collection
	 *
	 * @OGM\Relationship(type="IS_IN", direction="INCOMING", collection=true, mappedBy="place", targetEntity="Marker")
	 */
	private $markers;

	public function __construct($title, $description, $imagePath, $markers = [])
	{
		$this->title = $title;
		$this->description = $description;
		$this->imagePath = $imagePath;

		$this->markers = $markers;
		if (!$this->markers) {
			$this->markers = new Collection();
		}
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

	/**
	 * @return Collection|Marker[]
	 */
	public function getMarkers()
	{
		return $this->markers;
	}

	/**
	 * @param Collection|Marker[] $markers
	 * @return Place
	 */
	public function setMarkers($markers)
	{
		$this->markers = $markers;

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
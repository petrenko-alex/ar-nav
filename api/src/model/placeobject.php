<?php

namespace Petrenko\ArNav\Model;

use GraphAware\Neo4j\OGM\Common\Collection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @package Petrenko\Model
 *
 * @OGM\Node(label="PlaceObject")
 */
class PlaceObject
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
    private $type;

    /**
     * @var Marker[]|Collection
     *
     * @OGM\Relationship(
     *     type="ASSIGNED_TO",
     *     direction="INCOMING",
     *     collection=true,
     *     mappedBy="placeObject",
     *     targetEntity="Marker"
     * )
     */
    private $markers;

    /**
     * @var Place
     *
     * @OGM\Relationship(
     *     type="IS_IN",
     *     direction="OUTGOING",
     *     mappedBy="placeObjects",
     *     targetEntity="Place"
     * )
     */
    private $place;

    /**
     * PlaceObject constructor.
     * @param string $title
     * @param string $description
     * @param string $type
     * @param Place $place
     */
    public function __construct(string $title, string $description, string $type, Place $place)
    {
        $this->type = $type;
        $this->title = $title;
        $this->place = $place;
        $this->description = $description;
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
     * @return PlaceObject
     */
    public function setMarkers($markers): PlaceObject
    {
        $this->markers = $markers;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return PlaceObject
     */
    public function setDescription(string $description): PlaceObject
    {
        $this->description = $description;

        return $this;
    }
}
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
     * @var int counter of successfully completed paths
     *          to current PlaceObject
     *
     * @OGM\Property(type="int")
     */
    private $reachCounter;

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
     * @var PollResult[]|Collection
     *
     * @OGM\Relationship(
     *     type="RELATED_TO",
     *     direction="INCOMING",
     *     collection=true,
     *     mappedBy="placeObject",
     *     targetEntity="PollResult"
     * )
     */
    private $pollResults;

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
        $this->reachCounter = 0;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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

    /**
     * @return Collection|PollResult[]
     */
    public function getPollResults()
    {
        return $this->pollResults;
    }

    /**
     * @return int
     */
    public function getReachCounter(): int
    {
        return $this->reachCounter;
    }

    /**
     * @param int $reachCounter
     *
     * @return PlaceObject
     */
    public function setReachCounter(int $reachCounter): PlaceObject
    {
        $this->reachCounter = $reachCounter;

        return $this;
    }

    /**
     * Increments $reachCounter
     */
    public function incReachCounter()
    {
        $this->reachCounter++;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'type' => $this->type,
        ];
    }
}
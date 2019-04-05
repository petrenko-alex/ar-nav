<?php

namespace Petrenko\ArNav\Model;

use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 *
 * @OGM\RelationshipEntity(type="CONNECTED_WITH")
 */
class PathUnit
{
    /**
     * @var int
     *
     * @OGM\GraphId()
     */
    private $id;

    /**
     * @var Marker
     *
     * @OGM\StartNode(targetEntity="Marker")
     */
    protected $startMarker;

    /**
     * @var Marker
     *
     * @OGM\EndNode(targetEntity="Marker")
     */
    protected $endMarker;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    protected $directions;

    /**
     * PathUnit constructor.
     * @param Marker $startMarker
     * @param Marker $endMarker
     * @param string $directions
     */
    public function __construct(Marker $startMarker, Marker $endMarker, string $directions)
    {
        $this->startMarker = $startMarker;
        $this->endMarker = $endMarker;
        $this->directions = $directions;
    }
}
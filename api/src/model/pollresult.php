<?php

namespace Petrenko\ArNav\Model;

use GraphAware\Neo4j\OGM\Common\Collection;
use GraphAware\Neo4j\OGM\Annotations as OGM;

/**
 * @package Petrenko\Model
 *
 * @OGM\Node(label="PollResult")
 */
class PollResult
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
    private $sex;

    /**
     * @var int
     *
     * @OGM\Property(type="int")
     */
    private $age;

    /**
     * @var string
     *
     * @OGM\Property(type="string")
     */
    private $personType;

    /**
     * @var int
     *
     * @OGM\Property(type="int")
     */
    private $rating;

    /**
     * @var array
     *
     * @OGM\Property(type="array")
     */
    private $preferredNavType;

    /**
     * @var PlaceObject
     *
     * @OGM\Relationship(
     *     type="RELATED_TO",
     *     direction="OUTGOING",
     *     mappedBy="pollResults",
     *     targetEntity="PlaceObject"
     * )
     */
    private $placeObject;

    /**
     * PlaceObject constructor.
     * @param array $data
     */
    public function __construct(?array $data)
    {
        if ($data['sex']) {
            $this->setSex($data['sex']);
        }

        if ($data['age']) {
            $this->setAge($data['age']);
        }

        if ($data['personType']) {
            $this->setPersonType($data['personType']);
        }

        if ($data['rating']) {
            $this->setRating($data['rating']);
        }

        if ($data['preferredNavType']) {
            $this->setPreferredNavType($data['preferredNavType']);
        }

        if ($data['placeObject']) {
            $this->setPlaceObject($data['placeObject']);
        }
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getSex(): string
    {
        return $this->sex;
    }

    /**
     * @param string $sex 'man' or 'woman'
     *
     * @return PollResult
     */
    public function setSex(string $sex): PollResult
    {
        if ($sex === 'man' || $sex === 'woman') {
            $this->sex = $sex;
        }
        return $this;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age range - [5;100]
     *
     * @return PollResult
     */
    public function setAge(int $age): PollResult
    {
        if ($age >= 5 && $age <= 100) {
            $this->age = $age;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getPersonType(): string
    {
        return $this->personType;
    }

    /**
     * @param string $personType range - ['student', 'teacher', 'applicant', 'parent', 'stranger']
     *
     * @return PollResult
     */
    public function setPersonType(string $personType): PollResult
    {
        $availablePersonTypes = [
            'student',
            'teacher',
            'applicant',
            'parent',
            'stranger'
        ];

        if (in_array($personType, $availablePersonTypes)) {
            $this->personType = $personType;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * @param int $rating range - [1;5]
     *
     * @return PollResult
     */
    public function setRating(int $rating): PollResult
    {
        if ($rating >= 1 && $rating <= 5) {
            $this->rating = $rating;
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getPreferredNavType(): array
    {
        return $this->preferredNavType;
    }

    /**
     * @param array $preferredNavType range - ['text', 'voice', '3d-object']
     *
     * @return PollResult
     */
    public function setPreferredNavType(array $preferredNavType): PollResult
    {
        $filteredNavTypes = [];
        $availableNavTypes = [
            'text',
            'voice',
            '3d-object'
        ];

        foreach ($preferredNavType as $navType) {
            if (in_array($navType, $availableNavTypes)) {
                $filteredNavTypes[] = $navType;
            }
        };

        $this->preferredNavType = $filteredNavTypes;

        return $this;
    }

    /**
     * @return PlaceObject
     */
    public function getPlaceObject(): PlaceObject
    {
        return $this->placeObject;
    }

    /**
     * @param PlaceObject $placeObject
     *
     * @return PollResult
     */
    public function setPlaceObject(PlaceObject $placeObject): PollResult
    {
        $this->placeObject = $placeObject;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'sex' => $this->sex,
            'age' => $this->age,
            'personType' => $this->personType,
            'rating' => $this->rating,
            'preferredNavType' => $this->preferredNavType,
            'placeObject' => $this->placeObject ? $this->placeObject->toArray() : null,
        ];
    }
}
<?php

namespace PHPOrchestra\ModelBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use PHPOrchestra\ModelInterface\Model\BlockInterface;

/**
 * Description of BaseBlock
 *
 * @ODM\EmbeddedDocument
 */
class Block implements BlockInterface
{
    /**
     * @var string $component
     *
     * @ODM\Field(type="string")
     */
    protected $component;

    /**
     * @var string $label
     *
     * @ODM\Field(type="string")
     */
    protected $label;

    /**
     * @var string $class
     *
     * @ODM\Field(type="string")
     */
    protected $class;

    /**
     * @var string $id
     *
     * @ODM\Field(type="string")
     */
    protected $id;

    /**
     * @var array $attributes
     *
     * @ODM\Field(type="hash")
     */
    protected $attributes = array();

    /**
     * @ODM\Field(type="collection")
     */
    protected $areas = array();

    /**
     * Set component
     *
     * @param string $component
     */
    public function setComponent($component)
    {
        $this->component = $component;
    }

    /**
     * Get component
     *
     * @return string $component
     */
    public function getComponent()
    {
        return $this->component;
    }

    /**
     * Set label
     *
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * Get label
     *
     * @return string $label
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set attributes
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function addAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Get attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param string $name
     *
     * @return string|null
     */
    public function getAttribute($name)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

        return null;
    }

    /**
     * @return array
     */
    public function getAreas()
    {
        return $this->areas;
    }

    /**
     * @param array $areas
     */
    public function setAreas(array $areas)
    {
        $this->areas = $areas;
    }

    /**
     * @param array $area
     */
    public function addArea(array $area)
    {
        if (!in_array($area, $this->areas)) {
            $this->areas[] = $area;
        }
    }

    /**
     * @param string $areaId
     * @param string $nodeId
     */
    public function removeAreaRef($areaId, $nodeId)
    {
        foreach ($this->getAreas() as $key => $area) {
            if ($areaId === $area['areaId'] && ($nodeId === $area['nodeId'] || 0 === $area['nodeId'])) {
                unset($this->areas[$key]);
            }
        }
    }

    /**
     * Set class
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Get class
     *
     * @return string $class
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set id
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Get id
     *
     * @return string $id
     */
    public function getId()
    {
        return $this->id;
    }
}

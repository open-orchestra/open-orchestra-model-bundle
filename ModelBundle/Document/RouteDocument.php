<?php

namespace OpenOrchestra\ModelBundle\Document;

use OpenOrchestra\ModelInterface\Model\RouteDocumentInterface;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

/**
 * Class RouteDocument
 *
 * @ODM\Document(
 *   collection="route_document",
 *   repositoryClass="OpenOrchestra\ModelBundle\Repository\RouteDocumentRepository"
 * )
 */
class RouteDocument implements RouteDocumentInterface
{
    /**
     * @var string $id
     *
     * @ODM\Id
     */
    protected $id;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $name;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $nodeId;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $language;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $siteId;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $aliasId;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $pattern;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $host;

    /**
     * @var array
     *
     * @ODM\Field(type="raw")
     */
    protected $defaults;

    /**
     * @var array
     *
     * @ODM\Field(type="raw")
     */
    protected $requirements = array();

    /**
     * @var array
     *
     * @ODM\Field(type="raw")
     */
    protected $options = array();

    /**
     * @var string|array
     *
     * @ODM\Field(type="raw")
     */
    protected $schemes;

    /**
     * @var string|array
     *
     * @ODM\Field(type="raw")
     */
    protected $methods;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token0;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token1;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token2;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token3;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token4;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token5;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token6;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token7;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token8;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token9;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $token10;

    /**
     * @var string
     *
     * @ODM\Field(type="string")
     */
    protected $condition;

    /**
     * @var int
     *
     * @ODM\Field(type="int")
     */
    protected $weight = 0;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;

        $workingPattern = explode('/', trim($pattern, '/'));

        foreach ($workingPattern as $key => $value) {
            $this->setToken($key, $value);
        }
    }

    /**
     * @param int    $number
     * @param string $value
     */
    protected function setToken($number, $value)
    {
        $tokenNumber = 'token' . $number;
        if (preg_match('/{.*}/', $value)) {
            $value = '*';
            $this->weight += pow(10, $number);
        }
        $this->$tokenNumber = $value;
    }

    /**
     * @return array
     */
    public function getDefaults()
    {
        if (null === $this->defaults) {
            return array(
                '_locale' => $this->getLanguage(),
                'nodeId' => $this->getNodeId(),
                'siteId' => $this->getSiteId(),
                'aliasId' => $this->getAliasId(),
            );
        }

        return $this->defaults;
    }

    /**
     * @param array $defaults
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;
    }

    /**
     * @return array
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @param array $requirements
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * @return array|string
     */
    public function getSchemes()
    {
        return $this->schemes;
    }

    /**
     * @param array|string $schemes
     */
    public function setSchemes($schemes)
    {
        $this->schemes = $schemes;
    }

    /**
     * @return array|string
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param array|string $methods
     */
    public function setMethods($methods)
    {
        $this->methods = $methods;
    }

    /**
     * @return string
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * @param string $condition
     */
    public function setCondition($condition)
    {
        $this->condition = $condition;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getToken0()
    {
        return $this->token0;
    }

    /**
     * @return string
     */
    public function getToken1()
    {
        return $this->token1;
    }

    /**
     * @return string
     */
    public function getToken2()
    {
        return $this->token2;
    }

    /**
     * @return string
     */
    public function getToken3()
    {
        return $this->token3;
    }

    /**
     * @return string
     */
    public function getToken4()
    {
        return $this->token4;
    }

    /**
     * @return string
     */
    public function getToken5()
    {
        return $this->token5;
    }

    /**
     * @return string
     */
    public function getToken6()
    {
        return $this->token6;
    }

    /**
     * @return string
     */
    public function getToken7()
    {
        return $this->token7;
    }

    /**
     * @return string
     */
    public function getToken8()
    {
        return $this->token8;
    }

    /**
     * @return string
     */
    public function getToken9()
    {
        return $this->token9;
    }

    /**
     * @return string
     */
    public function getToken10()
    {
        return $this->token10;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @return string
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * @param string $nodeId
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getSiteId()
    {
        return $this->siteId;
    }

    /**
     * @param string $siteId
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;
    }

    /**
     * @return string
     */
    public function getAliasId()
    {
        return $this->aliasId;
    }

    /**
     * @param string $aliasId
     */
    public function setAliasId($aliasId)
    {
        $this->aliasId = $aliasId;
    }
}

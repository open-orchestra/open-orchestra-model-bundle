<?php

namespace OpenOrchestra\ModelBundle\Repository\RepositoryTrait;

use OpenOrchestra\ModelInterface\Repository\RepositoryTrait\KeywordableTraitInterface;
/**
 * Trait KeywordableTrait
 */
trait KeywordableTrait
{
    /**
     * @param string $condition
     * @param int    $count
     * @param array  $aliases
     * @param string $delimiter
     *
     * @return array|null
     */
    public function transformConditionToMongoCondition($condition, $count = 0, array $aliases = array(), $delimiter = '##')
    {
        if (!is_null($condition)) {
            $result = array();
            $encapsuledElements = array();
            preg_match_all(KeywordableTraitInterface::GET_BALANCED_BRACKETS, $condition, $encapsuledElements);
            foreach ($encapsuledElements[0] as $key => $encapsuledElement) {
                $alias = $delimiter.$count.$delimiter;
                $condition = preg_replace('/'.preg_quote($encapsuledElement).'/', $alias, $condition, 1);
                $aliases[$alias] = $this->transformSubConditionToMongoCondition($encapsuledElements[1][$key], $aliases);
                $count++;
            }
            if (count($encapsuledElements[0]) > 0) {
                $result = $this->transformConditionToMongoCondition($condition, $count, $aliases, $delimiter);
            } else {
                $result = $this->transformSubConditionToMongoCondition($condition, $aliases);
            }
        } else {
            $result = null;
        }

        return $result;
    }

    /**
     * @param string $condition
     * @param array  $aliases
     *
     * @return array
     *
     * @throws TransformationFailedException
     */
    public function transformSubConditionToMongoCondition($condition, array &$aliases)
    {
        $subElements = array();
        if (preg_match(KeywordableTraitInterface::IS_AND_BOOLEAN, $condition)) {
            preg_match_all(KeywordableTraitInterface::GET_AND_SUB_BOOLEAN, $condition, $subElements);
        } elseif  (preg_match(KeywordableTraitInterface::IS_OR_BOOLEAN, $condition)) {
            preg_match_all(KeywordableTraitInterface::GET_OR_SUB_BOOLEAN, $condition, $subElements);
        }
        if (count($subElements) > 0) {
            $operator = ($subElements[3][0] == ' OR ') ? '$or' : '$and';
            $result = array();
            foreach($subElements[2] as $key => $subElement) {
                if (array_key_exists($subElement, $aliases)) {
                    if ($subElements[1][$key] != '') {
                        array_push($result, array('$not' => $aliases[$subElement]));
                    } else {
                        array_push($result, $aliases[$subElement]);
                    }
                    unset($aliases[$subElement]);
                } else {
                    $comparison = ($subElements[1][$key] == '') ? '$eq' : '$ne';
                    array_push($result, array('keywords.$id' => array($comparison => new \MongoId($subElement))));
                }
            }

            return (array($operator => $result));
        }
    }
}

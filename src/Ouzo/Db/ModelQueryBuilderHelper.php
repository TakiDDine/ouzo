<?php
namespace Ouzo\Db;

use InvalidArgumentException;
use Ouzo\Model;
use Ouzo\Utilities\Arrays;
use Ouzo\Utilities\Functions;

class ModelQueryBuilderHelper
{
    public static function extractRelations(Model $root, $relationSelector)
    {
        $relations = array();
        if ($relationSelector instanceof Relation) {
            $relations[] = $relationSelector;
        } else {
            $relationNames = explode('->', $relationSelector);
            $model = $root;
            foreach ($relationNames as $name) {
                $relation = $model->getRelation($name);
                $relations[] = $relation;
                $model = $relation->getRelationModelObject();
            }
        }
        return $relations;
    }

    public static function associateRelationsWithAliases($relations, $aliases)
    {
        $aliases = Arrays::toArray($aliases);
        if (count($relations) < count($aliases)) {
            throw new InvalidArgumentException("More aliases than relations in join");
        }

        $relationWithAliases = array();
        foreach ($relations as $index => $relation) {
            $alias = Arrays::getValue($aliases, $index, Arrays::getValue($aliases, $relation->getName()));
            $relationWithAliases[] = new RelationWithAlias($relation, $alias);
        }
        return $relationWithAliases;
    }

    public static function createModelJoins($fromTable, $relationWithAliases, $type, $on)
    {
        $result = array();
        $field = '';
        $table = $fromTable;
        foreach ($relationWithAliases as $relationWithAlias) {
            $relation = $relationWithAlias->relation;
            $field = $field ? $field . '->' . $relation->getName() : $relation->getName();
            $modelJoin = new ModelJoin($field, $table, $relation, $relationWithAlias->alias, $type, $on);
            $table = $modelJoin->alias();
            $result[] = $modelJoin;
        }
        return $result;
    }

    public static function extractModelFromResult(Model $metaInstance, array $result, $offsetInResultSet)
    {
        $attributes = self::extractAttributes($metaInstance, $result, $offsetInResultSet);
        if (Arrays::any($attributes, Functions::notEmpty())) {
            return $metaInstance->newInstance($attributes);
        }
        return null;
    }

    public static function extractAttributes(Model $metaInstance, array $result, $offsetInResultSet)
    {
        $attributes = array();
        $offset = $offsetInResultSet;
        foreach ($metaInstance->getFields() as $field) {
            $attributes[$field] = $result[$offset];
            $offset++;
        }
        return $attributes;
    }
} 
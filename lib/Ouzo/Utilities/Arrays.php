<?php
namespace Ouzo\Utilities;

class Arrays
{
    public static function all(array $elements, $predicate)
    {
        foreach ($elements as $element) {
            if (!Functions::call($predicate, $element)) {
                return false;
            }
        }
        return true;
    }

    public static function toMap(array $elements, $keyFunction, $valueFunction = null)
    {
        if ($valueFunction == null) {
            $valueFunction = Functions::identity();
        }

        $keys = array_map($keyFunction, $elements);
        $values = array_map($valueFunction, $elements);
        return empty($keys) ? array() : array_combine($keys, $values);
    }

    static function flatten(array $array)
    {
        $return = array();
        array_walk_recursive($array, function ($a) use (&$return) {
            $return[] = $a;
        });
        return $return;
    }

    static public function findKeyByValue($array, $value)
    {
        if ($value === 0) {
            $value = '0';
        }
        foreach ($array as $key => $item) {
            if ($item == $value) {
                return $key;
            }
        }
        return FALSE;
    }

    public static function any($elements, $predicate)
    {
        foreach ($elements as $element) {
            if (Functions::call($predicate, $element)) {
                return true;
            }
        }
        return false;
    }

    public static function first($elements)
    {
        if (empty($elements)) {
            throw new \InvalidArgumentException('empty array');
        }
        $keys = array_keys($elements);
        return $elements[$keys[0]];
    }

    public static function last($elements)
    {
        if (empty($elements)) {
            throw new \InvalidArgumentException('empty array');
        }
        return end($elements);
    }

    public static function firstOrNull($object)
    {
        return empty($object) ? null : $object[0];
    }

    public static function getValue($array, $key, $default = null)
    {
        return isset($array[$key]) ? $array[$key] : $default;
    }

    public static function filterByAllowedKeys(array $map, $allowed)
    {
        if (is_array($allowed)) {
            $allowedKeys = $allowed;
        } else {
            $allowedKeys = array_filter(array_keys($map), $allowed);
        }
        return array_intersect_key($map, array_flip($allowedKeys));
    }

    public static function filterByKeys(array $map, $predicate)
    {
        $allowedKeys = array_filter(array_keys($map), $predicate);
        return self::filterByAllowedKeys($map, $allowedKeys);
    }

    public static function groupBy(array $elements, $keyFunction, $orderField = null)
    {
        $map = array();
        if (!empty($orderField)) {
            $elements = self::orderBy($elements, $orderField);
        }
        foreach ($elements as $element) {
            $key = Functions::call($keyFunction, $element);
            $map[$key][] = $element;
        }
        return $map;
    }

    public static function orderBy(array $elements, $orderField)
    {
        usort($elements, function ($a, $b) use ($orderField) {
            return $a->$orderField < $b->$orderField ? -1 : 1;
        });
        return $elements;
    }

    public static function mapKeys(array $elements, $function)
    {
        $newArray = array();
        foreach ($elements as $oldKey => $value) {
            $newKey = Functions::call($function, $oldKey);
            $newArray[$newKey] = $value;
        }
        return $newArray;
    }

    public static function map($array, $function)
    {
        return array_map($function, $array);
    }

    public static function filter($array, $function)
    {
        return array_filter($array, $function);
    }

    public static function toArray($elements)
    {
        return $elements ? is_array($elements) ? $elements : array($elements) : array();
    }

    public static function randElement($elements)
    {
        return $elements[array_rand($elements)];
    }
}
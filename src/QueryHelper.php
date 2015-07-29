<?php

namespace GraphAware\Neo4j\GraphUnit;

class QueryHelper
{
    public static function formatPropertyQuery($key)
    {
        $k = trim($key);

        return $k.': {'.$k.'}';
    }

    /**
     * @param $label
     *
     * @return string
     */
    public static function secureLabel($label)
    {
        $l = trim((string) $label);

        return '`'.$l.'`';
    }

    /**
     * @param array $labels
     *
     * @return array
     */
    public static function secureLabels(array $labels)
    {
        $l = [];
        foreach ($labels as $label) {
            $l[] = self::secureLabel($label);
        }

        return $l;
    }

    /**
     * @return string
     */
    public static function queryIdentifier()
    {
        return uniqid('_');
    }
}

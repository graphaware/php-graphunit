<?php

namespace GraphAware\Neo4j\GraphUnit;

class QueryHelper
{
    const RELATIONSHIP_INCOMING = 'IN';

    const RELATIONSHIP_OUTGOING = 'OUT';

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

    public static function formatMultipleLabelsForQuery(array $labels)
    {
        $q = '';
        foreach ($labels as $label) {
            $q .= ':'.self::secureLabel($label);
        }

        return $q;
    }

    public static function formatRelationshipQueryPart($type = null, $direction = null, array $properties = array())
    {
        switch ($direction) {
            case self::RELATIONSHIP_INCOMING:
                $start = '<-[';
                $end = ']-';
                break;
            case self::RELATIONSHIP_OUTGOING:
                $start = '-[';
                $end = ']->';
                break;
            default:
                $start = '-[';
                $end = ']-';
        }
        $q = $start;
        $q .= self::queryIdentifier();
        $q .= null !== $type ? ':'.$type : '';
        $q .= $end;

        return $q;
    }

    /**
     * @return string
     */
    public static function queryIdentifier()
    {
        return uniqid('_');
    }
}

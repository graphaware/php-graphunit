<?php

namespace GraphAware\Neo4j\GraphUnit\Constraint;

class SameGraph extends \PHPUnit_Framework_Constraint
{
    protected $actualGraph;

    protected $differenceString;

    public function __construct(array $actual)
    {
        $this->actualGraph = $actual;
    }

    public function matches($other)
    {
        return $this->compareGraphs($other);
    }

    public function toString()
    {
        return 'the expected graph is the same as the actual graph';
    }

    public function failureDescription($other)
    {
        return $this->toString();
    }

    private function compareGraphs(array $expected)
    {
        foreach ($expected['nodes'] as $node) {
            $found = false;
            foreach ($this->actualGraph['nodes'] as $anode) {
                if ($node['sha'] === $anode['sha']) {
                    $found = true;
                }
            }
            if (!$found) {
                return false;
            }
        }

        foreach ($expected['edges'] as $relationship) {
            $found = false;
            foreach ($this->actualGraph['edges'] as $rel) {
                if ($relationship['sha'] === $rel['sha']) {
                    $found = true;
                }
            }

            if (!$found) {
                return false;
            }
        }

        return $found;
    }
}
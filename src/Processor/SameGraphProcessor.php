<?php

namespace GraphAware\Neo4j\GraphUnit\Processor;

use Neoxygen\NeoClient\Client;
use Neoxygen\NeoClient\Formatter\Node;
use Neoxygen\NeoClient\Formatter\Relationship;
use Neoxygen\NeoClient\Formatter\Result;

class SameGraphProcessor
{
    public function assertSameGraph($expectedCypherGraph, Client $client)
    {
        $currentGraphQuery = 'MATCH (n) OPTIONAL MATCH (n)-[r]-() RETURN n,r';
        $currentGraphResult = $client->sendCypherQuery($currentGraphQuery)->getResult();
        $expectedResult = $this->getExpectedGraphResult($expectedCypherGraph, $client);

        return array($this->formatGraphResultAsComparableFormat($expectedResult), $this->formatGraphResultAsComparableFormat($currentGraphResult));

    }

    public function getExpectedGraphResult($cypher, Client $client)
    {
        $transaction = $client->createTransaction();
        $transaction->pushQuery('MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE r,n');
        $transaction->pushQuery($cypher);
        $expectedResult = $transaction->pushQuery('MATCH (n) OPTIONAL MATCH (n)-[r]-() RETURN n,r');
        $transaction->rollback();

        return $expectedResult;
    }

    public function formatGraphResultAsComparableFormat(Result $graph) {
        $nodes = [];
        $relationships = [];
        foreach ($graph->getNodes() as $node) {
            $n = $this->formatNodeAsComparableFormat($node);
            $nodes[$n['sha']] = $n;
        }
        foreach ($graph->getRelationships() as $rel) {
            $r = $this->formatRelationshipAsComparableFormat($rel);
            $relationships[$r['sha']] = $r;
        }
        ksort($nodes);
        ksort($relationships);
        $format = ['nodes' => array_values($nodes), 'edges' => array_values($relationships)];

        return $format;
    }

    public function formatNodeAsComparableFormat(Node $node)
    {
        $node = [
          'labels' => $node->getLabels(),
          'properties' => $node->getProperties()
        ];

        asort($node['labels']);
        ksort($node['properties']);
        $node['sha'] = implode('_', $node['labels']) . implode('_', $node['properties']);

        return $node;
    }

    public function formatRelationshipAsComparableFormat(Relationship $relationship)
    {
        $rel = [
            'type' => $relationship->getType(),
            'start' => $this->formatNodeAsComparableFormat($relationship->getStartNode()),
            'end' => $this->formatNodeAsComparableFormat($relationship->getEndNode()),
            'properties' => $relationship->getProperties()
        ];
        ksort($rel['properties']);
        $rel['sha'] = $rel['type'] . implode('_', $rel['properties']) . $rel['start']['sha'] . $rel['end']['sha'];

        return $rel;
    }

}
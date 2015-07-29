<?php

namespace GraphAware\Neo4j\GraphUnit\Tests;

use GraphAware\Neo4j\GraphUnit\Neo4jGraphDatabaseTestCase;

class GraphDatabaseTestCase extends Neo4jGraphDatabaseTestCase
{
    /**
     * @return \Neoxygen\NeoClient\Client
     */
    public function getConnection()
    {
        return $this->createConnection('localhost', 7474, 'neo4j', 'password');
    }
}
<?php

namespace GraphAware\Neo4j\GraphUnit\Tests\Integration;

use GraphAware\Neo4j\GraphUnit\Tests\GraphDatabaseTestCase;

class SimpleIntegrationTest extends GraphDatabaseTestCase
{
    public function setUp()
    {
        $this->resetDatabase();
    }

    public function testSimpleNodeCount()
    {
        $q = 'CREATE (n:SuperNode), (n2:SuperNode)';
        $this->getConnection()->sendCypherQuery($q);

        $this->assertNodesByLabelCount(2, 'SuperNode');
    }

    public function testSimpleNodeExist()
    {
        $q = 'CREATE (n:SuperNodeLabel)';
        $this->getConnection()->sendCypherQuery($q);

        $this->assertNodeWithLabelExist('SuperNodeLabel');
    }
}
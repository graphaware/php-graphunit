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

    public function testNodesCount()
    {
        $q = 'FOREACH (x in range(1, 10) | CREATE (:Node))';
        $this->getConnection()->sendCypherQuery($q);

        $this->assertNodesCount(10);
    }

    public function testRelationshipExist()
    {
        $q = 'CREATE (n:SuperNode)-[:OWNED_BY]->(u:SuperUser) RETURN u';
        $result = $this->getConnection()->sendCypherQuery($q)->getResult();
        $node = $result->get('u');

        $this->assertNodeHasRelationship($node, 'OWNED_BY');
    }

    /**
     * @graphState("(:User {id:1})-[:WORKS_AT]->(:Company {name:'GraphAware'})")
     */
    public function testPrepareDatabase()
    {
        $cypher = '(n:User {id:1, name:"chris"})-[:WORKS_AT]->(c:Company {name:"GraphAware"}),
        (john:User {name:"john"})-[:WORKS_AT]->(c)';
        $this->prepareDatabase($cypher);

        $this->assertNodesByLabelCount(2, 'User');
    }

    public function testAssertSame()
    {
        $state = 'CREATE (n:User {id:1, name:"chris"})-[:WORKS_AT]->(c:Company {name:"GraphAware"}),
        (john:User {name:"john"})-[:WORKS_AT]->(c)';
        $this->prepareDatabase($state);

        $state2 = 'CREATE (n:User {id:1, name:"chris"})-[:WORKS_AT]->(c:Company {name:"GraphAware"}),
        (john:User {name:"john"})-[:WORKS_AT]->(c)';

        $this->assertSameGraph($state2);
    }
}
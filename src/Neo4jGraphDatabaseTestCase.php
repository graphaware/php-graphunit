<?php

namespace GraphAware\Neo4j\GraphUnit;

use GraphAware\Neo4j\GraphUnit\Exception\GraphUnitRuntimeException;
use Neoxygen\NeoClient\Client;
use Neoxygen\NeoClient\ClientBuilder;
use Neoxygen\NeoClient\Exception\HttpException;
use Neoxygen\NeoClient\Formatter\Node;

abstract class Neo4jGraphDatabaseTestCase extends \PHPUnit_Framework_TestCase implements GraphDatabaseServiceInterface
{
    /**
     * @var null|\Neoxygen\NeoClient\Client
     */
    private $graphUnitDatabaseConnection;

    /**
     * Creates a connection to the database with the provided settings.
     *
     * @param $host
     * @param $port
     * @param null $user
     * @param null $password
     * @param bool $https
     *
     * @return \Neoxygen\NeoClient\Client
     */
    public function createConnection($host, $port, $user = null, $password = null, $https = false)
    {
        $scheme = false !== $https ? 'https' : 'http';
        $client = ClientBuilder::create()
          ->addConnection('default', $scheme, $host, (int) $port, true, $user, $password)
          ->setDefaultTimeout(10)
          ->setAutoFormatResponse(true)
          ->build();

        return $client;
    }

    /**
     * Assert that the nodes count with the given label matches with the database content.
     *
     * @param $count
     * @param $label
     */
    public function assertNodesByLabelCount($count, $label)
    {
        $identifier = QueryHelper::queryIdentifier();
        $label = ':'.QueryHelper::secureLabel($label);
        $q = 'MATCH ('.$identifier.$label.') RETURN '.$identifier;

        $result = $this->getCypherResult($q);

        $this->assertCount($count, $result->get($identifier));
    }

    public function assertNodeWithLabelExist($label)
    {
        $identifier = QueryHelper::queryIdentifier();
        $label = ':'.QueryHelper::secureLabel($label);
        $q = 'MATCH ('.$identifier.') RETURN '.$identifier;
        $result = $this->getCypherResult($q);

        $this->assertTrue($result->get($identifier) instanceof Node);
    }

    /**
     * Resets the database. Deletes all nodes and relationships.
     *
     * @throws GraphUnitRuntimeException When the connection to the database can not be executed
     */
    public function resetDatabase()
    {
        $q = 'MATCH (n) OPTIONAL MATCH (n)-[r]-() DELETE r,n';
        try {
            $this->getGraphUnitDatabaseConnection()->sendCypherQuery($q);
        } catch (HttpException $e) {
            throw new GraphUnitRuntimeException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @return \Neoxygen\NeoClient\Client
     */
    private function getGraphUnitDatabaseConnection()
    {
        if (null !== $this->graphUnitDatabaseConnection) {
            return $this->graphUnitDatabaseConnection;
        }

        $providedConnection = $this->getConnection();
        if (!$providedConnection instanceof Client) {
            throw new GraphUnitRuntimeException('The connection given by the "getConnection()" method is not an instance of Neoxygen\NeoClient\Client');
        }
        $this->graphUnitDatabaseConnection = $providedConnection;

        return $this->graphUnitDatabaseConnection;
    }

    /**
     * @param $query
     * @param array $parameters
     *
     * @return \Neoxygen\NeoClient\Formatter\Result
     */
    private function getCypherResult($query, array $parameters = array())
    {
        return $this->getGraphUnitDatabaseConnection()->sendCypherQuery($query, $parameters)->getResult();
    }
}

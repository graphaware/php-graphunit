<?php

namespace GraphAware\Neo4j\GraphUnit;

interface GraphDatabaseServiceInterface
{
    /**
     * @return \Neoxygen\NeoClient\Client
     */
    public function getConnection();
}

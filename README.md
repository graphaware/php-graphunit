## Neo4j GraphUnit for PHP

Neo4j Graph Database Assertion Tool.

### Usage

Require the library in your dev dependencies :

```bash
composer require --dev graphaware/neo4j-graphunit
```

Create your `BaseTestCase` that will extend `GraphAware\Neo4j\GraphUnit\Neo4jGraphDatabaseTestCase` and declare the 
`getConnection` method that should return a `Client` instance.

To make things easier, you can just call the `createConnection` method of the parent class to bootstrap the connection.

```php

namespace MyVendor\MyApp\Tests;

use GraphAware\Neo4j\GraphUnit\Neo4jGraphDatabaseTestCase;

class MyAppBaseTestCase extends Neo4jGraphDatabaseTestCase
{
	public function getConnection()
	{
		return $this->createConnection('localhost', 7474, 'neo4j', 'password');
	}
}
```

Your integration tests can now extend your new TestCase base class and start doing assertions on the database :

```php
namespace MyVendor\MyApp\Tests\Integration;

class MyIntegrationTest extends MyAppBaseTestCase
{
	public function setUp()
	{
		// Available method for cleaning the db
		$this->resetDatabase();
	}
		
	public function testNodesAreCreated()
	{
		$q = 'CREATE (n:TestNode)';
		$this->getConnection()->sendCypherQuery($q);
		
		$this->assertNodeWithLabelExist('TestNode');
	}
	
	public function testMultipleNodesAreCreated()
	{
		$q = 'CREATE (n:TestNode), (n2:TestNode);
		$this->getConnection()->sendCypherQuery($q);
		
		$this->assertNodesWithLabelCount(2, 'TestNode');
	}
}
```

## Asserting same graphs

The library can assert that the actual graph in the database matches a graph you pass as a Cypher pattern, example : 

```php

public function testMyGraphIsGood()
{
	$this->assertSameGraph("(:User {name:'John'})-[:WORKS_AT]->(c:Company {name:'Acme'})");
}

// Returns true if the actual graph is identical, otherwise show errors in PHPUnit

//1) GraphAware\Neo4j\GraphUnit\Tests\Integration\SimpleIntegrationTest::testAssertSame
//Failed asserting that the expected graph is the same as the actual graph.
```

---

### License

This library is released under the MIT License, please refer to the `LICENSE` file shipped with the library.

### Credits

GraphAware Limited
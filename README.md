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

### Assertions

#### assertNodeWithLabelExist

```php
public function testNodeWillExist()
	{
		$q = 'CREATE (n:TestNode)';
		$this->getConnection()->sendCypherQuery($q);
		
		$this->assertNodeWithLabelExist('TestNode');
	}
```

#### assertNodesWithLabelCount

```php
public function testMultipleNodesAreCreated()
	{
		$q = 'CREATE (n:TestNode), (n2:TestNode);
		$this->getConnection()->sendCypherQuery($q);
		
		$this->assertNodesWithLabelCount(2, 'TestNode');
	}
```

#### assertNodesCount

```php
public function testMultipleNodesAreCreated()
	{
		$q = 'CREATE (n:TestNode), (n2:TestNode);
		$this->getConnection()->sendCypherQuery($q);
		
		$this->assertNodesCount(2);
	}
```

#### assertNodeHasRelationship

```php
public function testMultipleNodesAreCreated()
	{
		$q = 'CREATE (n:User {name: "john"}), (n2:User {name: "mary"})-[:WORKS_AT]->(:Company {name:"Acme"}) RETURN n2;
		$result = $this->getConnection()->sendCypherQuery($q);
		
		$this->assertNodeHasRelationship($result->get('n2'), 'WORKS_AT', 'OUT');
	}
```

### Reseting database states

You can easily reset the database state (deleting all nodes, relationships, schema indexes and constraints) during your `setUp` events :

```php
public function setUp()
{
	$this->resetDatabase();
}
```

If you don't want to delete the schema indexes and constraints, just call the `emptyDatabase` method :

```php
public function setUp()
{
	$this->emptyDatabase();
}
```

### Preparing database states

You can just pass a Cypher pattern for preparing your database :

```php
public function setUp()
{
	$state = "(a:User {name:'Abed'})-[:WORKS_AT]->(:Company {name:'Vinelab'})
	(c:User {name:'Chris'})-[:WORKS_AT]->(:Company {name:'GraphAware'})
	(a)-[:FRIEND]->(c)";
	$this->prepareDatabase($state);
}
```

### Asserting same graphs

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
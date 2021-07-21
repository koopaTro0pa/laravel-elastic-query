<?php

namespace Greensight\LaravelElasticQuery\Tests\Functional\Raw\Debug;

use Greensight\LaravelElasticQuery\ElasticQuery;
use Greensight\LaravelElasticQuery\Tests\Functional\ElasticTestCase;
use Greensight\LaravelElasticQuery\Tests\Models\ProductsIndex;
use Greensight\LaravelElasticQuery\Tests\Seeds\ProductIndexSeeder;

class QueryLogTest extends ElasticTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        ProductIndexSeeder::run();
    }

    public function testLoggingDisabled(): void
    {
        $this->executeQuery();

        $this->assertCount(0, ElasticQuery::getQueryLog());
    }

    public function testLoggingEnabled(): void
    {
        ElasticQuery::enableQueryLog();

        $this->executeQuery();

        $this->assertCount(1, ElasticQuery::getQueryLog());
    }

    public function testActualTime(): void
    {
        ElasticQuery::enableQueryLog();

        $this->travel(-1)->days();
        $this->executeQuery();

        $record = ElasticQuery::getQueryLog()[0];

        $this->assertGreaterThan(now(), $record->timestamp);
    }

    private function executeQuery(): void
    {
        ProductsIndex::query()
            ->where('active', true)
            ->whereNot('code', 'tv')
            ->get();
    }
}

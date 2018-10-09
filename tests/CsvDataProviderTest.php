<?php

namespace gri3li\yii2csvdataprovider\test;

use gri3li\yii2csvdataprovider\CsvDataProvider;
use yii\base\InvalidConfigException;

class CsvDataProviderTest extends BaseCase
{
    private $filename = __DIR__ . '/data/test.csv';

    protected $data = [
        ['Name', 'Date'],
        ['name 1', 'Oct 3, 2018 12:56:03 PM'],
        ['name 2', 'Oct 3, 2018 12:56:03 PM'],
    ];

    public function testInit()
    {
        $e = null;
        try {
            new CsvDataProvider();
        } catch (InvalidConfigException $e) {
        }
        $this->assertInstanceOf(InvalidConfigException::class, $e);
    }

    public function testPrepareModels()
    {
        $provider = new CsvDataProvider(['filename' => $this->filename]);
        $models = $this->invoke($provider, 'prepareModels');
        $provider->setPagination(false);
        $this->assertEquals($models, $this->data);
    }

    public function testPrepareTotalCount()
    {
        $provider = new CsvDataProvider(['filename' => $this->filename]);
        $count = $this->invoke($provider, 'prepareTotalCount');
        $this->assertEquals($count, count($this->data));
    }

    public function testPrepareKeys()
    {
        $provider = new CsvDataProvider(['filename' => $this->filename]);
        $models = $this->invoke($provider, 'prepareModels');
        $keys = $this->invoke($provider, 'prepareKeys', [$models]);
        $this->assertEquals(count($keys), 3);
    }

    public function testCustomSeparated()
    {
        $provider = new CsvDataProvider([
            'filename' => __DIR__ . '/data/custom.csv',
            'csvDelimiter' => '|',
        ]);
        $models = $this->invoke($provider, 'prepareModels');
        $provider->setPagination(false);
        $this->assertEquals($models, $this->data);
    }
}

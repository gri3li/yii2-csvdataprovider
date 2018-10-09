<?php

namespace gri3li\yii2csvdataprovider;

use LimitIterator;
use SplFileObject;
use yii\base\InvalidConfigException;
use yii\data\BaseDataProvider;

/**
 * CsvDataProvider implements a data provider based on a csv.
 *
 * Example:
 *
 * ```php
 * $provider = new CsvDataProvider([
 *     'filename' => '/path/to/file.csv',
 *     'pagination' => [
 *         'pageSize' => 20,
 *     ],
 * ]);
 * ```
 *
 * @author Mikhail Gerasimov <migerasimoff@gmail.com>
 */
class CsvDataProvider extends BaseDataProvider
{
    /**
     * @var string name of the CSV file to read
     */
    public $filename;

    /**
     * @var string
     */
    public $csvDelimiter = ',';

    /**
     * @var string
     */
    public $csvEnclosure = '"';

    /**
     * @var string
     */
    public $csvEscape = '\\';

    /**
     * @var string|callable name of the key column or a callable returning it
     */
    public $key;

    /**
     * @var SplFileObject
     */
    protected $fileObject;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!file_exists($this->filename)) {
            throw new InvalidConfigException('filename does not exist');
        }
        $this->fileObject = new SplFileObject($this->filename);
        $this->fileObject->setCsvControl($this->csvDelimiter, $this->csvEnclosure, $this->csvEscape);
        $this->fileObject->setFlags(SplFileObject::READ_CSV | SplFileObject::READ_AHEAD | SplFileObject::SKIP_EMPTY);
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareModels()
    {
        $offset = 0;
        $limit = -1;
        $pagination = $this->getPagination();
        if ($pagination !== false) {
            $pagination->totalCount = $this->getTotalCount();
            $offset = $pagination->getOffset();
            $limit = $pagination->getLimit();
        }
        $models = [];
        foreach (new LimitIterator($this->fileObject, $offset, $limit) as $model) {
            $models[] = $model;
        }

        return $models;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareKeys($models)
    {
        if ($this->key === null) {
            return array_keys($models);
        }

        $keys = [];
        foreach ($models as $model) {
            $keys[] = is_string($this->key) ? $model[$this->key] : call_user_func($this->key, $model);
        }

        return $keys;
    }

    /**
     * {@inheritdoc}
     */
    protected function prepareTotalCount()
    {
        $count = 0;
        while (!$this->fileObject->eof()) {
            $this->fileObject->next();
            $count++;
        }

        return $count;
    }
}

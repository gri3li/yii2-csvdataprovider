
CsvDataProvider
===

This Yii2 extension implements a data provider based on a csv

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist gri3li/yii2-csvdataprovider
```

or add

```json
"gri3li/yii2-csvdataprovider": "*"
```

to the require section of your composer.json.


Usage
-----

```php
$provider = new CsvDataProvider([
    'filename' => '/path/to/file.csv',
    'pagination' => [
        'pageSize' => 20,
    ],
]);
```
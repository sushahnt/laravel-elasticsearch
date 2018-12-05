<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel attempts to take the pain out of development by easing common tasks used in the majority of web projects, such as:

## Elasticsearch

### Index a document

In elasticsearch-php, almost everything is configured by associative arrays.  The REST endpoint, document and optional parameters - everything is an associative array.

To index a document, we need to specify four pieces of information: index, type, id and a document body. This is done by
constructing an associative array of key:value pairs.  The request body is itself an associative array with key:value pairs
corresponding to the data in your document:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id',
    'body' => ['testField' => 'abc']
];

$response = $client->index($params);
print_r($response);
```

The response that you get back indicates the document was created in the index that you specified.  The response is an
associative array containing a decoded version of the JSON that Elasticsearch returns:

```php
Array
(
    [_index] => my_index
    [_type] => my_type
    [_id] => my_id
    [_version] => 1
    [created] => 1
)

```

### Get a document

Let's get the document that we just indexed.  This will simply return the document:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];

$response = $client->get($params);
print_r($response);
```

The response contains some metadata (index, type, etc.) as well as a `_source` field...this is the original document
that you sent to Elasticsearch.

```php
Array
(
    [_index] => my_index
    [_type] => my_type
    [_id] => my_id
    [_version] => 1
    [found] => 1
    [_source] => Array
        (
            [testField] => abc
        )

)
```

If you want to retrieve the `_source` field directly, there is the `getSource` method:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];

$source = $client->getSource($params);
doSomething($source);
```

### Search for a document

Searching is a hallmark of Elasticsearch, so let's perform a search.  We are going to use the Match query as a demonstration:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'body' => [
        'query' => [
            'match' => [
                'testField' => 'abc'
            ]
        ]
    ]
];

$response = $client->search($params);
print_r($response);
```

The response is a little different from the previous responses.  We see some metadata (`took`, `timed_out`, etc.) and
an array named `hits`.  This represents your search results.  Inside of `hits` is another array named `hits`, which contains
individual search results:

```php
Array
(
    [took] => 1
    [timed_out] =>
    [_shards] => Array
        (
            [total] => 5
            [successful] => 5
            [failed] => 0
        )

    [hits] => Array
        (
            [total] => 1
            [max_score] => 0.30685282
            [hits] => Array
                (
                    [0] => Array
                        (
                            [_index] => my_index
                            [_type] => my_type
                            [_id] => my_id
                            [_score] => 0.30685282
                            [_source] => Array
                                (
                                    [testField] => abc
                                )
                        )
                )
        )
)
```

### Delete a document

Alright, let's go ahead and delete the document that we added previously:

```php
$params = [
    'index' => 'my_index',
    'type' => 'my_type',
    'id' => 'my_id'
];

$response = $client->delete($params);
print_r($response);
```

You'll notice this is identical syntax to the `get` syntax.  The only difference is the operation: `delete` instead of
`get`.  The response will confirm the document was deleted:

```php
Array
(
    [found] => 1
    [_index] => my_index
    [_type] => my_type
    [_id] => my_id
    [_version] => 2
)
```


### Delete an index

Due to the dynamic nature of Elasticsearch, the first document we added automatically built an index with some default settings.  Let's delete that index because we want to specify our own settings later:

```php
$deleteParams = [
    'index' => 'my_index'
];
$response = $client->indices()->delete($deleteParams);
print_r($response);
```

The response:


```php
Array
(
    [acknowledged] => 1
)
```

### Create an index

Now that we are starting fresh (no data or index), let's add a new index with some custom settings:

```php
$params = [
    'index' => 'my_index',
    'body' => [
        'settings' => [
            'number_of_shards' => 2,
            'number_of_replicas' => 0
        ]
    ]
];

$response = $client->indices()->create($params);
print_r($response);
```

Elasticsearch will now create that index with your chosen settings, and return an acknowledgement:

```php
Array
(
    [acknowledged] => 1
)

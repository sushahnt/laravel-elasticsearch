<?php

namespace App\Console\Commands;
use App\Article;
use Elasticsearch\Client;
use Illuminate\Console\Command;

class ReindexArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:articles:rebuild';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reindexes Articles';
    /**
     * @var \Elasticsearch\Client
     */
    private $client;
    /**
     * Create a new command instance.
     *
     * @param \Elasticsearch\Client $client
     */
    public function __construct(Client $client)
    {
        parent::__construct();
        $this->client = $client;
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Indexing all articles. Might take a while...');
        $mapping = [
            'index' => 'articles',
            'body' => [
                'settings' => [
                    'analysis' => [
                        'analyzer' => [
                            'my_analyzer' => [
                                'tokenizer' => 'my_tokenizer',
                                'filter' => [
                                    'lowercase'
                                ]
                            ]
                        ],
                        'tokenizer' => [
                            'my_tokenizer' => [
                                'type' => 'ngram',
                                'min_gram' => 3,
                                'max_gram' => 3,
                                'token_chars' => [
                                    'letter',
                                    'digit'
                                ]
                            ]
                        ]
                    ]
                ],

                'mappings' => [
                    'articles' => [
                        '_source' => [
                            'enabled' => true
                        ],
                        'properties' => [
                            'title' => [
                                'type' => 'text',
                                'analyzer' => 'my_analyzer'
                            ],
                            'body' => [
                                'type' => 'text',
                                'analyzer' => 'english'
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $this->client->indices()->create($mapping);
        /** @var Article $article */
        foreach (Article::cursor() as $article) {
            $this->client->index([
                'index' => $article->getSearchIndex(),
                'type' => $article->getSearchType(),
                'id' => $article->id,
                'body' => $article->toSearchArray(),
            ]);
            // PHPUnit-style feedback
            $this->output->write('.');
        }
        $this->info("\nDone!");
    }
}

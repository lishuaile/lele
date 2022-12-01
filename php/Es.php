<?php
namespace App\server;
use Elasticsearch\ClientBuilder;
class Es
{
    private $client;
    //连接es
    public function __construct()
    {
        $host=['127.0.0.1'];
        $this->client=ClientBuilder::create()->setHosts($host)->build();
    }
    public function createIndex($index){
        $params = [
            'index' => $index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 2,
                    'number_of_replicas' => 0
                ],
                //中文分词
                'mappings'=>[
                    '_doc'=>[
                        'properties'=>[
                            'title'=>[
                                'type'=>'text',
                                'analyzer'=>'ik_max_word',
                                'search_analyzer'=>'ik_max_word',
                            ]
                        ]
                    ]
                ]
            ]
        ];
    return $this->client->indices()->create($params);

    }
    public function create($data,$index){
        $params = [
            'index' => $index,
            'id'    => $data['id'],
            'body'=>$data
        ];
        return $this->client->index($params);
    }
    public function search($title,$index){
        $params = [
            'index' => $index,
            'body'  => [
                'query' => [
                    'match' => [
                        'title' => [
                            'query'=>$title
                        ]
                    ]
                ],
                'highlight'=>[
                    'pre_tags'=>["<span> style='color: red'>"],
                    'post_tags'=>["</span>"],
                    'fields'=>[
                        'title'=>new \stdClass()
                    ]
                ]
            ],
        ];
         return $this->client->search($params)['hits']['hits'];

    }
}

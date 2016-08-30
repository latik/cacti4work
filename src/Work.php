<?php

/**
 * Created by PhpStorm.
 * User: o.latkovskyi
 * Date: 18.08.2016
 * Time: 18:02
 */
class Work
{
    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function getChildrenNodes($nodeId = 0)
    {
        $url = 'http://work.volia.net/w2/eth/tree/json.get_switches.php';

        $postdata = [
            'company' => $this->params['company'],
            'node' => $nodeId,
        ];

        $response = $this->request($url, $postdata, [
            $this->params['username'],
            $this->params['password'],
        ]);

        return json_decode($response['content'], true);
    }

    public function searchNode($query)
    {
        $url = 'http://work.volia.net/w2/eth/tree/json.search.php';

        $postdata = [
            'company' => $this->params['company'],
            'query' => $query,
        ];

        $response = $this->request($url, $postdata, [
            $this->params['username'],
            $this->params['password'],
        ]);
        //var_dump($response);die;
        return json_decode($response['content'], true);
    }

    /**
     * @param $url
     * @param null $postdata
     *
     * @return mixed
     */
    public static function request($url, $postdata = null, array $auth = [])
    {
        if (!empty($postdata)) {
            $data = http_build_query($postdata);
            $context_options = [
                'http' => [
                    'method' => "POST",
                    'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                        "Authorization: Basic " . base64_encode("{$auth[0]}:{$auth[1]}") . "\r\n" .
                        "Content-Length: " . strlen($data) . "\r\n",
                    'content' => $data,
                    'timeout' => 5
                ]
            ];
        } else {
            $context_options = [
                'http' => [
                    'method' => "GET",
                    'header' => "Accept-language: ru\r\n"
                ]
            ];
        }
        //var_dump($context_options);
        $response = file_get_contents($url, false, stream_context_create($context_options));

        if (false !== $response) {
            return [
                'statusCode' => $http_response_header[0],
                'header' => $http_response_header,
                'content' => $response
            ];
        }

        return null;
    }
}

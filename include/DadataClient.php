<?php

namespace Dadata;

class DadataClient {

    private $url,
            $token;

    public function __construct($url, $token, $secret) {
        $this->url = $url;
        $this->token = $token;
        $this->secret = $secret;
    }

    public function clean($data) {
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => array(
                    'Content-type: application/json',
                    'Authorization: Token ' . $this->token,
                    'X-Secret: ' . $this->secret
                ),
                'content' => json_encode($data),
            ),
        );
        $context = stream_context_create($options);
        $result = file_get_contents($this->url, false, $context);
        //$result = json_encode($result);
        //var_dump($result);
        return $result;
    }

}

?>
<?php
namespace Udiko\Cms\Inc;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class Ping
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }
    public function __invoke()
    {
        return $this->__toString();
    }

    public function __toString()
    {
        return '';
    }
    public function url($url)
    {
       return strlen($this->check($url))>0 ? '<span class="badge badge-outline-success">UP</span>' : '<span class="badge badge-outline-danger">DOWN</span>';
    }
    public function check($url){

        try {
            if(strlen($this->checkDomain($url))>0){
            $response = $this->client->request('GET', $url, ['timeout' => 5]);
            return $response->getStatusCode() == 200 ? 'OK' : null;
            }
        } catch (RequestException $e) {
            return false;
        }
    }
    public function checkDomain($domain)
    {
        $resolvedIp = gethostbyname($domain);
        return $resolvedIp === $domain ? null : 'ok';
    }
}

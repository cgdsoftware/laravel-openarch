<?php

namespace FamilyTree365\OpenArch;

use GuzzleHttp\Exception\GuzzleException;

class OpenArchService
{
    public $response;

    /**
     * @param string $_name
     * @param int $_page
     * @param int $_perPage
     * @return mixed
     * @throws OpenArchException
     */
    public function search(string $_name,int $_page, int $_perPage = 100): mixed
    {
        $client = new \GuzzleHttp\Client();

        try {
            $this->response = $client->request('POST', config('open-arch.records') . '/search.json', ['query' => [
                'name' => $_name,
                'number_show' => $_perPage, // 100
                'start' => ($_perPage * $_page) - $_perPage, // 2,
            ]]);
        } catch (GuzzleException $e) {
            throw new OpenArchException('Unable to connect to '. config('open-arch.records'));
        }

        if($this->verify()){
            return json_decode($this->response->getBody(), true);
        }
        throw new OpenArchException('Something went wrong', 500);

    }

    /**
     * @return bool
     * @throws OpenArchException
     */
    private function verify(): bool
    {
        if($this->response->getStatusCode() >= 200 && $this->response->getStatusCode() < 300){
            return true;
        }
        throw new OpenArchException('Request Invalidated from open arch', $this->response->getStatusCode());
    }

}

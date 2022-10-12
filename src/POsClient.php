<?php
declare(strict_types=1);

namespace GrupoAlus\POsService;

class POsClient {

    protected $_settings;

    protected function request(string $method, string $uri, array $headers = [], array $data = [], bool $jsonEncode = true){

        $url = $this->_settings["host"]."/".$this->_settings["code"].$uri;

        $request = new Request();
        $request
            ->SetMethod($method)
            ->SetURL($url)
            ->AddHeader('Authorization', $this->_settings['token']);

        foreach($headers as $name=>$value) {
            $request->AddHeader($name, $value);
        }

        $request->SetData($data);

        return $request->Execute();
    }

    public function __construct(string $host, string $code, string $token) {
        $this->_settings = [
            "host"  => $host,
            "code"  => $code,
            "token" => $token,
        ];
    }

    public function GetPos() {
        return $this->request('GET', '/orders/');
    }

    public function GetStatistics() {
        return $this->request('GET', "/orders/statistics/");
    }

    public function GetBuyerStatistics($buyer_reference) {
        $buyer_reference = urlencode($buyer_reference);
        return $this->request('GET', "/{$buyer_reference}/orders/statistics");
    }

    public function GetPo($id) {
        return $this->request('GET', "/orders/{$id}/");
    }

    public function GetPosByBuyer($buyer_reference) {
        $buyer_reference = urlencode($buyer_reference);
        return $this->request('GET', "/{$buyer_reference}/orders/");
    }

    public function CancelPo($id) {     
        return $this->request('PATCH', "/orders/{$id}/cancel/");
    }

    public function DeletePo($id) { 
        return $this->request('DELETE', "/orders/{$id}/");
    }

    public function GetReport() {
        return $this->request('GET', "/orders/report/");
    }

    public function NewPO($po) {
        return $this->request('POST', "/orders/", [], $po);
    }

    public function BatchUpdate($filePath) {
        if (!\file_exists($filePath)) {
            throw new \Exception("file \"{$filePath}\" not found");
        }
        $response = $this->request(
            'POST',
            "/orders/excel-batch/",
            [ 'Content-Type' => 'multipart/form-data' ],
            [ 'file' => new \CURLFile($filePath) ]
        );
        return $response;
    }

}

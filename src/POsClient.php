<?php

namespace GrupoAlus\POsService;

class POsClient {

    protected $_settings;

    public function __construct(string $host, string $code, string $token) {
        $this->_settings = [
            "host"  => $host,
            "code"  => $code,
            "token" => $token,
        ];
    }

    protected function _buildURL($uri) {
        $url = $this->_settings["host"]."/".$this->_settings["code"].$uri;
        return $url;
    }

    protected function get($uri, $jsonEncode = true) {
        $ch = curl_init($this->_buildURL($uri));

        $headers = [
            'Authorization: ' . $this->_settings['token'],
        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch($httpcode) {
            case 200:
                if ( $jsonEncode ) {
                    return \json_decode($result, true);
                } else {
                    return $result;
                }
            case 403:
                throw new \Exception('Unauthorized');
                break;
            case 404:
                throw new \Exception('Requested URI '.$uri.' Not Found');
                break;
            default:
                throw new \Exception('Unexpected status code ' . $httpcode . " :: " . $result);
        }

        curl_close($ch);

        return $result;
    }

    protected function patch($uri, $data = [], $jsonEncode = true) {
        $ch = curl_init($this->_buildURL($uri));

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Authorization: ' . $this->_settings['token'],
        ];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch($httpcode) {
            case 200:
                if ( $jsonEncode ) {
                    return \json_decode($result, true);
                } else {
                    return $result;
                }
            case 403:
                throw new \Exception('Unauthorized');
                break;
            case 404:
                throw new \Exception('Requested URI '.$uri.' Not Found');
                break;
            default:
                throw new \Exception('Unexpected status code ' . $httpcode . " :: " . $result);
        }

        curl_close($ch);

        return $result;
    }

    protected function delete($uri, $data = [], $jsonEncode = true) {
        $ch = curl_init($this->_buildURL($uri));

        $headers = [
            'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
            'Authorization: ' . $this->_settings['token'],
        ];
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch($httpcode) {
            case 200:
                if ( $jsonEncode ) {
                    return \json_decode($result, true);
                } else {
                    return $result;
                }
            case 403:
                throw new \Exception('Unauthorized');
                break;
            case 404:
                throw new \Exception('Requested URI '.$uri.' Not Found');
                break;
            default:
                throw new \Exception('Unexpected status code ' . $httpcode . " :: " . $result);
        }

        curl_close($ch);

        return $result;
    }

    protected function post($uri, $data = [], $jsonEncode = true) {
        $ch = curl_init($this->_buildURL($uri));

        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: ' . $this->_settings['token'],
        ];
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        switch($httpcode) {
            case 200:
                if ( $jsonEncode ) {
                    return \json_decode($result, true);
                } else {
                    return $result;
                }
            case 403:
                throw new \Exception('Unauthorized');
                break;
            case 404:
                throw new \Exception('Requested URI '.$uri.' Not Found');
                break;
            default:
                throw new \Exception('Unexpected status code ' . $httpcode . " :: " . $result);
        }

        curl_close($ch);

        return $result;
    }

    protected function postFile($uri, $filePath, $data = [], $jsonEncode = true) {

        $cf = new \CURLFile($filePath);
        $ch = curl_init($this->_buildURL($uri));

        $postData = array_merge(["file" => $cf], $data);

        $headers = [
            'Content-Type: multipart/form-data',
            'Authorization: ' . $this->_settings['token'],
        ];
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseHeaders = [];
        curl_setopt($ch, CURLOPT_HEADERFUNCTION,
            function($curl, $header) use (&$responseHeaders)
            {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) return $len;
                $headers[strtolower(trim($header[0]))][] = trim($header[1]);
                return $len;
            }
        );
        $result = curl_exec($ch);

        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $json = \json_decode($result, true);

        $response = [
            "status"        => $httpcode,
            "headers"       => $responseHeaders,
        ];
        if ($json != "" ) {
            $respose = array_merge($response, [
                "body"          => $json,
                "content-type"  => "application/json",
            ]);
        } else {
            $respose = array_merge($response, [
                "body"          => $result,
                "content-type"  => "text/plain",
            ]);
        }

        curl_close($ch);

        return $respose;
    }

    public function GetPos() {
        return $this->get("/orders/");
    }

    public function GetStatistics() {
        return $this->get("/orders/statistics/");
    }

    public function GetPo($id) {
        return $this->get("/orders/{$id}/");
    }

    public function GetPosByBuyer($buyer_reference) {
        return $this->get("/{$buyer_reference}/orders/");
    }

    public function CancelPo($id) {
        return $this->patch("/orders/{$id}/cancel/");
    }

    public function DeletePo($id) {
        return $this->delete("/orders/{$id}/");
    }

    public function GetReport() {
        return $this->get("/orders/report/",false);
    }

    public function NewPO($po) {
        return $this->post("/orders/", $po);
    }

    public function BatchUpdate($filePath) {
        return $this->postFile("/orders/excel-batch/", $filePath);
    }
}

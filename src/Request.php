<?php
declare(strict_types=1);

namespace GrupoAlus\POsService;

class Request {

    private $_url       = "";
    private $_method    = "GET";
    private $_headers   = [];
    private $_data      = [];
    private $_files     = [];

    public function __construct() {
        $this->_headers = [
            'content-type'=>'application/x-www-form-urlencoded; charset=UTF-8',
        ];
    }

    private function _buildheaders()
    {
        foreach ( $this->_headers as $name=>$value ) {
            $headers[] = $name . ": " . $value;
        }

        return $headers;
    }

    private function _isContentType(string $value) {
        if (!isset($this->_headers['content-type'])) {
            return false;
        }
        if ( str_contains($this->_headers['content-type'], $value) ) {
            return true;
        }
        return false;
    }

    private function _buildData()
    {
        $data = "";
        if ( $this->_isContentType('x-www-form-urlencoded') ) {
            $data = http_build_query($this->_data);
        } else {
            $data = $this->_data;
        }

        return $data;
    }

    public function AddHeader( string $name, string $value )
    {
        $this->_headers[strtolower($name)] = $value;
        return $this;
    }

    public function SetData(array $data )
    {
        $this->_data = $data;
        return $this;
    }

    public function SetMethod(string $method)
    {
        $this->_method = \trim(\strtoupper($method));
        return $this;
    }

    public function SetURL(string $url)
    {
        $this->_url = $url;
        return $this;
    }

    public function Execute()
    {
        $headers    = $this->_buildheaders();
        $data       = $this->_buildData();
        $ch         = \curl_init($this->_url);

        if ( \in_array($this->_method, [ 'POST', 'PATCH', 'DELETE' ])  ) {
            \curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->_method);
            \curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseHeaders = [];
        \curl_setopt($ch, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$responseHeaders) {
            $len = \strlen($header);
            $header = \explode(':', $header, 2);
            if (\count($header) < 2) return $len;
            $responseHeaders[\strtolower(\trim($header[0]))][] = \trim($header[1]);            
            return $len;
        });

        $body = \curl_exec($ch);
        $status = \curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $response = new Response($status, $responseHeaders, $body == FALSE ? "" : $body);

        return $response;
    }

}

<?php
declare(strict_types=1);

namespace GrupoAlus\POsService;

class Response {
    private $_body      = "";
    private $_status    = 200;
    private $_headers   = [];

    public function __construct(int $status, array $headers, string $body) {
        $this->_body    = $body;
        $this->_headers = $headers;
        $this->_status  = $status;
    }

    public function GetStringBody()
    {
        return $this->_body;
    }

    public function GetStatus()
    {
        return $this->_status;
    }

    public function GetJSON()
    {
        return \json_decode($this->_body, true);
    }

    public function GetHeader(string $name)
    {
        $value = "";
        $name = strtolower($name);

        if ( isset($this->_headers[$name]) ) {
            $value = $this->_headers[$name];
        }

        return $value;
    }

    public function IsOK()
    {
        return $this->_status >= 200 && $this->_status < 400 ? true : false;
    }

    public function IsUnauthorized()
    {
        return $this->_status == 401 ? true : false;
    }

    public function IsForbidden()
    {
        return $this->_status == 403 ? true : false;
    }

    public function IsNotFound()
    {
        return $this->_status == 404 ? true : false;
    }

    public function IsInternalServerError()
    {
        return $this->_status == 500 ? true : false;
    }

}

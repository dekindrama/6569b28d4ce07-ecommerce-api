<?php

namespace App\Exceptions\Commons;

use App\Exceptions\Commons\CommonException;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Response;

class BadRequestException extends CommonException
{
    private $_message, $_code, $_additional_info;
    public function __construct(
        string $message = 'bad request',
        object $additionalInfo = null,
    ) {
        $this->_message = $message;
        $this->_code = Response::HTTP_BAD_REQUEST;
        $this->_additional_info = $additionalInfo;
    }

    private function _generateErrorInfo() : object
    {
        //* collect error info
        $errorInfo = (object)[
            'code' => $this->_code,
            'message' => $this->_message,
            'additional_info' => $this->_additional_info,
        ];

        //* otherwise return object
        return $errorInfo;
    }

    function renderObject() : object
    {
        //* collect error info
        $errorInfo = $this->_generateErrorInfo();

        //* return object
        return $errorInfo;
    }

    function renderResponse() : Response
    {
        //* collect error info
        $errorInfo = $this->_generateErrorInfo();

        //* return response
        return ResponseHelper::generate(
            false,
            $errorInfo->message,
            $errorInfo->code,
            null,
            $errorInfo->additional_info,
        );
    }
}

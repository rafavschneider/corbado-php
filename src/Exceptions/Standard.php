<?php

namespace Corbado\Exceptions;

use Throwable;

class Standard extends \Exception {

    /**
     * @var int
     */
    private $httpStatusCode;

    /**
     * @var array
     */
    private $requestData;

    /**
     * @var float
     */
    private $runtime;

    /**
     * @var array
     */
    private $error;

    public function __construct(int $httpStatusCode, string $message, array $requestData, float $runtime, array $error)
    {
        parent::__construct($message, $httpStatusCode);

        $this->httpStatusCode = $httpStatusCode;
        $this->requestData = $requestData;
        $this->runtime = $runtime;
        $this->error = $error;
    }

    public function getHttpStatusCode(): int {
        return $this->httpStatusCode;
    }

    public function getRequestData(): array {
        return $this->requestData;
    }

    public function getRuntime(): float {
        return $this->runtime;
    }

    public function getError(): array {
        return $this->error;
    }
}
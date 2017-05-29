<?php

namespace Charcoal\Support\Http\Exception;

use Exception;
use LogicException;

// From PSR-7
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Exception thrown when an HTTP client error occurs (4xx)
 */
class BadRequestException extends LogicException
{
    /** @var RequestInterface */
    private $request;

    /** @var ResponseInterface */
    private $response;

    /**
     * Construct the exception.
     *
     * @param string                 $message  The Exception message to throw.
     * @param RequestInterface       $request  The HTTP request.
     * @param ResponseInterface|null $response The HTTP response.
     * @param Exception|null         $previous The previous exception used for the exception chaining.
     */
    public function __construct(
        $message,
        RequestInterface $request,
        ResponseInterface $response = null,
        Exception $previous = null
    ) {
        // Set the code of the exception if the response is set and not future.
        $code = $response ? $response->getStatusCode() : 0;

        parent::__construct($message, $code, $previous);

        $this->request  = $request;
        $this->response = $response;
    }

    /**
     * Get the request that caused the exception
     *
     * @return RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Get the associated response
     *
     * @return ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Check if a response was received
     *
     * @return boolean
     */
    public function hasResponse()
    {
        return $this->response !== null;
    }
}

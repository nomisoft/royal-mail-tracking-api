<?php

namespace Nomisoft\RoyalMailTrackingApi;

/**
 * Class RoyalMailException
 * @package Nomisoft\RoyalMailTrackingApi
 */
class RoyalMailException extends \Exception
{

    /**
     * @var
     */
    private $error;

    /**
     * @param string $errors
     * @param int $code
     * @param \Exception|null $previous
     */
    public function __construct($errors, $code = 0, \Exception $previous = null)
    {
        $this->error = $errors->error;
        parent::__construct($this->error->errorDescription, $code, $previous);
    }

    /**
     * @return mixed
     */
    public function getCause()
    {
        return $this->error->errorCause;
    }
}

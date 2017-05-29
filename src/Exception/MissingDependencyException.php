<?php

namespace Charcoal\Support\Exception;

use Exception;
use RuntimeException;

/**
 * Exception thrown if a class does not adhere to an implementation.
 *
 * This exception is useful for traits that depend on functionality from another trait.
 */
class MissingDependencyException extends RuntimeException
{
    /** @var string The class with the missing dependency. */
    private $dependent;

    /** @var string The missing class, trait, interface, or method. */
    private $dependency;

    /**
     * Construct the exception.
     *
     * @param string         $message    The Exception message to throw or dependent class.
     * @param string|null    $dependency The missing class, trait, or interface.
     * @param Exception|null $previous   The previous exception used for the exception chaining.
     */
    public function __construct(
        $message,
        $dependency = null,
        Exception $previous = null
    ) {
        $template = 'Missing dependency for class [%s]';

        if (is_object($message)) {
            $this->dependent = get_class($message);
            $message = '';
        } elseif (is_string($message) && class_exists($message)) {
            $this->dependent = $message;
            $message = '';
        }

        if (is_string($dependency)) {
            $this->dependency = $dependency;
            $template = 'Class [%s] must implement %s';

            if (trait_exists($dependency)) {
                $template = 'Class [%s] must use %s';
            } elseif (class_exists($dependency)) {
                $template = 'Class [%s] must extend %s';
            }
        }

        if ($template && $this->dependent) {
            $message = sprintf(
                $template,
                $this->dependent,
                $this->dependency
            );
        }

        parent::__construct($message, 0, $previous);
    }
}

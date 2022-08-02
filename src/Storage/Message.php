<?php

declare(strict_types=1);

namespace Laminas\Mail\Storage;

use Laminas\Stdlib\ErrorHandler;

use function array_combine;
use function file_get_contents;
use function is_resource;
use function ltrim;
use function stream_get_contents;

class Message extends Part implements Message\MessageInterface
{
    /**
     * flags for this message
     *
     * @var array
     */
    protected $flags = [];

    /**
     * Public constructor
     *
     * In addition to the parameters of Part::__construct() this constructor supports:
     * - file  filename or file handle of a file with raw message content
     * - flags array with flags for message, keys are ignored, use constants defined in \Laminas\Mail\Storage
     *
     * @param array $params
     * @throws Exception\RuntimeException
     */
    public function __construct(array $params)
    {
        if (isset($params['file'])) {
            if (! is_resource($params['file'])) {
                ErrorHandler::start();
                $params['raw'] = file_get_contents($params['file']);
                $error         = ErrorHandler::stop();
                if ($params['raw'] === false) {
                    throw new Exception\RuntimeException('could not open file', 0, $error);
                }
            } else {
                $params['raw'] = stream_get_contents($params['file']);
            }

            $params['raw'] = ltrim($params['raw']);
        }

        if (! empty($params['flags'])) {
            // set key and value to the same value for easy lookup
            $this->flags = array_combine($params['flags'], $params['flags']);
        }

        parent::__construct($params);
    }

    /**
     * return toplines as found after headers
     *
     * @return string toplines
     */
    public function getTopLines()
    {
        return $this->topLines;
    }

    /**
     * check if flag is set
     *
     * @param mixed $flag a flag name, use constants defined in \Laminas\Mail\Storage
     * @return bool true if set, otherwise false
     */
    public function hasFlag($flag)
    {
        return isset($this->flags[$flag]);
    }

    /**
     * get all set flags
     *
     * @return array array with flags, key and value are the same for easy lookup
     */
    public function getFlags()
    {
        return $this->flags;
    }
}

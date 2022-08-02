<?php

declare(strict_types=1);

namespace Laminas\Mail\Header;

use function preg_match;
use function strtolower;

class MimeVersion implements HeaderInterface
{
    /** @var string Version string */
    protected $version = '1.0';

    public static function fromString($headerLine)
    {
        [$name, $value] = GenericHeader::splitHeaderLine($headerLine);
        $value          = HeaderWrap::mimeDecodeValue($value);

        // check to ensure proper header type for this factory
        if (strtolower($name) !== 'mime-version') {
            throw new Exception\InvalidArgumentException('Invalid header line for MIME-Version string');
        }

        // Check for version, and set if found
        $header = new static();
        if (preg_match('/^(?P<version>\d+\.\d+)$/', $value, $matches)) {
            $header->setVersion($matches['version']);
        }

        return $header;
    }

    public function getFieldName()
    {
        return 'MIME-Version';
    }

    public function getFieldValue($format = HeaderInterface::FORMAT_RAW)
    {
        return $this->version;
    }

    public function setEncoding($encoding)
    {
        // This header must be always in US-ASCII
        return $this;
    }

    public function getEncoding()
    {
        return 'ASCII';
    }

    public function toString()
    {
        return 'MIME-Version: ' . $this->getFieldValue();
    }

    /**
     * Set the version string used in this header
     *
     * @param  string $version
     * @return MimeVersion
     */
    public function setVersion($version)
    {
        if (! preg_match('/^[1-9]\d*\.\d+$/', $version)) {
            throw new Exception\InvalidArgumentException('Invalid MIME-Version value detected');
        }
        $this->version = $version;
        return $this;
    }

    /**
     * Retrieve the version string for this header
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }
}

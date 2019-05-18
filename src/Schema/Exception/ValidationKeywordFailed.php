<?php

declare(strict_types=1);

namespace OpenAPIValidation\Schema\Exception;

use Throwable;

// Indicates that data was not matched against a schema's keyword
class ValidationKeywordFailed extends SchemaMismatch
{
    /** @var string */
    protected $keyword;

    /**
     * @param mixed $data
     *
     * @return ValidationKeywordFailed
     */
    public static function fromKeyword(string $keyword, $data, ?string $message = null, ?Throwable $prev = null) : self
    {
        $instance          = new self('Keyword validation failed: ' . $message, 0, $prev);
        $instance->keyword = $keyword;
        $instance->data    = $data;

        return $instance;
    }

    public function keyword() : string
    {
        return $this->keyword;
    }
}

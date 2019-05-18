<?php

declare(strict_types=1);

namespace OpenAPIValidationTests\Schema\Keywords;

use OpenAPIValidation\Schema\Exception\ValidationKeywordFailed;
use OpenAPIValidation\Schema\SchemaValidator;
use OpenAPIValidationTests\Schema\SchemaValidatorTest;

final class MinLengthTest extends SchemaValidatorTest
{
    public function testItValidatesMinLengthGreen() : void
    {
        $spec = <<<SPEC
schema:
  type: string
  minLength: 10
SPEC;

        $schema = $this->loadRawSchema($spec);
        $data   = 'abcde12345';

        (new SchemaValidator())->validate($data, $schema);
        $this->addToAssertionCount(1);
    }

    public function testItValidatesMinLengthRed() : void
    {
        $spec = <<<SPEC
schema:
  type: string
  minLength: 11
SPEC;

        $schema = $this->loadRawSchema($spec);
        $data   = 'abcde12345';

        try {
            (new SchemaValidator())->validate($data, $schema);
        } catch (ValidationKeywordFailed $e) {
            $this->assertEquals('minLength', $e->keyword());
        }
    }
}

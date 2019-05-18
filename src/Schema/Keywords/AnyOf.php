<?php

declare(strict_types=1);

namespace OpenAPIValidation\Schema\Keywords;

use cebe\openapi\spec\Schema as CebeSchema;
use OpenAPIValidation\Schema\BreadCrumb;
use OpenAPIValidation\Schema\Exception\InvalidSchema;
use OpenAPIValidation\Schema\Exception\SchemaMismatch;
use OpenAPIValidation\Schema\Exception\ValidationKeywordFailed;
use OpenAPIValidation\Schema\SchemaValidator;
use Respect\Validation\Exceptions\ExceptionInterface;
use Respect\Validation\Validator;

class AnyOf extends BaseKeyword
{
    /** @var int this can be Validator::VALIDATE_AS_REQUEST or Validator::VALIDATE_AS_RESPONSE */
    protected $validationDataType;
    /** @var BreadCrumb */
    protected $dataBreadCrumb;

    public function __construct(CebeSchema $parentSchema, int $type, BreadCrumb $breadCrumb)
    {
        parent::__construct($parentSchema);
        $this->validationDataType = $type;
        $this->dataBreadCrumb     = $breadCrumb;
    }

    /**
     * This keyword's value MUST be an array.  This array MUST have at least
     * one element.
     *
     * Inline or referenced schema MUST be of a Schema Object and not a standard JSON Schema.
     *
     * An instance validates successfully against this keyword if it
     * validates successfully against at least one schema defined by this
     * keyword's value.
     *
     * @param mixed        $data
     * @param CebeSchema[] $anyOf
     *
     * @throws ValidationKeywordFailed
     */
    public function validate($data, array $anyOf) : void
    {
        try {
            Validator::arrayVal()->assert($anyOf);
            Validator::each(Validator::instance(CebeSchema::class))->assert($anyOf);

            foreach ($anyOf as $schema) {
                $schemaValidator = new SchemaValidator($this->validationDataType);
                try {
                    $schemaValidator->validate($data, $schema, $this->dataBreadCrumb);

                    return;
                } catch (SchemaMismatch $e) {
                    // that did not match... its ok
                }
            }

            throw ValidationKeywordFailed::fromKeyword('anyOf', $data, 'Data must match at least one schema');
        } catch (ExceptionInterface $e) {
            throw InvalidSchema::becauseDefensiveSchemaValidationFailed($e);
        }
    }
}

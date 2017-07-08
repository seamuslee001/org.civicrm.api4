<?php

namespace Civi\Test\API\V4\Spec;

use Civi\API\V4\Service\Spec\FieldSpec;
use Civi\API\V4\Service\Spec\RequestSpec;
use Civi\Test\API\V4\UnitTestCase;

/**
 * @group headless
 */
class RequestSpecTest extends UnitTestCase {

  public function testRequiredFieldFetching() {
    $spec = new RequestSpec('Contact', 'get');
    $requiredField = new FieldSpec('name');
    $requiredField->setRequired(TRUE);
    $nonRequiredField = new FieldSpec('age', 'Integer');
    $nonRequiredField->setRequired(FALSE);
    $spec->addFieldSpec($requiredField);
    $spec->addFieldSpec($nonRequiredField);

    $requiredFields = $spec->getRequiredFields();

    $this->assertCount(1, $requiredFields);
    $this->assertEquals('name', array_shift($requiredFields)->getName());
  }

  public function testGettingFieldNames() {
    $spec = new RequestSpec('Contact', 'get');
    $nameField = new FieldSpec('name');
    $ageField = new FieldSpec('age', 'Integer');
    $spec->addFieldSpec($nameField);
    $spec->addFieldSpec($ageField);

    $fieldNames = $spec->getFieldNames();

    $this->assertCount(2, $fieldNames);
    $this->assertEquals(array('name', 'age'), $fieldNames);
  }
}
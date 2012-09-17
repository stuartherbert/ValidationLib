<?php

/**
 * Copyright (c) 2012-present Stuart Herbert.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the names of the copyright holders nor the names of the
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package     Phix_Project
 * @subpackage  ValidationLib
 * @author      Stuart Herbert <stuart@stuartherbert.com>
 * @copyright   2012-present Stuart Herbert. www.stuartherbert.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phix-project.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ValidationLib;

use PHPUnit_Framework_TestCase;

class ValidationResultTest extends PHPUnit_Framework_TestCase
{
        public function testCanCreate()
        {
                // ----------------------------------------------------------------
                // perform the change

                $obj = new ValidationResult(true);

                // ----------------------------------------------------------------
                // test the results

                $this->assertTrue($obj instanceof ValidationResult);
        }

        public function testProvideValueUnderTestWhenCreating()
        {
                // ----------------------------------------------------------------
                // setup your test

                $expectedData = 10;

                // ----------------------------------------------------------------
                // perform the change

                $obj = new ValidationResult($expectedData);

                // ----------------------------------------------------------------
                // test the results

                $actualData = $obj->getValue();
                $this->assertEquals($expectedData, $actualData);
        }

        public function testStartsWithNoErrorMessages()
        {
                // ----------------------------------------------------------------
                // perform the change

                $obj = new ValidationResult(10);

                // ----------------------------------------------------------------
                // test the results

                $this->assertFalse($obj->hasErrors());
        }

        public function testStartsAsValidResult()
        {
                // ----------------------------------------------------------------
                // perform the change

                $obj = new ValidationResult(10);

                // ----------------------------------------------------------------
                // test the results

                $this->assertTrue($obj->isValid());
        }

        public function testGetErrorsReturnsAnArray()
        {
                $obj = new ValidationResult(10);
                $this->assertTrue(is_array($obj->getErrors()));
        }

        public function testHasErrorsReturnsFalseWhenThereAreNoErrors()
        {
                $obj = new ValidationResult(10);
                $this->assertTrue(is_array($obj->getErrors()));
                $this->assertEquals(0, count($obj->getErrors()));

                // the real test
                $this->assertFalse($obj->hasErrors());
        }

        public function testHasErrorsReturnsTrueWhenThereAreErrors()
        {
                // create an object with errors
                $obj = new ValidationResult(10);
                $obj->addError("It all went a bit Pete Tong");
                $this->assertFalse($obj->isValid());
                $this->assertTrue(is_array($obj->getErrors()));
                $this->assertEquals(1, count($obj->getErrors()));

                // the real test
                $this->assertTrue($obj->hasErrors());
        }

        public function testCanExpandExtraTokensInTheErrorMessage()
        {
                // setup
                $obj = new ValidationResult(1);
                $expected = "'1' is not in the range 10-20";

                // change
                $obj->addError("'%value%' is not in the range %min%-%max%", array (
                        "%min%" => 10,
                        "%max%" => 20
                ));

                // test
                $this->assertFalse($obj->isValid());
                $this->assertTrue($obj->hasErrors());
                $errors = $obj->getErrors();
                $this->assertEquals(1, count($errors));
                $this->assertEquals($expected, $errors[0]);
        }

        public function testCanThrowExceptionOnError()
        {
                // setup
                $obj = new ValidationResult(1);
                $expectedError = "Bad request: Validation failed with error(s): '1' is not in the range 10-20";

                // change
                $obj->addError("'%value%' is not in the range %min%-%max%", array (
                        "%min%" => 10,
                        "%max%" => 20
                ));

                // test
                $caughtException = false;
                try {
                        $obj->requireNoErrors();
                }
                catch (E4xx_ValidationFailedException $e) {
                        $caughtException = true;
                        $actualError = $e->getMessage();
                }

                $this->assertTrue($caughtException);
                $this->assertEquals($expectedError, $actualError);
        }

        public function testDoesNotThrowExceptionWhenNoError()
        {
                // setup
                $obj = new ValidationResult(1);

                // test
                $caughtException = false;
                try {
                        $obj->requireNoErrors();
                }
                catch (E4xx_ValidationFailedException $e) {
                        $caughtException = true;
                }

                $this->assertFalse($caughtException);
        }

        public function testErrorMessagesCanContainObjectAsValue()
        {
                // setup
                $obj = new ValidationResult(new TestValidation);
                $msg = "'%value%' is an error";
                $expectedMsgs = array("'fred' is an error");

                // test
                $obj->addError($msg);

                // results
                $actualMsgs = $obj->getErrors();
                $this->assertEquals($expectedMsgs, $actualMsgs);
        }

        public function testErrorMessagesCanContainTrueAsValue()
        {
                // setup
                $obj = new ValidationResult(true);
                $msg = "'%value%' is an error";
                $expectedMsgs = array("'TRUE' is an error");

                // test
                $obj->addError($msg);

                // results
                $actualMsgs = $obj->getErrors();
                $this->assertEquals($expectedMsgs, $actualMsgs);
        }

        public function testErrorMessagesCanContainFalseAsValue()
        {
                // setup
                $obj = new ValidationResult(false);
                $msg = "'%value%' is an error";
                $expectedMsgs = array("'FALSE' is an error");

                // test
                $obj->addError($msg);

                // results
                $actualMsgs = $obj->getErrors();
                $this->assertEquals($expectedMsgs, $actualMsgs);
        }

        public function testErrorMessagesCanContainIntegerAsValue()
        {
                // setup
                $obj = new ValidationResult(1);
                $msg = "'%value%' is an error";
                $expectedMsgs = array("'1' is an error");

                // test
                $obj->addError($msg);

                // results
                $actualMsgs = $obj->getErrors();
                $this->assertEquals($expectedMsgs, $actualMsgs);
        }

        public function testErrorMessagesCanContainDoubleAsValue()
        {
                // setup
                $obj = new ValidationResult(3.14);
                $msg = "'%value%' is an error";
                $expectedMsgs = array("'3.14' is an error");

                // test
                $obj->addError($msg);

                // results
                $actualMsgs = $obj->getErrors();
                $this->assertEquals($expectedMsgs, $actualMsgs);
        }

        public function testErrorMessagesCanContainStringAsValue()
        {
                // setup
                $obj = new ValidationResult('fred');
                $msg = "'%value%' is an error";
                $expectedMsgs = array("'fred' is an error");

                // test
                $obj->addError($msg);

                // results
                $actualMsgs = $obj->getErrors();
                $this->assertEquals($expectedMsgs, $actualMsgs);
        }

        public function testErrorMessagesCanCopeWithResourceAsValue()
        {
                // setup
                $filename = './phix-test.php';
                $fp = fopen($filename, 'a');
                $obj = new ValidationResult($fp);
                $msg = "'%value%' is an error";
                $expectedMsgs = array("'[unsupported]' is an error");

                // test
                $obj->addError($msg);
                fclose($fp);
                unlink($filename);

                // results
                $actualMsgs = $obj->getErrors();
                $this->assertEquals($expectedMsgs, $actualMsgs);
        }

        public function testCanConvertValidResultToString()
        {
                // setup
                $obj = new ValidationResult(1);
                $expected = 'Data is valid';

                // results
                $actual = (string) $obj;
                $this->assertEquals($expected, $actual);
        }

        public function testCanConvertInvalidResultToString()
        {
                // setup
                $obj = new ValidationResult('fred');
                $msg = "'%value%' is an error";
                $obj->addError($msg);
                $obj->addError($msg);
                $expected = "Validation failed with error(s): 'fred' is an error" . PHP_EOL . "'fred' is an error";

                // results
                $actual = (string) $obj;
                $this->assertEquals($expected, $actual);
        }

}

class TestValidation
{
        public function __toString()
        {
                return "fred";
        }
}


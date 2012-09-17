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

use stdClass;

class ValidationListTest extends ValidationLibTestBase
{
        public function testCanCreate()
        {
                // ----------------------------------------------------------------
                // perform the change

                $obj = new ValidationList();

                // ----------------------------------------------------------------
                // test the results

                $this->assertTrue($obj instanceof ValidationList);
        }

        public function testStartsWithNoValidators()
        {
                // ----------------------------------------------------------------
                // perform the change

                $obj = new Validationlist();

                // ----------------------------------------------------------------
                // test the results

                $this->assertFalse($obj->hasValidators());
                $this->assertEquals(0, count($obj->getValidators()));
        }

        public function testCanAddValidatorToTheList()
        {
                // ----------------------------------------------------------------
                // setup your test

                $obj = new ValidationList();
                $this->assertFalse($obj->hasValidators());
                $this->assertEquals(0, count($obj->getValidators()));

                // ----------------------------------------------------------------
                // perform the change

                $obj->addValidator(new Type_MustBeInteger());

                // ----------------------------------------------------------------
                // test the results

                $this->assertEquals(1, count($obj->getValidators()));
        }

        public function testCanGetTheListOfValidators()
        {
                // ----------------------------------------------------------------
                // setup your test

                $expectedList = array(
                        new Type_MustBeInteger(),
                        new Type_MustBeString()
                );

                $obj = new ValidationList();
                foreach ($expectedList as $validator)
                {
                        $obj->addValidator($validator);
                }

                // ----------------------------------------------------------------
                // perform the change

                $actualList = $obj->getValidators();

                // ----------------------------------------------------------------
                // test the results

                $this->assertEquals($expectedList, $actualList);
        }

        public function testCanSetTheListOfValidators()
        {
                // ----------------------------------------------------------------
                // setup your test

                $expectedList = array(
                        new Type_MustBeInteger(),
                        new Type_MustBeString()
                );

                $obj = new ValidationList();

                // ----------------------------------------------------------------
                // perform the change

                $obj->setValidators($expectedList);

                // ----------------------------------------------------------------
                // test the results

                $actualList = $obj->getValidators();
                $this->assertEquals($expectedList, $actualList);
        }

        public function testSettingTheListOfValidatorsOverridesExistingList()
        {
                // ----------------------------------------------------------------
                // setup your test

                $expectedList = array(
                        new Type_MustBeInteger(),
                        new Type_MustBeString()
                );

                $obj = new ValidationList();
                $obj->addValidator(new Type_MustBeString);

                // ----------------------------------------------------------------
                // perform the change

                $obj->setValidators($expectedList);

                // ----------------------------------------------------------------
                // test the results

                $actualList = $obj->getValidators();
                $this->assertEquals($expectedList, $actualList);
        }

        public function testCanSetTheListOfValidatorsOnlyUsingValidators()
        {
                // ----------------------------------------------------------------
                // setup your test

                $obj = new ValidationList();

                $list1 = array(1,2,3,4,5,6,7,8,9,10);
                $list2 = array(new stdClass);

                $expectedMsgs = array
                (
                        "list1" => "Bad request: Bad validator: (integer) 1; must be object that implements Validator",
                        "list2" => "Bad request: Bad validator: stdClass; must be object that implements Validator"
                );

                $actualMsgs = array();

                // ----------------------------------------------------------------
                // perform the change

                try
                {
                        $obj->setValidators($list1);
                }
                catch (E4xx_BadValidatorException $e)
                {
                        $actualMsgs['list1'] = $e->getMessage();
                }

                try
                {
                        $obj->setValidators($list2);
                }
                catch (E4xx_BadValidatorException $e)
                {
                        $actualMsgs['list2'] = $e->getMessage();
                }

                // ----------------------------------------------------------------
                // test the results

                $this->assertEquals($expectedMsgs, $actualMsgs);
        }

        public function testCanLoopThroughValidators()
        {
                // ----------------------------------------------------------------
                // setup your test

                $obj = new ValidationList();
                $obj->addValidator(new Type_MustBeInteger)
                    ->addValidator(new Type_MustBeIntegerInRange(10, 20));

                // ----------------------------------------------------------------
                // perform the change

                $result = $obj->validate(15);

                // ----------------------------------------------------------------
                // test the results

                $this->assertTrue($result->isValid());
        }

        public function testStopsAtFirstFailedValidation()
        {
                // ----------------------------------------------------------------
                // setup your test

                $obj = new ValidationList();
                $obj->addValidator(new Type_MustBeInteger)
                    ->addValidator(new Type_MustBeIntegerInRange(10, 20))
                    ->addValidator(new Type_MustBeString);

                $expectedErrors = array(
                        "'5' is not in the range 10 to 20"
                );

                // ----------------------------------------------------------------
                // perform the change

                $result = $obj->validate(5);

                // ----------------------------------------------------------------
                // test the results

                $this->assertFalse($result->isValid());

                $actualErrors = $result->getErrors();
                $this->assertEquals($expectedErrors, $actualErrors);
        }

        public function testNext()
        {
                // ----------------------------------------------------------------
                // setup your test

                // explain your test setup here if needed ...

                // ----------------------------------------------------------------
                // perform the change
                //
                // explain your test here if needed ...

                // ----------------------------------------------------------------
                // test the results
                //
                // explain what you expect to have happened
        }
}

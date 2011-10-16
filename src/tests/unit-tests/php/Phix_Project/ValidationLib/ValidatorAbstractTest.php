<?php

/**
 * Copyright (c) 2011 Stuart Herbert.
 * Copyright (c) 2010 Gradwell dot com Ltd.
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
 * @copyright   2011 Stuart Herbert. www.stuartherbert.com
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phix-project.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ValidationLib;

class TestValidatorAbstract extends ValidatorAbstract
{
        const MSG_NOTVALIDSTRING = "'%value%' (of type %type%) is not a valid string";
        const MSG_OUTOFRANGE = "'%value%' is not in the range %min%-%max%";

        public function isValid($value)
        {
                $this->setValue($value);

                $isValid = true;

                // these are the only types that convert to being a string
                if (!is_int($value) && !is_float($value) && !is_string($value))
                {
                        $this->addMessage(self::MSG_NOTVALIDSTRING);
                        return false;
                }

                // special case for testing extra tokens
                if (is_int($value) && ($value < 10 || $value > 20))
                {
                        $this->addMessage(self::MSG_OUTOFRANGE, array('%min%' => 10, '%max%' => 20));
                        return false;
                }
                return true;
        }
}

class ValidatorAbstractTest extends ValidationLibTestBase
{
        /**
         *
         * @return TestValidatorAbstract
         */
        protected function setupObj()
        {
                // setup the test
                $obj = new TestValidatorAbstract();
                $messages = $obj->getMessages();
                $this->assertTrue(is_array($messages));
                $this->assertEquals(0, count($messages));

                return $obj;
        }

        public function testResetsErrorMessagesWhenIsValueSet()
        {
                // create an object with errors
                $obj = $this->setupObj();
                $this->assertFalse($obj->isValid(null));
                $this->assertTrue(is_array($obj->getMessages()));
                $this->assertEquals(1, count($obj->getMessages()));

                // now, let's validate something else
                $this->assertTrue($obj->isValid(11));
                $this->assertTrue(is_array($obj->getMessages()));
                $this->assertEquals(0, count($obj->getMessages()));
        }

        public function testGetMessagesReturnsAnArray()
        {
                $obj = $this->setupObj();
                $this->assertTrue(is_array($obj->getMessages()));
        }

        public function testHasMessagesReturnsFalseWhenThereAreNoMessages()
        {
                $obj = $this->setupObj();
                $this->assertTrue(is_array($obj->getMessages()));
                $this->assertEquals(0, count($obj->getMessages()));

                // the real test
                $this->assertFalse($obj->hasMessages());
        }

        public function testHasMessagesReturnsTrueWhenThereAreMessages()
        {
                // create an object with errors
                $obj = $this->setupObj();
                $this->assertFalse($obj->isValid(null));
                $this->assertTrue(is_array($obj->getMessages()));
                $this->assertEquals(1, count($obj->getMessages()));

                // the real test
                $this->assertTrue($obj->hasMessages());
        }
        
        public function testCanExpandExtraTokensInTheErrorMessage()
        {
                // setup
                $obj = $this->setupObj();
                $expected = "'1' is not in the range 10-20";
                
                // change
                $isValid = $obj->isValid(1);
                                
                // test
                $this->assertFalse($isValid);                
                $this->assertTrue($obj->hasMessages());
                $messages = $obj->getMessages();
                $this->assertEquals(1, count($messages));
                $this->assertEquals($expected, $messages[0]);
        }
        
        public function testCanObtainValueThatWasValidated()
        {
                // setup
                $obj = $this->setupObj();
                $this->assertNull($obj->getValue());
                
                // change
                $obj->isValid(11);
                
                // test
                $this->assertEquals(11, $obj->getValue());
        }
}

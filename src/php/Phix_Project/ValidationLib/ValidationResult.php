<?php

/**
 * Copyright (c) 2012 Stuart Herbert.
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
 * @copyright   2012 Stuart Herbert. www.stuartherbert.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phix-project.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ValidationLib;

use ReflectionObject;

class ValidationResult
{
        /**
         * The value that the validator has been asked to check
         * @var mixed
         */
        protected $value = null;

        /**
         * An array of the error messages generated during validation
         * @var array an array of error messages when validation fails
         */
        protected $errorMsgs = array();

        /**
         * constructor. remembers the value that is under test
         *
         * @param mixed $value the value that is under test
         */
        public function __construct($value)
        {
                $this->setValue($value);
        }

        /**
         * Get the value that this validator has been asked to check
         * @return mixed
         */
        public function getValue()
        {
                return $this->value;
        }

        /**
         * Set the value that the validator has been asked to check
         *
         * @param mixed $value
         */
        protected function setValue($value)
        {
                $this->value = $value;
        }

        /**
         * was the data valid?
         *
         * @return boolean true if there were no errors, false otherwise
         */
        public function isValid()
        {
                if (count($this->errorMsgs) == 0)
                {
                        return true;
                }

                return false;
        }

        /**
         * throw an exception if the validation failed
         *
         * @return void
         */
        public function requireNoErrors()
        {
                if (!$this->isValid())
                {
                        throw new E4xx_ValidationFailedException($this);
                }
        }

        /**
         * Get the array of error messages
         *
         * If validation was successful, the array will be empty, but it
         * will still be an array rather than a boolean
         *
         * @return array
         */
        public function getErrors()
        {
                return $this->errorMsgs;
        }

        /**
         * Do we have any error messages?
         *
         * @return boolean
         */
        public function hasErrors()
        {
                if (count($this->errorMsgs) > 0)
                {
                        return true;
                }

                return false;
        }

        /**
         * Add another error message to the pile
         *
         * @param string $msg the format string to use
         * @param array $extraTokens any additional tokens you want
         *              expanded in the $msg
         */
        public function addError($msg, $extraTokens = array())
        {
                // work out how to format the error message
                $type = gettype($this->value);

                // special cases
                if (is_callable($this->value))
                {
                        $value = '(callable)';
                }

                if (!isset($value))
                {
                        switch ($type)
                        {
                                case 'object':
                                        $refObj = new ReflectionObject($this->value);
                                        if ($refObj->hasMethod('__toString'))
                                        {
                                                $value = (string) $this->value;
                                        }
                                        else
                                        {
                                                $value = '(object) ' .get_class($this->value);
                                        }
                                        break;

                                case 'boolean':
                                        if ($this->value)
                                        {
                                                $value = 'TRUE';
                                        }
                                        else
                                        {
                                                $value = 'FALSE';
                                        }
                                        break;

                                case 'integer':
                                case 'float':
                                case 'double':
                                case 'string':
                                        $value = $this->value;
                                        break;

                                case 'NULL':
                                        $value = 'NULL';
                                        break;

                                case 'array':
                                        $value = '(array)';
                                        break;

                                default:
                                        $value = '[unsupported]';
                                        break;

                        }
                }

                $searchList = array_keys($extraTokens);
                $searchList[] = '%value%';
                $searchList[] = '%type%';

                $replaceList = array_values($extraTokens);
                $replaceList[] = $value;
                $replaceList[] = $type;

                $this->errorMsgs[] = str_replace($searchList, $replaceList, $msg);
        }

        /**
         * return the validation results as a string for ease of printing out
         * @return string
         */
        public function __toString()
        {
                if ($this->isValid())
                {
                        return "Data is valid";
                }

                // if we get here, we have errors to return
                return "Validation failed with error(s): " . implode(PHP_EOL, $this->errorMsgs);
        }
}

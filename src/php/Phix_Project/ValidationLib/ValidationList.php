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

class ValidationList implements Validator
{
        /**
         * a list of the validators to call, one at a time
         *
         * @var array
         */
        protected $list = array();

        /**
         * add a validator, and an optional callback, to the list
         *
         * @param Validator $validator the validator to add to the list
         */
        public function addValidator(Validator $validator)
        {
                $this->list[] = $validator;

                // fluent interface
                return $this;
        }

        /**
         * get the list of validators
         *
         * @return array
         */
        public function getValidators()
        {
                return $this->list;
        }

        /**
         * do we have any validators?
         * @return boolean true if there are validators in the list, false otherwise
         */
        public function hasValidators()
        {
                return (count($this->list) > 0);
        }

        /**
         * replace any validators with a list provided by the caller
         *
         * @param array(Validator) $validators a list of validators to use
         */
        public function setValidators($validators)
        {
                // make sure they are all validators
                foreach ($validators as $validator)
                {
                        // make sure it is an object
                        if (!is_object($validator))
                        {
                                throw new E4xx_BadValidatorException('(' . gettype($validator) . ') ' . $validator);
                        }

                        // make sure it implements our interface
                        if (!$validator instanceof Validator)
                        {
                                throw new E4xx_BadValidatorException(get_class($validator));
                        }
                }

                // if we get here, then validation has been successful
                $this->list = $validators;

                // fluent interface
                return $this;
        }

        /**
         * use the validators in the list to validate a piece of data
         *
         * @param  mixed            $data   the data to validate
         * @param  ValidationResult $result the object to save the results into
         * @return ValidationResult
         */
        public function validate($data, ValidationResult $result = null)
        {
                // make sure we have a result object
                if ($result === null)
                {
                        $result = new ValidationResult($data);
                }

                // loop through all of the validators in the list
                foreach ($this->list as $listItem)
                {
                        // does the data pass the validator?
                        $listItem->validate($data, $result);

                        // did it pass?
                        if (!$result->isValid())
                        {
                                // no, so bail early
                                return $result;
                        }
                }

                // all done
                return $result;
        }
}
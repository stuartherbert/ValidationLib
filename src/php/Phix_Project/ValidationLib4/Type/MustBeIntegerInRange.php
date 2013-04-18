<?php

/**
 * Copyright (c) 2011-present Stuart Herbert.
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
 * @subpackage  ValidationLib4
 * @author      Stuart Herbert <stuart@stuartherbert.com>
 * @copyright   2011-present Stuart Herbert. www.stuartherbert.com
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phix-project.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ValidationLib4;

class Type_MustBeIntegerInRange implements Validator
{
        const MSG_NOTVALIDINTEGER = "'%value%' (of type %type%) is not a valid integer";
        const MSG_NOTINRANGE      = "'%value%' is not in the range %min% to %max%";

        public function __construct($min, $max)
        {
                $this->minValue = $min;
                $this->maxValue = $max;
        }

        public function validate($value, ValidationResult $result = null)
        {
                if ($result === null)
                {
                        $result = new ValidationResult($value);
                }

                if (!is_int($value) && !is_string($value))
                {
                        $result->addError(static::MSG_NOTVALIDINTEGER);
                        return $result;
                }

                // does the (probably string) get through the filter too?
                if ($value != filter_var($value, FILTER_SANITIZE_NUMBER_INT))
                {
                        $result->addError(static::MSG_NOTVALIDINTEGER);
                        return $result;
                }

                // okay, so we have an integer
                // is it in range?
                if ($value < $this->minValue || $value > $this->maxValue)
                {
                        $result->addError(static::MSG_NOTINRANGE, array('%min%' => $this->minValue, '%max%' => $this->maxValue));
                        return $result;
                }

                // if we get here, then we like the value
                return $result;
        }
}

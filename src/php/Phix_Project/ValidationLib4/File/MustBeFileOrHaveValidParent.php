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
 * @subpackage  ValidationLib4
 * @author      Stuart Herbert <stuart@stuartherbert.com>
 * @copyright   2012-present Stuart Herbert. www.stuartherbert.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phix-project.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ValidationLib4;

class File_MustBeFileOrHaveValidParent implements Validator
{
        const MSG_PARENTNOTFOUND = "'%value%'s parent folder does not exist on disk at all";
        const MSG_CANNOTBEPATH   = "'%value%' is a folder; must be a new or existing file";

        public function validate($value, ValidationResult $result = null)
        {
                if ($result === null)
                {
                        $result = new ValidationResult($value);
                }

                // is it actually a folder?
                //
                // we have to test this first, as file_exists() returns
                // TRUE for folders
                if (is_dir($value))
                {
                        // that's no good - we need something that can
                        // be a new file
                        $result->addError(static::MSG_CANNOTBEPATH);
                        return $result;
                }

                // does it already exist as a file?
                if (file_exists($value))
                {
                        // yes - that's fine
                        return $result;
                }

                // does its parent exist?
                $parent = dirname($value);
                if (!is_dir($parent))
                {
                        $result->addError(static::MSG_PARENTNOTFOUND);
                        return $result;
                }

                // all done
                return $result;
        }
}

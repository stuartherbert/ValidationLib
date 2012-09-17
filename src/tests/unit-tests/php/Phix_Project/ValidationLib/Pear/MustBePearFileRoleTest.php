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
 * @subpackage  ValidationLib
 * @author      Stuart Herbert <stuart@stuartherbert.com>
 * @copyright   2011-present Stuart Herbert. www.stuartherbert.com
 * @copyright   2010 Gradwell dot com Ltd. www.gradwell.com
 * @license     http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link        http://www.phix-project.org
 * @version     @@PACKAGE_VERSION@@
 */

namespace Phix_Project\ValidationLib;

class Pear_MustBePearFileRoleTest extends ValidationLibTestBase
{
        /**
         *
         * @return MustBePearFileRole
         */
        protected function setupObj()
        {
                // setup the test
                $obj = new Pear_MustBePearFileRole();

                return $obj;
        }

        public function testRoleMustBeAString()
        {
                $obj = $this->setupObj();

                // strings are valid
                $this->doTestIsValid($obj, "bin");

                // comma-separated strings are also valid
                $this->doTestIsValid($obj, "bin,doc");

                // arrays are not valid
                $this->doTestIsNotValid($obj, array(), array("'' is not a valid comma-separated set of PEAR file roles"));

                // integers are not valid
                $this->doTestIsNotValid($obj, 5, array("'5' is not a valid comma-separated set of PEAR file roles"));

                // floats are not valid
                $this->doTestIsNotValid($obj, 5.5, array("'5.5' is not a valid comma-separated set of PEAR file roles"));

                // null is not valid
                $this->doTestIsNotValid($obj, null, array("'' is not a valid comma-separated set of PEAR file roles"));
        }

        public function testRoleCanBeBinary()
        {
                $obj = $this->setupObj();

                $this->doTestIsValid($obj, "bin");
                $this->doTestIsValid($obj, "bin,data");
                $this->doTestIsValid($obj, "data,bin");
                $this->doTestIsValid($obj, "data,bin,php");
        }

        public function testRoleCanBeData()
        {
                $obj = $this->setupObj();

                $this->doTestIsValid($obj, "data");
                $this->doTestIsValid($obj, "data,bin");
                $this->doTestIsValid($obj, "bin,data");
                $this->doTestIsValid($obj, "bin,data,doc");
        }

        public function testRoleCanBeDoc()
        {
                $obj = $this->setupObj();

                $this->doTestIsValid($obj, "doc");
                $this->doTestIsValid($obj, "doc,bin");
                $this->doTestIsValid($obj, "bin,doc");
                $this->doTestIsValid($obj, "bin,doc,data");
        }

        public function testRoleCanBePhp()
        {
                $obj = $this->setupObj();

                $this->doTestIsValid($obj, "php");
                $this->doTestIsValid($obj, "php,bin");
                $this->doTestIsValid($obj, "bin,php");
                $this->doTestIsValid($obj, "bin,php,data");
        }

        public function testRoleCanBeTest()
        {
                $obj = $this->setupObj();

                $this->doTestIsValid($obj, "test");
                $this->doTestIsValid($obj, "test,bin");
                $this->doTestIsValid($obj, "bin,test");
                $this->doTestIsValid($obj, "bin,test,data");
        }

        public function testRoleCanBeWww()
        {
                $obj = $this->setupObj();

                $this->doTestIsValid($obj, "www");
                $this->doTestIsValid($obj, "www,bin");
                $this->doTestIsValid($obj, "bin,www");
                $this->doTestIsValid($obj, "bin,www,data");
        }

        public function testRolesMustBeValid()
        {
                $obj = $this->setupObj();

                $this->doTestIsNotValid($obj, "java", array("'java' is not a valid PEAR file role"));
                $this->doTestIsNotValid($obj, "bin,java", array("'bin,java' is not a valid comma-separated set of PEAR file roles"));
        }
}

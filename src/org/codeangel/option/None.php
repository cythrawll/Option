<?php
/**
 * CodeAngel Option class
 *
 * LICENSE
 *
 * Copyright (c) 2013, Chad Minick
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the following conditions are met:
 *
 *    * Redistributions of source code must retain the above copyright notice, this list of conditions and the following disclaimer.
 *    * Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR
 * CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
 * PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF
 * ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Option
 * @copyright Copyright (c) 2013 Chad Minick
 * @version 1.0M1-SNAPSHOT
 * @license BSD
 */
namespace org\codeangel\option;

/**
 * Represents an empty Option
 */
class None extends Option {

    /**
     * Always returns true
     *
     * @return bool Returns if this Option is empty or not
     */
    public function isEmpty() {
        return true;
    }

    /**
     * Always throws NoSuchElementException
     *
     * @throws NoSuchElementException always throws exception
     */
    public function get() {
        throw new NoSuchElementException("Cannot get from a None");
    }

    /**
     * Returns empty string
     *
     * @return string string representation of this object
     */
    public function __toString() {
        return "";
    }
}

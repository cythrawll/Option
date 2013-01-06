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
 * Abstract class that represents either Some or None
 */
abstract class Option {
    /**
     * Returns the value if Some, NoSuchElementException if None
     * Should not be used directly..  favor getOrElse or getOrNull
     *
     * @return mixed Returns value if Some, throws NoSuchElementException if None
     * @throws org\codeangel\option\NoSuchElementException Throws if None.
     */
    abstract public function get();

    /**
     * Tests if the Option is empty or not
     *
     * @return bool Returns True if None, false if Some
     */
    abstract public function isEmpty();

    /**
     * Gets the value of the Option or the default if None.
     *
     * @param mixed $default The default to return if this is a representation of None
     * @return mixed Returns either the value of Some or the default if None.
     */
    public function getOrElse($default) {
        if($this->isEmpty()) {
            return $default;
        } else {
            return $this->get();
        }
    }

    /**
     * This should be used if you are using an interface that expects null
     * rather than Option `someFuncThatExpectsNull($option->getOrNull());`
     *
     * @return mixed Returns either the value of Some or Null if None
     */
    public function getOrNull() {
        if($this->isEmpty()) {
            return null;
        } else {
            return $this->get();
        }
    }

    /**
     * Tests if Option  has a value or not.
     *
     * @return bool Returns true if Some, false if None;
     */
    public function isDefined() {
        return !$this->isEmpty();
    }

    /**
     * Creates an Option... Warning takes argument in as reference.  So a variable must be passed here.
     *
     * @param mixed $obj Takes in an argument and decides whether to create Some or None
     * @return None|Some
     */
    public static function create(&$obj) {
        if(isset($obj) && $obj != null) {
            return new Some($obj);
        } else {
            return new None;
        }
    }

    /**
     * Applies the $callback to the value of the option.
     *
     * @param callback $callback callback to run on this Option, the value will be given as the argument to the callback
     * @return mixed|None Returns the value of the callback applied to the value of Option, or None if Option is empty.
     */
    public function map($callback) {
        if($this->isEmpty()) {
            return new None;
        }

        if(is_callable($callback)) {
            return call_user_func($callback, $this->get());
        } else {
            return new None;
        }
    }
}

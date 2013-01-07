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
     * @throws NoSuchElementException Throws if None.
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

    /**
     * Gets keys from an array and returns the result as an Option
     *
     * @param array $array array to get values from
     * @param mixed $key,... Unlimited keys to fetch off of the array
     * @returns None|Some
     */
    static public function createArray(&$array,$key) {
        if(!is_array($array)) {
            return self::create($array);
        }
        if(array_key_exists($key, $array)) {
            $keys = func_get_args();
            array_shift($keys);
            array_shift($keys);
            if(count($keys) > 0) {
                //because the first argument needs to be passed by ref
                $args = self::createArgsList($array[$key], $keys);
                return call_user_func_array(array('self', 'createArray'), $args);
            } else {
                return self::create($array[$key]);
            }
        }
        return new None;
    }

    /**
     * fetches Option from $_GET supervariable
     *
     * @param mixed $var,... keys that will be applied to $_GET
     * @return Some|None returns Option for $_GET supervariable
     */
    public static function _get($var) {
        $fargs = func_get_args();
        return call_user_func_array(array('self', 'createArray'), self::createArgsList($_GET, $fargs));
    }

    /**
     * fetches Option from $_POST supervariable
     *
     * @param mixed $var,... keys that will be applied to $_POST
     * @return Some|None returns Option for $_POST supervariable
     */
    public static function _post($var) {
        $fargs = func_get_args();
        return call_user_func_array(array('self', 'createArray'), self::createArgsList($_POST, $fargs));
    }

    /**
     * fetches Option from $_SERVER supervariable
     *
     * @param mixed $var,... keys that will be applied to $_SERVER
     * @return Some|None returns Option for $_SERVER supervariable
     */
    public static function _server($var) {
        $fargs = func_get_args();
        return call_user_func_array(array('self', 'createArray'), self::createArgsList($_SERVER, $fargs));
    }

    /**
     * fetches Option from $_GET or $_POST supervariable
     * $_GET is prefered if there is a conflict
     *
     * @param mixed $var,... keys that will be applied to $_GET or $_POST
     * @return Some|None returns Option for $_GET or $_POST supervariable
     */
    public static function _request($var) {
        $args = func_get_args();
        $res = call_user_func_array(array('self', '_get'), $args);
        if($res instanceof None) {
            return call_user_func_array(array('self', '_post'), $args);
        }
        return $res;
    }

    /**
     * fetches Option from $_GET or $_POST supervariable
     * $_POST is prefered if there is a conflict
     *
     * @param mixed $var,... keys that will be applied to $_GET or $_POST
     * @return Some|None returns Option for $_GET or $_POST supervariable
     */
    public static function _requestp($var) {
        $args = func_get_args();
        $res = call_user_func_array(array('self', '_post'), $args);
        if($res instanceof None) {
            return call_user_func_array(array('self', '_get'), $args);
        }
        return $res;
    }

    /**
     * Creates an argument list with references first parameter
     *
     * @param mixed $array first thing that needs to be reference
     * @param $fargs array of argumetns that is the rest
     * @return array array that's appropriate for call_user_func_array
     */
    private static function createArgsList(&$array, $fargs) {
        $args[0] = &$array;
        foreach($fargs as $f) {
            $args[] = $f;
        }
        return $args;
    }
}

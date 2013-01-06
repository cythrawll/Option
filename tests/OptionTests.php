<?php
/**
 * CodeAngel Options class
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
 */
require 'bootstrap.php';

use org\codeangel\option\Option;
use org\codeangel\option\Some;
use org\codeangel\option\None;
use org\codeangel\option\NoSuchElementException;

class OptionTests extends PHPUnit_Framework_TestCase {
    public function testSome() {
        $var = 'hello';
        $some = Option::create($var);
        $this->assertEquals('hello', $some->get());
        $this->assertEquals('hello', $some->getOrElse('hi'));
        $this->assertEquals('hello', $some->getOrNull());
        $this->assertTrue($some->isDefined());
        $this->assertFalse($some->isEmpty());
    }

    /**
     * @expectedException org\codeangel\option\NoSuchElementException
     */
    public function testNonExistingVariableEx() {
        $none = Option::create($nonexistence);
        $val= $none->get();
    }

    /**
     * @expectedException org\codeangel\option\NoSuchElementException
     */
    public function testNullVariableEx() {
        $nope = null;
        $none = Option::create($nope);
        $val = $none->get();
    }

    public function testNoneExisting() {
        $none = Option::create($nonexisting);
        $this->assertEquals('hi', $none->getOrElse('hi'));
        $this->assertNull($none->getOrNull());
        $this->assertFalse($none->isDefined());
        $this->assertTrue($none->isEmpty());
    }

    public function testNull() {
        $null = null;
        $none = Option::create($null);
        $this->assertEquals('hi', $none->getOrElse('hi'));
        $this->assertNull($none->getOrNull());
        $this->assertFalse($none->isDefined());
        $this->assertTrue($none->isEmpty());
    }

    public function testMapBasicFunction() {
        $some = new Some('hi');
        $this->assertEquals('HI', $some->map('strtoupper'));
    }

    public function testMapBasicFunctionNone() {
        $none = new None;
        $this->assertInstanceOf('org\codeangel\option\None', $none->map('strtoupper'));
    }

    public function testMapCallback() {
        $some = new Some('HELLO');
        $this->assertEquals('hello', $some->map(array($this, 'someCallback')));
    }

    public function testMapCallbackNone() {
        $none = new None;
        $this->assertInstanceOf('org\codeangel\option\None', $none->map(array($this, 'someCallback')));
    }

    public function testMapClosure() {
        $some = new Some('HELLO');
        $this->assertEquals('hello', $some->map(function($arg) { return strtolower($arg);}));
    }

    public function testMapClosureNone() {
        $none = new None;
        $this->assertInstanceOf('org\codeangel\option\None', $none->map(array($this, 'someCallback')));
    }

    public function testBreakReference() {
        $s = 'hi';
        $some = Option::create($s);
        $s = 'hello';
        $this->assertEquals('hi', $some->get());
    }

    public function someCallback($what) {
        return strtolower($what);
    }
}

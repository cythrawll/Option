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

    public function testInvalidCallback() {
        $s = 'hi';
        $some = Option::create($s);
        $this->assertInstanceOf('org\codeangel\option\None', $some->map('flipittyfloppityfloop'));
    }

    public function testArrayCreate() {
        $s['hello'][0] = 'hi';
        $some = Option::createArray($s, 'hello', 0);
        $this->assertEquals('hi', $some->get());

        $some = Option::createArray($s, 'hello');
        $this->assertEquals(array('hi'), $some->get());

        $none = Option::createArray($s, 'hello', 1);
        $this->assertInstanceOf('org\codeangel\option\None', $none);

        $none = Option::createArray($t, 'hello', 2);
        $this->assertInstanceOf('org\codeangel\option\None', $none);
    }

    public function testSuperGet() {
        $_GET['foo'] = "hello";
        $_GET['bar'][0] = "world";
        $some = Option::_get('foo');
        $this->assertEquals('hello', $some->get());
        $some = Option::_get('bar', 0);
        $this->assertEquals('world', $some->get());

        $none = Option::_get('non');
        $this->assertInstanceOf('org\codeangel\option\None', $none);

        $none = Option::_get('bar', 1);
        $this->assertInstanceOf('org\codeangel\option\None', $none);
        $_GET = array();
    }

    public function testSuperPost() {
        $_POST['foo'] = "hello";
        $_POST['bar'][0] = "world";

        $some = Option::_post('foo');
        $this->assertEquals('hello', $some->get());
        $some = Option::_post('bar', 0);
        $this->assertEquals('world', $some->get());

        $none = Option::_post('non');
        $this->assertInstanceOf('org\codeangel\option\None', $none);

        $none = Option::_post('bar', 1);
        $this->assertInstanceOf('org\codeangel\option\None', $none);

        $_POST = array();
    }

    public function testSuperServer() {
        $_SERVER['foo'] = "hello";
        $_SERVER['bar'][0] = "world";

        $some = Option::_server('foo');
        $this->assertEquals('hello', $some->get());
        $some = Option::_server('bar', 0);
        $this->assertEquals('world', $some->get());

        $none = Option::_server('non');
        $this->assertInstanceOf('org\codeangel\option\None', $none);

        $none = Option::_server('bar', 1);
        $this->assertInstanceOf('org\codeangel\option\None', $none);

        unset($_SERVER['foo']);
        unset($_SERVER['bar']);
    }

    public function testRequest() {
        $_GET['foo'] = "hello";
        $_GET['bar'][0] = "world";
        $_GET['banana'] = 'bam';
        $_POST['foo'] = "hellop";
        $_POST['bar'][0] = "worldp";
        $_POST['bar'][1] = 'wassup';

        $some = Option::_request('foo');
        $this->assertEquals('hello', $some->get());
        $some = Option::_request('bar', 0);
        $this->assertEquals('world', $some->get());

        $some = Option::_request('bar', 1);
        $this->assertEquals("wassup", $some->get());

        $some = Option::_requestp('foo');
        $this->assertEquals('hellop', $some->get());
        $some = Option::_requestp('bar', 0);
        $this->assertEquals('worldp', $some->get());

        $some = Option::_requestp('banana');
        $this->assertEquals('bam', $some->get());

        $none = Option::_request('non');
        $this->assertInstanceOf('org\codeangel\option\None', $none);

        $none = Option::_requestp('non');
        $this->assertInstanceOf('org\codeangel\option\None', $none);

        $_GET = array();
        $_POST = array();
    }

    public function testEcho() {
        $none = new None;
        echo $none;
        $some = new Some("");
        echo $some;
    }

    public function testHasLength() {
        $emptyArray = new Some(array());
        $fullArray = new Some(array(1,2,3));
        $string = new Some("hi");
        $emptyString = new Some("");
        $zero = new Some(0);
        $zeroString = new Some('0');
        $object = new Some(new None);
        $true = new Some(true);
        $false = new Some(false);
        $none = new None;

        $this->assertFalse($emptyArray->hasLength());
        $this->assertTrue($fullArray->hasLength());
        $this->assertTrue($string->hasLength());
        $this->assertFalse($emptyString->hasLength());
        $this->assertTrue($zero->hasLength());
        $this->assertTrue($zeroString->hasLength());
        $this->assertTrue($object->hasLength());
        $this->assertTrue($true->hasLength());
        $this->assertTrue($false->hasLength());
        $this->assertFalse($none->hasLength());
    }

    public function someCallback($what) {
        return strtolower($what);
    }
}

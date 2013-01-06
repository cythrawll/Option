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
 *
 */
require_once 'bootstrap.php';

use org\codeangel\option\Some;
use org\codeangel\option\None;

class StudentRegistry {
    static function getStudent($name) {
        $chance = rand(0,1);
        if($chance === 1) {
            return new None;
        } else {
            return new Some(new Student);
        }
    }
}

class Student {
    function getCourse($name) {
        $chance = rand(0,1);
        if($chance === 1) {
            return new None;
        } else {
            return new Some(new Course);
        }
    }
}

class Course {
    function getGrade() {
        return new Some(70);
    }
}

var_dump(StudentRegistry::getStudent('Chad')
    ->map(function($student) { return $student->getCourse('Math'); })
    ->map(function($course) { return $course->getGrade(); })
    ->map(function($grade) {return new Some($grade > 60);})->getOrElse(false));
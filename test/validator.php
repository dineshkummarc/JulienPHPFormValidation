<?php

require_once('../../simpletest/unit_tester.php');
require_once('../../simpletest/web_tester.php');
require_once('../../simpletest/reporter.php');
require_once('../validator.php');

class ValidatorTest extends WebTestCase {

  function setUp()
  {
    $this->get('http://localhost/julienPHPFormValidation/index.php');
    $this->assertText('Hello');
  }

  function testSubmit() 
  {
    $this->click('Submit');
    $this->assertText('form sent!');
  }

  function testPasswordNotTheSame()
  {
    $this->setField('first_name', 'Julien');
    $this->setField('last_name', 'Desrosiers');
    $this->setField('username', 'jdesrosiers');
    $this->setField('password', '23r29fh2f2');
    $this->setField('confirm_password', 'not the same!');
    $this->click('Submit');
    $this->assertPattern('/Password Confirmation must be the same as Password/');
    $this->assertNoPattern('/First Name must not be empty/');
  }

  function testNotEmptyError()
  {
    $this->setField('first_name', '');
    $this->click('Submit');
    $this->assertPattern('/First Name must not be empty/');
  }

  function testEmailNotValid()
  {
    $this->setField('first_name', 'Julien');
    $this->setField('last_name', 'Desrosiers');
    $this->setField('email', 'not_an_email');
    $this->setField('username', 'jdesrosiers');
    $this->setField('password', '23r29fh2f2');
    $this->setField('confirm_password', '23r29fh2f2');
    $this->click('Submit');
    $this->assertPattern('/Email must have a valid format./');
  }

  function testTermsNotAccepted()
  {
    $this->setField('first_name', 'Julien');
    $this->setField('last_name', 'Desrosiers');
    $this->setField('email', 'test@gmail.com');
    $this->setField('username', 'jdesrosiers');
    $this->setField('password', '23r29fh2f2');
    $this->setField('confirm_password', '23r29fh2f2');
    $this->click('Submit');
    $this->assertPattern('/Accept Terms must be checked./');
  }

  function testPasswordMinLenght()
  {
    $this->setField('password', '123');
    $this->setField('confirm_password', '123');
    $this->click('Submit');
    $this->assertPattern('/Password must be at least 5 characters./');
  }

  function testPasswordMaxLenght()
  {
    $this->setField('username', '123456789012345678901');
    $this->click('Submit');
    $this->assertPattern('/User name must be less than 20 characters./');
  }
}


$test = new ValidatorTest();
$test->run(new HtmlReporter());

Fixtures
========

Setting up fixtures in testing of an ATK14 application is pretty simple.

Place fixture files into this directory. A fixture file must have a .yml suffix and it must contain an YAML document.

Fixture file
------------

Take a look at an example fixture file.

    # file: test/fixtures/users.yml
    
    john:
      login: "john.doe"
      password: "JohnNeverDoe"
      name: "John Doe"

    samantha:
      login: "samantha.doe"
      password: "fowl.no.more"
      name: "Samantha Doe"

It's supposed, that the model class User exists and it is the TableRecord descendant.

Usage in tests
--------------

In a specific test case you can use @fixture annotation and the given fixture will be automatically loaded.

    <?php
    /**
     * file: tests/model/tc_user.php
     *
     * @fixture users
     */
    class TcUser extends TcBase {

      function test(){
        $john = $this->users["john"];
        $samantha = $this->users["samantha"];

        $this->assertEquals("John Doe",$john->getName());
        $this->assertEquals("Samantha Doe",$samantha->getName());

        $this->assertTrue((bool)User::Login("john.doe","JohnNeverDoe"));
        $this->assertNull(User::Login("john","BadTry"));
      }
    }

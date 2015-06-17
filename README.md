# PHP XML Helper
##### Static class for encode xml string to array or object & encode array to SimpleXMLElement 
## Installation

Installation with Composer

Add in composer.json
~~~
    "require": {
        ...
        "turkevich/php-xml-helper":"dev-master"
    }
~~~

Well done!

## Example encode
~~~
XML::encode(array(
        'book' => array(
            1,
            'page' => 1,
        ),
        'book1' => array(
            'attribute:test' => 2,
            'attribute:test2' => 'testValue',
            'page' => 1,
            'page1' => 'testValue',
        ),
), 'document')->asXML()
~~~
##### Result:
~~~
<?xml version="1.0"?>
<document>
  <book>
    <item>1</item>
    <page>1</page>
  </book>
  <book1 test="2" test2="testValue">
    <page>1</page>
    <page1>testValue</page1>
  </book1>
</document>
~~~

## Example decode
~~~
$xml = '<?xml version="1.0"?>
<document>
  <book>
    <item>1</item>
    <page>1</page>
  </book>
  <book1 test="2" test2="testValue">
    <page>1</page>
    <page1>testValue</page1>
  </book1>
</document>
';

XML::decode($xml);

or

XML::decode($xml,true);
~~~
##### Result:
~~~
To array:
array(2) {
  ["book"]=>
  array(3) {
    ["item"]=>
    string(1) "1"
    ["page"]=>
    string(1) "1"
    [0]=>
    string(13) "
    
    
  "
  }
  ["book1"]=>
  array(4) {
    ["attribute:"]=>
    array(2) {
      ["test"]=>
      string(1) "2"
      ["test2"]=>
      string(9) "testValue"
    }
    ["page"]=>
    string(1) "1"
    ["page1"]=>
    string(9) "testValue"
    [0]=>
    string(13) "
    
    
  "
  }
}

To object:
object(stdClass)#5 (2) {
  ["book"]=>
  array(3) {
    ["item"]=>
    string(1) "1"
    ["page"]=>
    string(1) "1"
    [0]=>
    string(13) "
    
    
  "
  }
  ["book1"]=>
  array(4) {
    ["attribute:"]=>
    array(2) {
      ["test"]=>
      string(1) "2"
      ["test2"]=>
      string(9) "testValue"
    }
    ["page"]=>
    string(1) "1"
    ["page1"]=>
    string(9) "testValue"
    [0]=>
    string(13) "
    
    
  "
  }
}
~~~

Enjoy, guys!

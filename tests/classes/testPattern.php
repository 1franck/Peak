<?php
/**
 * test for PEAK/PATTERN.PHP
 * @version 20100528
 */
$file_to_test = realpath('./../library/Peak/Pattern.php');
include($file_to_test);
echo 'Tested file: '.$file_to_test.'<br />';


/**
 * Test class.pattern.php
 */
class TestOfPattern extends UnitTestCase
{
    
    //test for method text()
    function testOfText()
    {
        $this->assertTrue(Peak_Pattern::text('ssg4gredfg'),'not detected as text without space');
        $this->assertTrue(Peak_Pattern::text('ssg4gr edfg',true),'not detected as text with space');
        $this->assertTrue(Peak_Pattern::text('ssg4gr edfg gf dfg',true),'not detected as text with space');
        
        $this->assertFalse(Peak_Pattern::text('ssg4gr @ edfg',true),'not detected as text with space');
        $this->assertFalse(Peak_Pattern::text('@¤46546'),'not detected as text without space');
        $this->assertFalse(Peak_Pattern::text('ssdg redfg'),'not detected as text without space');      
    }
    
    /**
     * test for method range()
     */
    function testOfRange()
    {
        $this->assertTrue(Peak_Pattern::range(12,1,100),'12 detected as not in range: 1 - 100');
        $this->assertTrue(Peak_Pattern::range(16080,10000,20000),'16080 detected as not in range: 10000 - 20000');
        $this->assertTrue(Peak_Pattern::range(300,1,1000),'300 detected as not in range: 1 - 1000');
        $this->assertTrue(Peak_Pattern::range(-1,-10,10),'-1 detected as not in range: -10 , -10');
        
        
        $this->assertFalse(Peak_Pattern::range(3000,1,1000),'3000 detected as in range: 1 - 1000');
        $this->assertFalse(Peak_Pattern::range(-1,1,100),'-1 detected as not in range: 1 - 100');
        $this->assertFalse(Peak_Pattern::range(10,-1,-1000),'10 detected as not in range: 1 , -1000');
        
    }
    
    //test for method day()
    function testOfDay()
    {
        //expected to be false
        $this->assertFalse(Peak_Pattern::day(0),'0 detected as valid day integer');
        $this->assertFalse(Peak_Pattern::day(32),'32 detected as valid day integer');               
        $this->assertFalse(Peak_Pattern::day('a01'),'"a01" detected as valid day integer');
        $this->assertFalse(Peak_Pattern::day('01a'),'"01a" detected as valid day integer');
        $this->assertFalse(Peak_Pattern::day(0.1),'0.1 detected as valid day integer');
        $this->assertFalse(Peak_Pattern::day(-1),'-1 detected as valid day integer');
        $this->assertFalse(Peak_Pattern::day(1.9),'1.9 detected as valid day integer');
        $this->assertFalse(Peak_Pattern::day(31.9),'31.9 detected as valid day integer');
        
        //expected to be true
        $this->assertTrue(Peak_Pattern::day('1'),'"1" not detected as valid day integer');
        $this->assertTrue(Peak_Pattern::day('01'),'"01" not detected as valid day integer');
        $this->assertTrue(Peak_Pattern::day(1),'1 not detected as valid day integer');
        $this->assertTrue(Peak_Pattern::day(31),'31 not detected as valid day integer');
    }
    
    //test for method month()
    function testOfMonth()
    {
        //expected to be false
        $this->assertFalse(Peak_Pattern::month(0),'0 detected as valid month integer');
        $this->assertFalse(Peak_Pattern::month(13),'13 detected as valid month integer');               
        $this->assertFalse(Peak_Pattern::month('a1'),'"a01" detected as valid month integer');
        $this->assertFalse(Peak_Pattern::month('3a1'),'"3a1" detected as valid month integer');
        $this->assertFalse(Peak_Pattern::month('01a'),'"01a" detected as valid month integer');
        $this->assertFalse(Peak_Pattern::month(10.1),'10.1 detected as valid month integer');
        $this->assertFalse(Peak_Pattern::month(-1),'-1 detected as valid month integer');
        $this->assertFalse(Peak_Pattern::month(1.9),'1.9 detected as valid month integer');
        $this->assertFalse(Peak_Pattern::month(12.9),'12.9 detected as valid month integer');
        
        //expected to be true
        $this->assertTrue(Peak_Pattern::month('1'),'"1" not detected as valid month integer');
        $this->assertTrue(Peak_Pattern::month('01'),'"01" not detected as valid month integer');
        $this->assertTrue(Peak_Pattern::month(1),'1 not detected as valid month integer');
        $this->assertTrue(Peak_Pattern::month(12),'12 not detected as valid month integer');
    }
    
    //test for method year()
    function testOfYear()
    {
        //expected to be false
        $this->assertFalse(Peak_Pattern::year(0),'0 detected as valid year integer');                    
        $this->assertFalse(Peak_Pattern::year('a01'),'"a01" detected as valid year integer');
        $this->assertFalse(Peak_Pattern::year('01a'),'"01a" detected as valid year integer');
        $this->assertFalse(Peak_Pattern::year(0.1),'0.1 detected as valid year integer');
        $this->assertFalse(Peak_Pattern::year(-1),'-1 detected as valid year integer');
        $this->assertFalse(Peak_Pattern::year(1.9),'1.9 detected as valid year integer');
        $this->assertFalse(Peak_Pattern::year(31.9),'31.9 detected as valid year integer');
        $this->assertFalse(Peak_Pattern::year(1524.4),'1524.4 detected as valid year integer');
        $this->assertFalse(Peak_Pattern::year(12345),'12345 detected as valid year integer');
        $this->assertFalse(Peak_Pattern::year("2009a"),'"2009a" detected as valid year integer');
        
        //expected to be true
        $this->assertTrue(Peak_Pattern::year(32),'32 detected as valid year integer'); 
        $this->assertTrue(Peak_Pattern::year('1000'),'"1000" not detected as valid year integer');
        $this->assertTrue(Peak_Pattern::year('01000'),'"01000" not detected as valid year integer');
        $this->assertTrue(Peak_Pattern::year(1524),'1524 not detected as valid year integer');
        $this->assertTrue(Peak_Pattern::year(531),'531 not detected as valid year integer');
    }
        
    //test for method email()
    function testOfEmail()
    {
        $emails = array('abcdef123@hotmail.com' => true,
                        'abcdef123@hotmail.qc.ca' => true,
                        'abc_def_123@hotmail.qc.ca' => true,
                        
                        'abcdef123 @hotmail.com' => false,
                        'abcdef123@hotmail.1' => false,
                        'abcdef123@hotmail.' => false,
                        'test@hotmail' => false);
        
        foreach($emails as $email => $expected) {
            if($expected) $this->assertTrue(Peak_Pattern::email($email), $email.' detected as invalid email');
            else $this->assertFalse(Peak_Pattern::email($email), $email.' detected as valid email');
        }
    }
    
    /**
     * Tests for method pattern::ip()
     */
    function testOfIp()
    {
        $ips = array('127.0.0.1' => true,
                     '255.255.255.255' => true,
                     '192.168.0.1' => true,
                     '254.56.98.138' => true,
                        
                     '127.0.0.1.1' => false,
                     '127.0.0.1.' => false,
                     '256.2.78.56' => false,
                     '256.256.256.256' => false,
                     '256.0.0.0' => false,
                     '156.256.151.26' => false,
                     '025.021.025.054' => false,
                     '.127.0.0.1' => false);
        
        foreach($ips as $ip => $expected) {
            if($expected) $this->assertTrue(Peak_Pattern::ip($ip), $ip.' detected as invalid ip address');
            else $this->assertFalse(Peak_Pattern::ip($ip), $ip.' detected as valid ip address');
        }
    }
    
    //test for method url()
    function testOfUrl()
    {
        // 'url to test' => true or false(this is test expected value)
        $urlToTest = array('http://127.0.0.1' => true,           //failed
                           'http://127.0.0.1/test.html' => true, //failed
                           'http://localhost' => true,           //failed
                           'http:/127.0.0.1'  => false,
                           'https://127.0.0.1'  => true,         //failed
                           'https:/127.0.0.1'  => false,
                           'p://127.0.0.1'  => false,
                           '127.0.0.1' => false,                       
                           
                           'http://example'  => true,
                           'http://example' => false,
                           'http://example.com' => true,
                           'http://example.com?test' => true,
                           'http://example.com?test=test' => true,
                           'http://example.com?test=test&test' => true,
                           'http://example.com?test&tes78t' => true,
                           'http://example.com?&' => true,
                           'http://example.com/test' => true,
                           'http://example.com/test.php' => true,
                           'http://example.com/test/test/test' => true,
                           
                           'example/test.txt' => false,
                           'class.test.php' => false,
                           'http://www.example' => false,                                        
                           'http://example/test' => false,
                           'http:\\example' => false,
                           'http:\\example.com' => false,
                           'http:example.com' => false,
                           'http://www.example' => false);
                           
        foreach($urlToTest as $url => $expected) {
            if($expected) $this->assertTrue(Peak_Pattern::url($url,true),'url '.$url.' no detected properly');
            else $this->assertFalse(Peak_Pattern::url($url), 'url '.$url.' detected as url');
        }
        
    }

}
<?php
require_once '/var/www/html/sugarPro/custom/modules/Accounts/clients/base/api/MyApi.php';
class MyApiTest extends Sugar_PHPUnit_Framework_TestCase
{
    /*
     * This function behaves as constructor
     */

    public function setUp()
    {
        $GLOBALS['log']->fatal('Executed');
        SugarTestHelper::setUp("beanList");
    }

    /*
     * This function behaves as Destructor
     */
    public function tearDown()
    {
        parent::tearDown();
    }

    public function mytestProvider()
    {
        return array(
            '1st',
            '2nd',
            '3rd'
        );
    }

      /*
     * @dataProvider mytestProvider
     * @param type $one
     * @param type $two
     * @param type $three
     * @var temp object of mock class
     * @value return value of mock method
     * This functions Mock Account class including its method getBeans
     * The function then returns mock output defined by willReturn method
     * The function then check expected output with mock output
     */

    public function testmytest(/*$one, $two, $three*/)
    {
        $temp = $this->getMockBuilder(Account::class)
            ->setMethods(['getBeans'])
            ->getMock();

        $temp->expects($this->once())
            ->method('getBeans')
            ->willReturn('Allen');

        $value = $temp->getBeans();
        $this->assertEquals('Allen',$value);
    }
}
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

    /*
     * When using arbitrary number of arguments
     */

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
     * @value return value of mock method getBean
     * @value1 return value of mock method loadRelationship
     * @value2 return value of mock method getBeans
     * This functions Mock Account class including its method getBeans
     * The function then returns mock output defined by willReturn method
     * The function then check expected output with mock output
     */

    public function testmytest(/*$one, $two, $three*/)
    {
        $temp = $this->getMockBuilder(Account::class)
            ->setMethods(['getBean','loadRelationship', 'getBeans'])
            ->getMock();

        $temp->expects($this->once())
            ->method('getBean')
            ->willReturn('Target1');

        $temp->expects($this->once())
            ->method('loadRelationship')
            ->with('Target_lead')
            ->willReturn('true');

        $temp->expects($this->once())
            ->method('getBeans')
            ->willReturn('Test Lead');

        $value = $temp->getBean();
        $value1 = $temp->loadRelationship('Target_lead');
        $value2 = $temp->getBeans();

        if($value1==true) {
            $this->assertEquals('Target1',$value);
            $this->assertEquals('Test Lead',$value2);
        }
    }
}
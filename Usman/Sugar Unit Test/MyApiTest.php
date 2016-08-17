<?php
//require_once '/var/www/html/sugarPro/custom/modules/Accounts/clients/base/api/StockApi.php';
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
     * @dataProvider myProvider
     * @var temp object of mock class
     * @value return value of mock method
     * This functions Mock Account class including its method getBeans
     * The function then returns mock output defined by willReturn method
     * The function then check expected output with mock output
     */

    public function testmytest($target=null, $link_field=null, $lead=null)
    {

        $temp = $this->getMockBuilder(Account::class)
            ->setMethods(['getBean','loadRelationship', 'getBeans'])
            ->getMock();

        $temp->expects($this->once())
            ->method('getBean')
            ->willReturn($target);

        $temp->expects($this->once())
            ->method('loadRelationship')
            ->with($link_field)
            ->willReturn('true');

        $temp->expects($this->once())
            ->method('getBeans')
            ->willReturn($lead);

        $value = $temp->getBean();
        $value1 = $temp->loadRelationship($link_field);
        $value2 = $temp->getBeans();

        if($value1==true) {
            $this->assertEquals($target,$value);
            $this->assertEquals($lead,$value2);
        }

    }


    /*
     * Bean Objects Data that will be used for assertions
     * instead of operating on Databases
     */

    public function myProvider()
    {
        return [
            ['Target 1', 'Target_lead', 'Test Lead 1'],
            ['Target 2', 'Target_lead', 'Test Lead 2'],
            ['Target 3', 'Target_lead', 'Test Lead 3'],
            ['Target 4', 'Target_lead', 'Test Lead 4'],
        ];
    }
}


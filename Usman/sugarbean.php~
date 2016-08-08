<?php
/*
 * Implementation of API through SigarBean
 */
class MyApi extends SugarApi {
    public function registerApiRest() {
        return array(
            'getData' => array(
                'reqType' => 'GET', //The type of web requests this code responds to
                'path' => array('Accounts','at_'),
                'pathVars' => array('',''),
                'method' => 'getData', //The PHP method that will execute for the response
                'shortHelp' => 'This method retrieves stock data',
                'longHelp' => '',
            )
        );
    }
/*
 *This is the method that executes when the endpoint is invoked
 * It shows the Leads (customer which became (Opportunities)
 * and Accounts as well using SugarBean
 *
 */
    public function getData($api, $args)
    {
        $id = $args['id'];
        $bean = BeanFactory::getBean('Leads', $id);
        if ($bean->load_relationship('opportunity')) {
            $relatedBeans = $bean->opportunity->getBeans();
        }
        //   $GLOBALS['log']->fatal($relatedBeans);

        foreach ($relatedBeans as $var) {
            if ($var->load_relationship('accounts')) {
                $accRelated = $var->accounts->getBeans();
                foreach($accRelated as $var1) {
                    $GLOBALS['log']->fatal('Related Account'. $accRelated->name);
                }
            }
        }
    }
}
?>

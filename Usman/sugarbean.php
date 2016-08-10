<?php

class MyApi extends SugarApi {
//This method must be defined in all custom endpoints
public function registerApiRest() {
    return array(
    'getData' => array(
    'reqType' => 'GET', //The type of web requests this code responds to
    'method' => 'getData', //The PHP method that will execute for the response
    'path' => array('Accounts','at_'),
    'pathVars' => array('',''),
    //'noLoginRequired' => true,
    'shortHelp' => 'This method retrieves stock data',
    'longHelp' => '',
    )
  );
}

//This is the method that executes when the endpoint is invoked
public function getData($api, $args)
{
    $beanTarget = BeanFactory::getBean('Prospects')->get_list('prospects.first_name');
    $data = 'Targets ';
    foreach($beanTarget['list'] as $item) {
        $GLOBALS['log']->fatal("All Prospects with related prospects lists, leads, opportunities and accounts");
        $GLOBALS['log']->fatal($item->first_name);
        $data .= $item->first_name;

        if ($item->load_relationship('prospect_lists')) {
            $GLOBALS['log']->fatal("Prospect Which became Prospects Lists");
            $relatedBeansPlist = $item->prospect_lists->getBeans();

            foreach ($relatedBeansPlist as $item1) {
                $GLOBALS['log']->fatal($item1->name);
                $data .= "->".$item1->name;

                if ($item1->load_relationship('leads')) {
                    $relatedBeansLeads = $item1->leads->getBeans();
                    $GLOBALS['log']->fatal("Prospect List Which became Leads");

                    foreach ($relatedBeansLeads as $item2) {
                        $GLOBALS['log']->fatal($item2->first_name);
                        $data .= "->".$item2->first_name;

                        if ($item1->load_relationship('opportunity')) {
                            $relatedBeansOpportunity = $item2->opportunity->getBeans();
                            $GLOBALS['log']->fatal("Lead Which became opportunity");

                            foreach ($relatedBeansOpportunity as $item3) {
                                $GLOBALS['log']->fatal($item3->name);
                                $data .= "->".$item3->name;
                                if ($item1->load_relationship('accounts')) {
                                    $relatedBeansAccounts = $item3->accounts->getBeans();
                                    $GLOBALS['log']->fatal("Opportunity Which became Account");

                                    foreach ($relatedBeansAccounts as $item4) {
                                        $GLOBALS['log']->fatal($item4->name);
                                        $data .= "->".$item4->name;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    $GLOBALS['log']->fatal("All Leaders Lists with its related Opportunity and Accounts");

    $beanLeads = BeanFactory::getBean('Leads')->get_list('first_name');

    foreach($beanLeads['list'] as $item) {
        $GLOBALS['log']->fatal("Lead Lists");
        $GLOBALS['log']->fatal($item->first_name);

        if ($item->load_relationship('opportunity')) {
            $relatedBeansOpportunity = $item->opportunity->getBeans();
            $GLOBALS['log']->fatal("Lead Which became opportunity");

            foreach ($relatedBeansOpportunity as $opp) {
                $GLOBALS['log']->fatal($opp->name);

                if ($opp->load_relationship('accounts')) {
                    $relatedBeansAccounts = $opp->accounts->getBeans();
                    $GLOBALS['log']->fatal("Opportunity Which became Account");

                    foreach ($relatedBeansAccounts as $item2) {
                        $GLOBALS['log']->fatal($item2->name);
                    }
                }
            }
        }
    }

    $GLOBALS['log']->fatal("All Opportunity Lists and its related Accounts");

    $beanOpp = BeanFactory::getBean('Opportunities')->get_list('opportunities.name');

    foreach($beanOpp['list'] as $item) {
        $GLOBALS['log']->fatal("Opportunity Lists");
        $GLOBALS['log']->fatal($item->name);

        if ($item->load_relationship('accounts')) {
            $relatedBeansAccounts = $item->accounts->getBeans();
            $GLOBALS['log']->fatal("Opportunity Which became Account");

            foreach ($relatedBeansAccounts as $item2) {
                $GLOBALS['log']->fatal($item2->name);
            }
        }
    }
    return $data;
}
    }
?>

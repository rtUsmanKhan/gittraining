<?php

class StockApi extends SugarApi {
//This method must be defined in all custom endpoints
public function registerApiRest() {
    return array(
    'getStockData' => array(
    'reqType' => 'GET', //The type of web requests this code responds to
    'method' => 'getStockData', //The PHP method that will execute for the response
    'path' => array('Accounts','at_'),
    'pathVars' => array('',''),
    //'noLoginRequired' => true,
    'shortHelp' => 'This method retrieves stock data',
    'longHelp' => '',
    )
  );
}

//This is the method that executes when the endpoint is invoked
public function getStockData($api, $args)
{
    $beanTarget = BeanFactory::getBean('Prospects')->get_list('prospects.first_name');
    $GLOBALS['log']->fatal($beanTarget);

    $GLOBALS['log']->fatal("All Prospects with related prospects lists, leads, opportunities and accounts");

    foreach($beanTarget['list'] as $item) {
        $GLOBALS['log']->fatal($item->first_name);
        $data['targets'][$item->id] = $item->first_name;

        if ($item->load_relationship('prospect_lists')) {
            $GLOBALS['log']->fatal("Prospect Which became Prospects Lists");
            $relatedBeansPlist = $item->prospect_lists->getBeans();

            foreach ($relatedBeansPlist as $item1) {
                $GLOBALS['log']->fatal($item1->name);
                $data['targets'][$item1->id] = $item1->name;

                if ($item1->load_relationship('leads')) {
                    $relatedBeansLeads = $item1->leads->getBeans();
                    $GLOBALS['log']->fatal("Prospect List Which became Leads");

                    foreach ($relatedBeansLeads as $item2) {
                        $GLOBALS['log']->fatal($item2->first_name);
                        $data['targets'][$item2->id] = $item2->name;

                        if ($item1->load_relationship('opportunity')) {
                            $relatedBeansOpportunity = $item2->opportunity->getBeans();
                            $GLOBALS['log']->fatal("Lead Which became opportunity");
                            foreach ($relatedBeansOpportunity as $item3) {
                                $GLOBALS['log']->fatal($item3->name);

                                $data['targets'][$item->id] = $item3->name;

                                if ($item1->load_relationship('accounts')) {
                                    $relatedBeansAccounts = $item3->accounts->getBeans();
                                    $GLOBALS['log']->fatal("Opportunity Which became Account");
                                    foreach ($relatedBeansAccounts as $item4) {
                                        $GLOBALS['log']->fatal($item4->name);
                                        $data['targets'][$item4->id] = $item3->name;
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
        $data['lead'][$item->id] = $item->name;

        if ($item->load_relationship('opportunity')) {
            $relatedBeansOpportunity = $item->opportunity->getBeans();
            $GLOBALS['log']->fatal("Lead Which became opportunity");

            foreach ($relatedBeansOpportunity as $opp) {
                $GLOBALS['log']->fatal($opp->name);
                $data['lead'][$opp->id] = $opp->name;

                if ($opp->load_relationship('accounts')) {
                    $relatedBeansAccounts = $opp->accounts->getBeans();
                    $GLOBALS['log']->fatal("Opportunity Which became Account");

                    foreach ($relatedBeansAccounts as $item2) {
                        $GLOBALS['log']->fatal($item2->name);
                        $data['lead'][$item2->id] = $item2->name;
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
        $data['opp'][$item->id] = $item->name;

        if ($item->load_relationship('accounts')) {
            $relatedBeansAccounts = $item->accounts->getBeans();
            $GLOBALS['log']->fatal("Opportunity Which became Account");

            foreach ($relatedBeansAccounts as $item2) {
                $GLOBALS['log']->fatal($item2->name);
                $data['opp'][$item2->id] = $item2->name;
            }
        }
    }
    $jdata = json_encode($data);

    return $jdata;
}
    }
?>

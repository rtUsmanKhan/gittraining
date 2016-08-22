<?php

class StockApi extends SugarApi {
//This method must be defined in all custom endpoints
public function registerApiRest() {
    return array(
    'getStockData' => array(
    'reqType' => 'GET',
    'method' => 'getStockData',
    'path' => array('Accounts','at_','?'),
    'pathVars' => array('','', 'id'),
    //'noLoginRequired' => true,
    'shortHelp' => 'This method retrieves stock data',
    'longHelp' => '',
    )
  );
}

/*
 * This method collects list of:
 * All Targets which are converted to Target lists then transform to lead which
 * progress and become opportunity and finally become Account.
 * The Targets which are converted to Target lists then transform to lead which
 * progress and become opportunity only.
 * The Targets which are converted to Target lists then transform to lead only.
 * The Targets which are converted to Target lists only.
 *
 * All Leads and which progress to become opportunity and finally become Account.
 * The leads which progress to become opportunity only
 *
 * All Opportunities and which matures to Accounts
 *
 * It saves the data into associated array with module name and record id.
 * It saves null value which is used represent the end of related record.
 *
 * It encodes to array to JSON text and returns
 */
public function getStockData($api, $args)
{
    $id = $args['id'];
    //$id = '4e37bb18-593d-eea2-1e55-57a9dbcb5482';
    $beanProspect = BeanFactory::getBean('Prospects', $id);
    $i=0;

    if (!empty($beanProspect)) {
        if ($beanProspect->load_relationship('prospect_lists')) {
            $data['targets'][$beanProspect->id] = $beanProspect->name;

            $relatedBeansPlist = $beanProspect->prospect_lists->getBeans();
        }

        foreach ($relatedBeansPlist as $plist){
            if ($plist->load_relationship('leads')) {

                $data['targets'][$plist->id] = $plist->name;
                $relatedBeanslead = $plist->leads->getBeans();

                if(!empty($relatedBeanslead)) {
                    foreach ($relatedBeanslead as $var) {
                        $data['targets'][$var->id] = $var->first_name;
                        if ($var->load_relationship('opportunity')) {
                            $oppRelated = $var->opportunity->getBeans();

                            if(!empty($oppRelated)) {
                                foreach ($oppRelated as $var1) {
                                    $data['targets'][$var1->id] = $var1->name;

                                    if ($var1->load_relationship('accounts')) {
                                        $accRelated = $var->accounts->getBeans();
                                        if(!empty($accRelated)) {
                                            foreach ($accRelated as $var2) {
                                                $data['targets'][$var2->id] = $var2->name;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $data['targets']['End' . $i++] = 'Not Available';
                                $data['targets']['End' . $i++] = 'Not Available';
                                $data['targets']['End' . $i++] = null;
                            }
                        }
                    }
                } else {
                    $data['targets']['End' . $i++] = 'Not Available';
                    $data['targets']['End' . $i++] = 'Not Available';
                    $data['targets']['End' . $i++] = 'Not Available';
                    $data['targets']['End' . $i++] = null;
                }
            }
        }
    } else {
            $data['targets']['End' . $i++] = 'Not Available';
            $data['targets']['End' . $i++] = 'Not Available';
            $data['targets']['End' . $i++] = 'Not Available';
            $data['targets']['End' . $i++] = 'Not Available';
            $data['targets']['End' . $i++] = null;
    }
 $jdata = json_encode($data);
    return $jdata;
}
    }
?>

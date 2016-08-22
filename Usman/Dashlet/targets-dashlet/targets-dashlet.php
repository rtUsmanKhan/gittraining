<?php
/**
 * Created by PhpStorm.
 * User: usman.khan
 * Date: 8/19/16
 * Time: 6:11 PM
 */

/**
 * Metadata for the targets-dashlet by Status example dashlet view
 *
 * This dashlet is only allowed to appear on the Case module's list view
 * which is also known as the 'records' layout.
 */
$viewdefs['base']['view']['targets-dashlet'] = array(
    'dashlets' => array(
        array(
            //Display label for this dashlet
            'label' => 'LBL_STATUS',
            //Description label for this Dashlet
            'description' => 'LBL_DESCRIPTION',
            'config' => array(
            ),
            'preview' => array(
                'limit' => '10',
                'filter' => '7',
                'visibility' => 'user',
            ),
            //Filter array decides where this dashlet is allowed to appear
            'filter' => array(
                //Modules where this dashlet can appear
                'module' => array(
                    'Prospects',
                ),
                //Views where this dashlet can appear
                'view' => array(
                    'record',
                )
            )
        ),
    ),
);
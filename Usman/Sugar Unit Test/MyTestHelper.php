<?php
/*
 * Your installation or use of this SugarCRM file is subject to the applicable
 * terms available at
 * http://support.sugarcrm.com/06_Customer_Center/10_Master_Subscription_Agreements/.
 * If you do not agree to all of the applicable terms or do not have the
 * authority to bind the entity as an authorized representative, then do not
 * install or use this SugarCRM file.
 *
 * Copyright (C) SugarCRM Inc. All rights reserved.
 */

if(!defined('sugarEntry')) define('sugarEntry', true);

set_include_path(
    dirname(__FILE__) . PATH_SEPARATOR .
    dirname(__FILE__) . '/..' . PATH_SEPARATOR .
    get_include_path()
);

// constant to indicate that we are running tests
if (!defined('SUGAR_PHPUNIT_RUNNER'))
    define('SUGAR_PHPUNIT_RUNNER', true);

// initialize the various globals we use
global $sugar_config, $db, $fileName, $current_user, $locale, $current_language;
if ( !isset($_SERVER['HTTP_USER_AGENT']) )
    // we are probably running tests from the command line
    $_SERVER['HTTP_USER_AGENT'] = 'cli';

// move current working directory
if (basename(getcwd()) == 'tests' || !is_file('include/entryPoint.php')) {
  chdir(dirname(__FILE__) . '/..');
}

// this is needed so modules.php properly registers the modules globals, otherwise they
// end up defined in wrong scope
global $beanFiles, $beanList, $objectList, $moduleList, $modInvisList, $bwcModules, $sugar_version, $sugar_flavor;
require_once 'include/entryPoint.php';
require_once 'include/utils/layout_utils.php';

chdir(sugar_root_dir());

$GLOBALS['db'] = DBManagerFactory::getInstance();

$current_language = $sugar_config['default_language'];
// disable the SugarLogger
$sugar_config['logger']['level'] = 'fatal';

$GLOBALS['sugar_config']['default_permissions'] = array (
        'dir_mode' => 02770,
        'file_mode' => 0777,
        'chown' => '',
        'chgrp' => '',
    );

$GLOBALS['js_version_key'] = 'testrunner';

if ( !isset($_SERVER['SERVER_SOFTWARE']) )
    $_SERVER["SERVER_SOFTWARE"] = 'PHPUnit';

// helps silence the license checking when running unit tests.
$_SESSION['VALIDATION_EXPIRES_IN'] = 'valid';

$GLOBALS['startTime'] = microtime(true);

// clean out the cache directory
require_once 'modules/Administration/QuickRepairAndRebuild.php';
$repair = new RepairAndClear();
$repair->module_list = array();
$repair->show_output = false;
$repair->clearJsLangFiles();
$repair->clearJsFiles();

// make sure the client license has been validated
$license = new Administration();
$license = $license->retrieveSettings('license', true);
if ( !isset($license->settings['license_vk_end_date']))
    $license->saveSetting('license', 'vk_end_date', date('Y-m-d',strtotime('+1 year')));
// mark that we got by the admin wizard already
$focus = new Administration();
$focus->retrieveSettings();
$focus->saveSetting('system','adminwizard',1);


$GLOBALS['db']->commit();

define('CHECK_FILE_MAPS', false);
// define our testcase subclass
if (function_exists("shadow_get_config") && ($sc = shadow_get_config()) != false && !empty($sc['template'])) {
    // shadow is enabled
    define('SHADOW_ENABLED', true);
    define('SHADOW_CHECK', false); // disable for faster tests
} else {
    define('SHADOW_ENABLED', false);
    define('SHADOW_CHECK', false);
}

class My_PHPUnit_Framework_TestCase extends PHPUnit_Framework_TestCase
{
    protected $backupGlobals = false;

    protected $useOutputBuffering = true;

    protected $file_map;

    public static function getFiles()
    {
        $dir = realpath(dirname(__FILE__)."/..");
        $files = `find $dir -name cache -prune -o -name custom -prune -o -name upload -prune -o -name tests -prune -o -name sugarcrm\\*.log -prune -o -type f -print | sort`;
        $flist = explode("\n", $files);
        sort($flist);

        return join("\n", $flist);
    }

    protected function assertPreConditions()
    {
        $GLOBALS['runningTest'] = $this->getName(false);
        $GLOBALS['runningTestClass'] = get_class($this);
        if (isset($GLOBALS['log'])) {
            $GLOBALS['log']->info("START TEST: {$this->getName(false)}");
        }
    }

    protected function assertPostConditions()
    {
        if (!empty($_REQUEST)) {
            foreach (array_keys($_REQUEST) as $k) {
                unset($_REQUEST[$k]);
            }
        }

        if (!empty($_POST)) {
            foreach (array_keys($_POST) as $k) {
                unset($_POST[$k]);
            }
        }

        if (!empty($_GET)) {
            foreach (array_keys($_GET) as $k) {
                unset($_GET[$k]);
            }
        }
        if (isset($GLOBALS['log'])) {
            $GLOBALS['log']->info("DONE TEST: {$this->getName(false)}");
        }
        // reset error handler in case somebody set it
        restore_error_handler();
    }

    public static function setUpBeforeClass()
   {
        MyTestHelper::init();
   }

    public static function tearDownAfterClass()
    {
        unset($GLOBALS['disable_date_format']);
        SugarBean::resetOperations();
        $GLOBALS['timedate']->clearCache();
        if (constant('CHECK_FILE_MAPS')) {
            if (!self::compareArray(SugarAutoLoader::scanDir(""), SugarAutoLoader::$filemap)) {
                SugarAutoLoader::buildCache();
            }
        }
        MyTestHelper::tearDown();
    }

    public static function compareArray($arr1, $arr2, $path = "")
    {
        if (!is_array($arr2)) {
            echo ("\nERROR[{$GLOBALS['runningTestClass']}:{$GLOBALS['runningTest']}]: Difference in ./{$path} - is file in map but should be directory!\n");
        }
        foreach ($arr2 as $key => $value) {
            $keypath = "{$path}$key/";
            if (in_array($keypath, SugarAutoLoader::$exclude)) {
                unset($arr1[$key]);
                continue;
            }
            if (!isset($arr1[$key])) {
                echo ("\nERROR[{$GLOBALS['runningTestClass']}:{$GLOBALS['runningTest']}]: Difference in {$path}$key - in map but not on disk\n");

                return false;
            }
            if (is_array($arr1[$key])) {
                if (!self::compareArray($arr1[$key], $arr2[$key], $keypath)) {
                    return false;
                }
            }
            unset($arr1[$key]);
        }
        foreach ($arr1 as $key => $value) {
            echo ("\nERROR[{$GLOBALS['runningTestClass']}:{$GLOBALS['runningTest']}]: Difference in {$path}$key - on disk but not in map!\n");

            return false;
        }

        return true;
    }

    protected function notRegexCallback($output)
    {
        $this->assertNotRegExp($this->_notRegex, $output);
    }

    public function expectOutputNotRegex($expectedRegex)
    {
        if (is_string($expectedRegex) || is_null($expectedRegex)) {
            $this->_notRegex = $expectedRegex;
        }

        $this->setOutputCallback(array($this, "notRegexCallback"));
    }

    public function runBare()
    {

        // Prevent the activity stream from creating messages.
        Activity::disable();
        if (SHADOW_CHECK && empty($this->file_map)) {
            $this->file_map = static::getFiles();
        }
        //track the original max execution time limit
        $originalMaxTime = ini_get('max_execution_time');

        parent::runBare();

        //sometimes individual tests change the max time execution limit, reset back to original
        set_time_limit($originalMaxTime);

        if (SHADOW_CHECK) {
            $oldfiles = $this->file_map;
            $this->file_map = static::getFiles();
            $this->assertEquals($oldfiles, $this->file_map);
        }
    }

    /**
     * Function: invokeMethod
     * This method will use to invoke private or protected methods.
     *
     * @static
     * @param  object  $object object of private or protected method
     * @param  string  $$methodName private or protected method's name
     * @return array   $parameters parameter list of private or protected method
     */
    public function invokeMethod(&$object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}

// define a mock logger interface; used for capturing logging messages emited
// the test suite
class MyMockLogger
{
    private $_messages = array();

    public function __call($method, $message)
    {
        $this->_messages[] = strtoupper($method) . ': ' . $message[0];
    }

    public function getMessages()
    {
        return $this->_messages;
    }

    public function getLastMessage()
    {
        return end($this->_messages);
    }

    public function getMessageCount()
    {
        return count($this->_messages);
    }
}

/**
 * Helper for initialization of global variables of SugarCRM
 *
 */
class MyTestHelper
{
    /**
     * @var array array of registered vars. It allows helper to unregister them on tearDown
     */
    protected static $registeredVars = array();

    /**
     * @var array array of global vars. They are storing on init one time and restoring in global scope each tearDown
     */
    protected static $initVars = array(
        'GLOBALS' => array()
    );

    /**
     * @var array of system preference of SugarCRM as theme etc. They are storing on init one time and restoring each tearDown
     */
    protected static $systemVars = array();

    /**
     * @var array of modules which we should refresh on tearDown.
     */
    protected static $cleanModules = array();

    /**
     * @var array of modules and their custom fields created during setup.
     */
    protected static $customFields = array();

    /**
     * @var bool is SugarTestHelper inited or not. Just to skip initialization on the second and others call of init method
     */
    protected static $isInited = false;
    /**
     * Initialization of main variables of SugarCRM in global scope
     *
     * @static
     */
    public static function init()
    {
        if (self::$isInited == true) {
            return true;
        }

        SugarCache::instance()->flush();

        // initialization & backup of sugar_config
        self::$initVars['GLOBALS']['sugar_config'] = null;
        if ($GLOBALS['sugar_config']) {
            self::$initVars['GLOBALS']['sugar_config'] = $GLOBALS['sugar_config'];
        }
        if (self::$initVars['GLOBALS']['sugar_config'] == false) {
            global $sugar_config;
            if (is_file('config.php')) {
                require_once 'config.php';
            }
            if (is_file('config_override.php')) {
                require_once 'config_override.php';
            }
            self::$initVars['GLOBALS']['sugar_config'] = $GLOBALS['sugar_config'];
        }

        // backup of current_language
        self::$initVars['GLOBALS']['current_language'] = 'en_us';
        if (isset($sugar_config['current_language'])) {
            self::$initVars['GLOBALS']['current_language'] = $sugar_config['current_language'];
        }
        if (isset($GLOBALS['current_language'])) {
            self::$initVars['GLOBALS']['current_language'] = $GLOBALS['current_language'];
        }
        $GLOBALS['current_language'] = self::$initVars['GLOBALS']['current_language'];

        // backup of reload_vardefs
        self::$initVars['GLOBALS']['reload_vardefs'] = null;
        if (isset($GLOBALS['reload_vardefs'])) {
            self::$initVars['GLOBALS']['reload_vardefs'] = $GLOBALS['reload_vardefs'];
        }

        // backup of locale
        self::$initVars['GLOBALS']['locale'] = null;
        if (isset($GLOBALS['locale'])) {
            self::$initVars['GLOBALS']['locale'] = $GLOBALS['locale'];
        }
        if (empty(self::$initVars['GLOBALS']['locale'])) {
            self::$initVars['GLOBALS']['locale'] = Localization::getObject();
        }

        // backup of service_object

        if (isset($GLOBALS['service_object'])) {
            self::$initVars['GLOBALS']['service_object'] = $GLOBALS['service_object'];
        }

        //Backup everything that could have been loaded in modules.php
        include 'include/modules.php';
        foreach(array('moduleList', 'beanList', 'beanFiles', 'bwcModules', 'modInvisList',
                      'objectList', 'modules_exempt_from_availability_check', 'adminOnlyList'
                     ) as $globVar)
        {
            $GLOBALS[$globVar] = $$globVar;
            self::$initVars['GLOBALS'][$globVar] = $GLOBALS[$globVar];
        }

        if (isset($GLOBALS['current_user'])) {
            self::$initVars['GLOBALS']['current_user'] = $GLOBALS['current_user'];
        }

        // backup of SugarThemeRegistry
        self::$systemVars['SugarThemeRegistry'] = SugarThemeRegistry::current();

        self::$isInited = true;
    }

    /**
     * Checking is there helper for variable or not
     *
     * @static
     * @param  string                   $varName name of global variable of SugarCRM
     * @return bool                     is there helper for a variable or not
     * @throws SugarTestHelperException fired when there is no implementation of helper for a variable
     */
    protected static function checkHelper($varName)
    {
        if (method_exists(__CLASS__, 'setUp_' . $varName) == false) {
            throw new SugarTestHelperException('setUp for $' . $varName . ' is not implemented. ' . __CLASS__ . '::setUp_' . $varName);
        }
    }

    /**
     * Entry point for setup of global variable
     *
     * @static
     * @param  string $varName name of global variable of SugarCRM
     * @param  array  $params  some parameters for helper. For example for $mod_strings or $current_user
     * @return bool   is variable setuped or not
     */
    public static function setUp($varName, $params = array())
    {
        self::init();
        self::checkHelper($varName);

        return call_user_func(__CLASS__ . '::setUp_' . $varName, $params);
    }

    /**
     * Clean up all registered variables and restore $initVars and $systemVars
     * @static
     * @return bool status of tearDown
     */
    public static function tearDown()
    {
        self::init();

        // Handle current_user placing on the end since there are some things
        // that need current user for the clean up
        if (isset(self::$registeredVars['current_user'])) {
            $cu = self::$registeredVars['current_user'];
            unset(self::$registeredVars['current_user']);
            self::$registeredVars['current_user'] = $cu;
        }

        // unregister variables in reverse order in order to have dependencies unregistered after dependants
        $unregisterVars = array_reverse(self::$registeredVars);
        foreach ($unregisterVars as $varName => $isCalled) {
            if ($isCalled) {
                unset(self::$registeredVars[$varName]);
                if (method_exists(__CLASS__, 'tearDown_' . $varName)) {
                    call_user_func(__CLASS__ . '::tearDown_' . $varName, array());
                } elseif (isset($GLOBALS[$varName])) {
                    unset($GLOBALS[$varName]);
                }
            }
        }

        // Restoring of system variables
        foreach (self::$initVars as $scope => $vars) {
            foreach ($vars as $name => $value) {
                $GLOBALS[$name] = $value;
            }
        }

        // Restore the activity stream.
        Activity::enable();

        // Restoring of theme
        SugarThemeRegistry::set(self::$systemVars['SugarThemeRegistry']->dirName);
        SugarCache::$isCacheReset = false;

        return true;
    }

    /**
     * Registration of $beanList in global scope
     *
     * @static
     * @return bool is variable setuped or not
     */
    protected static function setUp_beanList($params = array(), $register = true)
    {
        if ($register) {
            self::$registeredVars['beanList'] = true;
        }
        global $beanList;
        require 'include/modules.php';

        return true;
    }

}
<?php

require_once 'mobtexting.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
function mobtexting_civicrm_config(&$config)
{
    _mobtexting_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function mobtexting_civicrm_xmlMenu(&$files)
{
    _mobtexting_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function mobtexting_civicrm_install()
{
    $groupID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', 'sms_provider_name', 'id', 'name');
    $params  =
    array('option_group_id' => $groupID,
        'label'                 => 'MOBtexting',
        'value'                 => 'org.civicrm.sms.mobtexting',
        'name'                  => 'mobtexting',
        'is_default'            => 1,
        'is_active'             => 1,
        'version'               => 3);
    require_once 'api/api.php';
    civicrm_api('option_value', 'create', $params);

    return _mobtexting_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function mobtexting_civicrm_uninstall()
{
    $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'mobtexting', 'id', 'name');
    if ($optionID) {
        CRM_Core_BAO_OptionValue::del($optionID);
    }

    $filter    = array('name' => 'org.civicrm.sms.mobtexting');
    $Providers = CRM_SMS_BAO_Provider::getProviders(false, $filter, false);
    if ($Providers) {
        foreach ($Providers as $key => $value) {
            CRM_SMS_BAO_Provider::del($value['id']);
        }
    }
    return _mobtexting_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function mobtexting_civicrm_enable()
{
    $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'mobtexting', 'id', 'name');
    if ($optionID) {
        CRM_Core_BAO_OptionValue::setIsActive($optionID, true);
    }

    $filter    = array('name' => 'org.civicrm.sms.mobtexting');
    $Providers = CRM_SMS_BAO_Provider::getProviders(false, $filter, false);
    if ($Providers) {
        foreach ($Providers as $key => $value) {
            CRM_SMS_BAO_Provider::setIsActive($value['id'], true);
        }
    }
    return _mobtexting_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function mobtexting_civicrm_disable()
{
    $optionID = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionValue', 'mobtexting', 'id', 'name');
    if ($optionID) {
        CRM_Core_BAO_OptionValue::setIsActive($optionID, false);
    }

    $filter    = array('name' => 'org.civicrm.sms.mobtexting');
    $Providers = CRM_SMS_BAO_Provider::getProviders(false, $filter, false);
    if ($Providers) {
        foreach ($Providers as $key => $value) {
            CRM_SMS_BAO_Provider::setIsActive($value['id'], false);
        }
    }
    return _mobtexting_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function mobtexting_civicrm_upgrade($op, CRM_Queue_Queue $queue = null)
{
    return _mobtexting_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function mobtexting_civicrm_managed(&$entities)
{
    return _mobtexting_civix_civicrm_managed($entities);
}

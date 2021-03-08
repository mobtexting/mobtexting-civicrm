<?php

/*
+--------------------------------------------------------------------+
| Copyright CiviCRM LLC. All rights reserved.                        |
|                                                                    |
| This work is published under the GNU AGPLv3 license with some      |
| permitted exceptions and without any warranty. For full license    |
| and copyright information, see https://civicrm.org/licensing       |
+--------------------------------------------------------------------+
 */
/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */

class org_civicrm_sms_mobtexting extends CRM_SMS_Provider
{
    protected $_apiType      = 'http';
    protected $_providerInfo = array();
    public $_apiURL          = "https://portal.mobtexting.com/";
    protected $_messageType  = array(
    );
    protected $_messageStatus = array(
        200 => 'Message Sent',
        422 => 'API Config Error',
    );
    public $_accessToken       = null;
    public $_service           = null;
    public $_sender            = null;
    private static $_singleton = array();
    protected $_ch;

    public function __construct($provider = array(
    ), $skipAuth = true) {
        // initialize vars
        $this->_apiType      = CRM_Utils_Array::value('api_type', $provider, 'http');
        $this->_providerInfo = $provider;
        if ($skipAuth) {
            return true;
        }

        $this->authenticate();
    }

    public static function &singleton($providerParams = array(
    ), $force = false) {

        $providerID = CRM_Utils_Array::value('provider_id', $providerParams);
        $skipAuth   = $providerID ? false : true;
        $cacheKey   = (int) $providerID;

        if (!isset(self::$_singleton[$cacheKey]) || $force) {
            $provider = array();
            if ($providerID) {
                $provider = CRM_SMS_BAO_Provider::getProviderInfo($providerID);
            }
            self::$_singleton[$cacheKey] = new org_civicrm_sms_mobtexting($provider, $skipAuth);
        }

        return self::$_singleton[$cacheKey];
    }

    public function authenticate()
    {
        return (true);
    }

    public function send($recipients, $header, $message, $jobID = null, $userID = null)
    {
        $name = $header['activity_subject'];
        if ($this->_apiType == 'http') {
            $access_token = "";
            $service      = "";
            $sender       = "";
            $validation   = "API Parameter Validation";
            $apiParams    = array_change_key_case($this->_providerInfo['api_params'], CASE_LOWER);

            if (array_key_exists('access_token', $apiParams)) {
                $access_token = $apiParams['access_token'];
                if (preg_match('/\|/', $access_token)) {
                    $access_tokens = explode('|', $access_token);
                    $key           = array_rand($access_tokens);
                    $access_token  = $access_tokens[$key];

                }
            } else {
                CRM_Core_Session::setStatus(ts("Access Token Field is Empty"), ts($validation), 'error');
                return false;

            }
            if (array_key_exists('service', $apiParams)) {
                $service = $apiParams['service'];
                if (preg_match('/\|/', $service)) {
                    $services = explode('|', $service);
                    $key      = array_rand($services);
                    $service  = $services[$key];

                }
            } else {
                CRM_Core_Session::setStatus(ts("Service Field is Empty"), ts($validation), 'error');
                return false;

            }
            if (array_key_exists('sender', $apiParams)) {
                $sender = $apiParams['sender'];
                if (preg_match('/\|/', $sender)) {
                    $senders = explode('|', $sender);
                    $key     = array_rand($senders);
                    $sender  = $senders[$key];

                }
            } else {
                CRM_Core_Session::setStatus(ts("Sender Field is Empty"), ts($validation), 'error');
                return false;

            }
            $this->_accessToken = trim($access_token);
            $this->_service     = trim($service);
            $this->_sender      = trim($sender);

            $getAccess_token = $this->_accessToken;
            $getService      = $this->_service;
            $getSender       = $this->_sender;
            $url             = $this->_providerInfo['api_url'];
            $response        = "";

            $response = $this->apiSender($url, $getAccess_token, $getService, $getSender, $message, $header['To'], $name);
            return $response;
        }

    }

    public function cleanNumber($number)
    {
        $num = trim($number);
        $num = str_replace(array('-', ' ', '%', '+', '(', ')'), '', $num);
        return $num;

    }

    public function apiSender($url, $access_token, $service, $sender, $message, $number, $name)
    {
        $number    = $this->cleanNumber($number);
        $data      = array("access_token" => $access_token, "service" => $service, "sender" => $sender, "message" => $message, "to" => $number, "name" => $name);
        $urlApi    = $url;
        $this->_ch = curl_init();
        if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        }
        curl_setopt_array($this->_ch, array(
            CURLOPT_URL            => $urlApi,
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_SSL_VERIFYHOST, Civi::settings()->get('verifySSL') ? 2 : 0,
            CURLOPT_SSL_VERIFYPEER, Civi::settings()->get('verifySSL'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 20,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_USERAGENT      => 'CiviCRM - http://civicrm.org/',
            CURLOPT_CUSTOMREQUEST  => 'POST',
        ));
        $response = curl_exec($this->_ch);
        curl_close($this->_ch);

        if (!$response) {
            $erroMessage = 'Error: "' . curl_error($this->_ch) . '" - Code: ' . curl_errno($this->_ch);
            return PEAR::raiseError($erroMessage, "API Error", PEAR_ERROR_RETURN);
        }
        $result   = json_decode($response);
        $response = (array) $result;

        $id      = "";
        $Message = "";
        $desc    = "";
        //API config errors response
        if ($response['status'] == "ERROR" and $response['code'] == 422) {

            $Message = $response['message'];
            $desc    = "API Parameter Config Value Is Error";

        } elseif ($response['status'] == 200) {
            //sms send response
            $id      = $response['data'][0]->id;
            $Message = $response['message'];
            $this->createActivity($id, $replace['message'][$i], $header, $jobID, $userID);
            return $id;

        } else {
            // access token error response
            $Message = "Access token value is invalid or empty";
            $desc    = "Access Token";
            CRM_Core_Error::debug_log_message("Error - for {$Message}");

        }
        return PEAR::raiseError($Message, $desc, PEAR_ERROR_RETURN);

    }

    public function callback()
    {
        $id = $this->retrieve('id', 'String');

        $activity         = new CRM_Activity_DAO_Activity();
        $activity->result = $id;

        if ($activity->find(true)) {
            $actStatusIDs = array_flip(CRM_Core_OptionGroup::values('activity_status'));

            $status = $this->retrieve('status', 'String');
            switch ($status) {
                case 200:
                    $statusID  = $actStatusIDs['Completed'];
                    $clickStat = $this->_messageStatus[$status] . " - Message Sent";
                    break;
                case 422:
                    $statusID  = $actStatusIDs['Cancelled'];
                    $clickStat = $this->_messageStatus[$status] . " - API Config Error";
                    break;
            }

            if ($statusID) {
                // update activity with status + msg in location
                $activity->status_id          = $statusID;
                $activity->location           = $clickStat;
                $activity->activity_date_time = CRM_Utils_Date::isoToMysql($activity->activity_date_time);
                $activity->save();
                CRM_Core_Error::debug_log_message("SMS Response updated for id={$id}.");
                return true;
            } else {
                $trace = "unhandled status value of '{$status}'";
            }
        } else {
            $trace = "could not find activity matching that Id";
        }

        // if no update is done
        CRM_Core_Error::debug_log_message("Could not update SMS Response for id={$id} - {$trace}");
        return false;

    }

    public function inbound()
    {
        $like      = "";
        $fromPhone = $this->retrieve('From', 'String');
        return parent::processInbound($fromPhone, $this->retrieve('Body', 'String'), null, $this->retrieve('id', 'String'));
    }

}

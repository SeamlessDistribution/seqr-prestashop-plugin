<?php

include_once('lib/prestashop/PsConfig.php');

if (!defined('_PS_VERSION_'))
    exit;

class Seqr extends PaymentModuleCore {
    public $config = null;

    public function __construct() {
        $this->name = 'seqr';
        $this->tab = 'payments_gateways';
        $this->version = '1.0.0';
        $this->author = 'SEQR Team';
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('SEQR');
        $this->description = $this->l('Accepts payments by SEQR.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall the SEQR module?');

        $this->config = new PsConfig();
        $this->config->load();
    }

    public function install() {
        if (!parent::install()
            || !$this->registerHook('payment')
            || !$this->registerHook('paymentReturn')
            || !$this->registerHook('header')
            || !$this->config->install()
        ) {
            return false;
        }
        return true;
    }

    public function uninstall() {
        if (
            !$this->config->uninstall()
            || !parent::uninstall()
        ) {
            return false;
        }
        return true;
    }

    public function getContent() {

        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            // Saving configuration settings

            $user = strval(Tools::getValue(SeqrConfig::SEQR_USER_ID));
            $terminalId = strval(Tools::getValue(SeqrConfig::SEQR_TERMINAL_ID));
            $terminalPass = strval(Tools::getValue(SeqrConfig::SEQR_TERMINAL_PASS));
            $mode = strval(Tools::getValue(SeqrConfig::SEQR_MODE));
            $timeout = strval(Tools::getValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT));

            $valid = true;
            $valid = $this->validateValue($user, "Invalid user id", $output);
            $valid = $this->validateValue($terminalId, "Invalid terminal id", $output) && $valid;
            $valid = $this->validateValue($terminalPass, "Invalid terminal password", $output) && $valid;
            $valid = $this->validateValue($mode, "SEQR mode is not set", $output) && $valid;
            $valid = $this->validateValue($timeout, "Payment timeout is not set", $output) && $valid;

            if ($valid) {
                $newConfig = new PsConfig();
                $newConfig->populate(array(
                    SeqrConfig::SEQR_USER_ID => $user,
                    SeqrConfig::SEQR_TERMINAL_ID => $terminalId,
                    SeqrConfig::SEQR_TERMINAL_PASS => $terminalPass,
                    SeqrConfig::SEQR_MODE => $mode,
                    SeqrConfig::SEQR_PAYMENT_TIMEOUT => $timeout
                ));
                $newConfig->save();
                $this->config = $newConfig;

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            } else {
                $output .= $this->displayError($this->l("Please correct the form and try again"));
            }

        }
        return $output . $this->displayForm();
    }

    public function displayForm() {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('SEQR Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('User id'),
                    'name' => SeqrConfig::SEQR_USER_ID,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Terminal id'),
                    'name' => SeqrConfig::SEQR_TERMINAL_ID,
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Terminal password'),
                    'name' => SeqrConfig::SEQR_TERMINAL_PASS,
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('SEQR Server'),
                    'name' => SeqrConfig::SEQR_MODE,
                    'required' => true,
                    'default_value' => $this->config->getMode(),
                    'options' => array(
                        'query' => $this->getModes(),
                        'id' => 'id',
                        'name' => 'name',
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Payment timeout (in seconds)'),
                    'name' => SeqrConfig::SEQR_PAYMENT_TIMEOUT,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit' . $this->name;
        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current value
        $helper->fields_value[SeqrConfig::SEQR_USER_ID] = $this->config->getUserId();
        $helper->fields_value[SeqrConfig::SEQR_TERMINAL_ID] = $this->config->getTerminalId();
        $helper->fields_value[SeqrConfig::SEQR_TERMINAL_PASS] = $this->config->getTerminalPass();
        $helper->fields_value[SeqrConfig::SEQR_MODE] = $this->config->getMode();
        $helper->fields_value[SeqrConfig::SEQR_PAYMENT_TIMEOUT] = $this->config->getTimeout();

        return $helper->generateForm($fields_form);
    }

    private function getModes() {
        return array(
            array(
                'id' => SeqrConfig::SEQR_MODE_DEMO,
                'name' => "Demo/Test server"
            ),
            array(
                'id' => SeqrConfig::SEQR_MODE_LIVE,
                'name' => "Live/Production server"
            )
        );
    }

    private function validateValue($value, $errorMessage, &$output) {

        $validation = !empty($value) && Validate::isGenericName($value);
        if (!$validation) {
            $output .= $this->displayError($this->l($errorMessage));
        }
        return $validation;
    }

    public function hookPayment($params) {

        if (!$this->active) {
            return;
        }

        // Check if configuration is valid
        if (!$this->config->isValid()) {
            return;
        }

        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_bw' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'
        ));
        return $this->display(__FILE__, 'seqr_payment_option.tpl');

    }

    public function hookPaymentReturn($params) {

    }

    /**
     * Adds SEQR css definition to the header.
     * @param $params
     */
    public function hookDisplayHeader($params) {

        $this->context->controller->addCss($this->_path . 'css/seqr.css');
        $this->context->controller->addJS($this->_path . 'js/seqr.js');
    }
}





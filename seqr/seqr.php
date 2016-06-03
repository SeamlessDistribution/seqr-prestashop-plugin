<?php

require_once("lib/prestashop/package.php");

if (!defined("_PS_VERSION_"))
    exit;

/**
 * The entry point of the SEQR payment module.
 */
final class Seqr extends PaymentModule {

    public $config = null;

    public function __construct() {

        $this->name = "seqr";
        $this->tab = "payments_gateways";
        $this->version = "1.3.0";
        $this->author = "SEQR Team";
        $this->need_instance = 1;
        $this->is_configurable = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l("SEQR");
        $this->description = $this->l("Accepts payments by SEQR.");

        $this->confirmUninstall = $this->l("Are you sure you want to uninstall the SEQR module?");

        $this->loadConfiguration();
        $this->validateModuleSettings();
    }

    /**
     * Installs the SEQR module.
     * @return bool
     */
    public function install() {

        if (!parent::install()
            || !$this->registerHook("payment")
            || !$this->registerHook("header")
            || !$this->config->install()
            || !$this->installModuleMenu('SeqrRefunds', 'SEQR Refunds')
        ) {
            return false;
        }
        return true;
    }

    /**
     * Uninstall the SEQR module.
     * @return bool
     */
    public function uninstall() {
        if (
            !$this->removeModuleTab('SeqrRefunds')
            || !$this->removeModuleTab('SeqrRefundsParent')
            || !$this->config->uninstall()
            || !parent::uninstall()
        ) {
            return false;
        }
        return true;
    }

    /**
     * Prints configuration page in the admin module.
     * @return string
     */
    public function getContent() {

        $output = null;

        if (Tools::isSubmit("submit" . $this->name)) {
            // Saving configuration settings

            $user = strval(Tools::getValue(SeqrConfig::SEQR_USER_ID));
            $terminalId = strval(Tools::getValue(SeqrConfig::SEQR_TERMINAL_ID));
            $terminalPass = strval(Tools::getValue(SeqrConfig::SEQR_TERMINAL_PASS));
            $wsdl = strval(Tools::getValue(SeqrConfig::SEQR_WSDL));
            $timeout = strval(Tools::getValue(SeqrConfig::SEQR_PAYMENT_TIMEOUT));
            $hideLeft = Tools::getValue(PsConfig::SEQR_HIDE_LEFT. "_check");
            $hideRight = Tools::getValue(PsConfig::SEQR_HIDE_RIGHT. "_check");

            $valid = true;
            $valid = $this->validateValue($user, "Invalid user id", $output);
            $valid = $this->validateValue($terminalId, "Invalid terminal id", $output) && $valid;
            $valid = $this->validateValue($terminalPass, "Invalid terminal password", $output) && $valid;
            $valid = $this->validateValue($wsdl, "SEQR mode is not set", $output) && $valid;
            $valid = $this->validateValue($timeout, "Payment timeout is not set", $output) && $valid;

            if ($valid) {
                $newConfig = new PsConfig();
                $newConfig->populate(array(
                    SeqrConfig::SEQR_USER_ID => $user,
                    SeqrConfig::SEQR_TERMINAL_ID => $terminalId,
                    SeqrConfig::SEQR_TERMINAL_PASS => $terminalPass,
                    SeqrConfig::SEQR_WSDL => $wsdl,
                    SeqrConfig::SEQR_PAYMENT_TIMEOUT => $timeout
                ));
                $newConfig->hideLeftColumn = $hideLeft;
                $newConfig->hideRightColumn = $hideRight;
                $newConfig->save();
                $this->config = $newConfig;

                $output .= $this->displayConfirmation($this->l("Settings updated"));
            } else {
                $output .= $this->displayError($this->l("Please correct the form and try again"));
            }

        }
        return $output . $this->displayForm();
    }

    /**
     * Configuration form definition.
     * @return mixed
     */
    public function displayForm() {


        // Init Fields form array
        $fields_form[0]["form"] = array(
            "legend" => array(
                "title" => $this->l("SEQR Settings"),
            ),
            "input" => array(
                array(
                    "type" => "text",
                    "label" => $this->l("User id"),
                    "name" => SeqrConfig::SEQR_USER_ID,
                    "required" => true,
                    "size" => 70
                ),
                array(
                    "type" => "text",
                    "label" => $this->l("Terminal id"),
                    "name" => SeqrConfig::SEQR_TERMINAL_ID,
                    "required" => true,
                    "size" => 70
                ),
                array(
                    "type" => "text",
                    "label" => $this->l("Terminal password"),
                    "name" => SeqrConfig::SEQR_TERMINAL_PASS,
                    "required" => true,
                    "size" => 70
                ),
                array(
                    "type" => "text",
                    "label" => $this->l("Payment timeout (in seconds)"),
                    "name" => SeqrConfig::SEQR_PAYMENT_TIMEOUT,
                    "required" => true,
                    "size" => 70
                ),
                array(
                    "type" => "text",
                    "label" => $this->l("SEQR WSDL url"),
                    "name" => SeqrConfig::SEQR_WSDL,
                    "required" => true,
                    "size" => 70
                ),
                array(
                    "type" => "checkbox",
                    "label" => $this->l("Hide left column"),
                    "name" => PsConfig::SEQR_HIDE_LEFT,
                    "required" => false,
                    "values"    => array(
                        "query" => array(
                            array(
                                "id" => "check",
                                "name" => "",
                                "val" => 'true'

                            )
                        ),
                        "id" => "id",
                        "name" => "name"
                    )
                ),
                array(
                    "type" => "checkbox",
                    "label" => $this->l("Hide right column"),
                    "name" => PsConfig::SEQR_HIDE_RIGHT,
                    "required" => false,
                    "values"    => array(
                        "query" => array(
                            array(
                                "id" => "check",
                                "val" => 'true',
                                "name" => ""
                            )
                        ),
                        "id" => "id",
                        "name" => "name"
                    )
                )
            ),
            "submit" => array(
            "title" => $this->l("Save"),
        )
        );

        $helper = $this->initHelperForm();

        return $helper->generateForm($fields_form);
    }

    /**
     * Helper function used to validate configuration values provided by the user.
     * @param $value
     * @param $errorMessage
     * @param $output
     * @return bool
     */
    private function validateValue($value, $errorMessage, &$output) {

        $validation = !empty($value) && Validate::isGenericName($value);
        if (!$validation) {
            $output .= $this->displayError($this->l($errorMessage));
        }
        return $validation;
    }

    /**
     * Hook payment, displays SEQR payment option on payment selection page.
     * @param $params
     * @return array|mixed|string|void
     */
    public function hookPayment($params) {

        if (!$this->active) {
            return;
        }

        // Check if configuration is valid
        if (!$this->config->isValid()) {
            return;
        }

        $this->smarty->assign(array(
            "this_path" => $this->_path,
            "this_path_bw" => $this->_path,
            "shopVersion" => $this->getShopVersion(),
            "this_path_ssl" => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . "modules/" . $this->name . "/"
        ));
        return $this->display(__FILE__, "seqr_payment_option.tpl");

    }

    /**
     * Adds SEQR css and JS definitions to the header.
     * @param $params
     */
    public function hookDisplayHeader($params) {

        $this->context->controller->addCss($this->_path . $this->getForVersion("css/seqr.css", dirname(__FILE__)));
        $this->context->controller->addJS($this->_path . "js/seqr.js");
    }

    private function getForVersion($file, $path = "") {

        $version = $this->getShopVersion();

        if ($file) {
            $name = substr($file, 0, strpos($file, "."));
            $ext = substr($file, strpos($file, ".") + 1, strlen($file));

            $fileForVer = $name . $version . "." . $ext;
            $filePath = $path == "" ? $fileForVer : $path . "/" . $fileForVer;
            if (file_exists($filePath)) {
                return $fileForVer;
            }

            return $file;
        }
        return null;
    }

    /**
     * Gets Prestashop version in XX format.
     * - 15 means 1.5.x version
     * - 16 means 1.6.x version
     * @return string
     */
    public function getShopVersion() {

        $version = _PS_VERSION_;
        return intval(substr($version, 0, 1) . substr($version, 2, 1));
    }

    private function installModuleTab($tabClass, $tabName, $parentId)
    {
    	$tab = new Tab();
	foreach(Language::getLanguages(true) as $lang){
                $tab->name[(int) $lang['id_lang']] = $tabName;
        }
    	$tab->class_name = $tabClass;
    	$tab->module = $this->name;
    	$tab->id_parent = $parentId;
    	$tab->active = 1;
    	if(!$tab->save())
    		return false;
    	return true;
    }

    private function installModuleMenu($tabClass, $tabName)
    {
    	if (!$this->installModuleTab($tabClass.'Parent', $tabName, 0))
    		return false;
    	$tab = new Tab((int)Tab::getIdFromClassName($tabClass.'Parent'));
    	return $this->installModuleTab($tabClass, $tabName, $tab->id);
    }
    
    private function removeModuleTab($tabClass) {
    	$tab = new Tab((int)Tab::getIdFromClassName($tabClass));
    	if($tab)
    		$tab->delete();
    	return true;
    }
    
    /**
     * Validates module settings.
     */
    private function validateModuleSettings() {

        if (!$this->config->isValid()) {
            $this->warning = $this->l("The SEQR plugin must be configured in order to use this module correctly");
        }
    }

    protected function loadConfiguration() {
        $this->config = new PsConfig();
        $this->config->load();
    }

    /**
     * @return HelperForm
     */
    protected function initHelperForm() {

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite("AdminModules");
        $helper->currentIndex = AdminController::$currentIndex . "&configure=" . $this->name;

        // Language
        $default_lang = (int)Configuration::get("PS_LANG_DEFAULT");
        $helper->default_form_language = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = "submit" . $this->name;
        $helper->toolbar_btn = array(
            "save" =>
                array(
                    "desc" => $this->l("Save"),
                    "href" => AdminController::$currentIndex . "&configure=" . $this->name . "&save" . $this->name .
                        "&token=" . Tools::getAdminTokenLite("AdminModules"),
                ),
            "back" => array(
                "href" => AdminController::$currentIndex . "&token=" . Tools::getAdminTokenLite("AdminModules"),
                "desc" => $this->l("Back to list")
            )
        );

        // Load current value
        $helper->fields_value[SeqrConfig::SEQR_USER_ID] = $this->config->getUserId();
        $helper->fields_value[SeqrConfig::SEQR_TERMINAL_ID] = $this->config->getTerminalId();
        $helper->fields_value[SeqrConfig::SEQR_TERMINAL_PASS] = $this->config->getTerminalPass();
        $helper->fields_value[SeqrConfig::SEQR_WSDL] = $this->config->getWsdl();
        $helper->fields_value[SeqrConfig::SEQR_PAYMENT_TIMEOUT] = $this->config->getTimeout();
        $helper->fields_value[PsConfig::SEQR_HIDE_LEFT. "_check"] = $this->config->hideLeftColumn;
        $helper->fields_value[PsConfig::SEQR_HIDE_RIGHT . "_check"] = $this->config->hideRightColumn;

        return $helper;
    }
}





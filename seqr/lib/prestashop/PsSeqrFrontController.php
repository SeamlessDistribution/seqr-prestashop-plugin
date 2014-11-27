<?php

/**
 * Abstract front controller.
 *
 * Created by IntelliJ IDEA.
 * User: kmanka
 * Date: 25/11/14
 * Time: 14:33
 */
abstract class PsSeqrFrontController extends ModuleFrontController {

    protected $config = null;

    public function __construct() {

        parent::__construct();

        $this->ssl = true;
        $this->config = $this->module->config;
        $this->assingSmartyDefault();
        $this->hideColumns();
    }

    /**
     * @return string
     */
    protected function getShopUrl() {
        return Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__;
    }

    /**
     * @return string
     */
    protected function getModuleUrl() {
        return $this->getShopUrl() . "module/" . $this->module->name;
    }

    /**
     * @return string
     */
    protected function getModulePath() {
        return $this->module->getPathUri();
    }

    protected function assingSmartyDefault() {

        $this->context->smarty->assign(array(
            "shopVersion" => $this->module->getShopVersion(),
            "this_path" => $this->getModulePath(),
            "this_path_bw" => $this->getModulePath(),
            "this_path_ssl" => $this->getModuleUrl() . "/",
            "shopUrl" => $this->getShopUrl()
        ));

//        $this->context->smarty->
    }

    protected function displayTemplate($templateName) {

        $tplPath = $this->getTplPath($templateName);
        $this->context->smarty->display($tplPath);
    }

    /**
     * @param $templateName
     * @throws Exception
     * @return string
     */
    protected function getTplPath($templateName) {

        if (Tools::file_exists_cache(_PS_THEME_DIR_ . 'modules/' . $this->module->name . '/' . $templateName))
            return _PS_THEME_DIR_ . 'modules/' . $this->module->name . '/' . $templateName;
        elseif (Tools::file_exists_cache(_PS_THEME_DIR_ . 'modules/' . $this->module->name . '/views/templates/front/' . $templateName))
            return _PS_THEME_DIR_ . 'modules/' . $this->module->name . '/views/templates/front/' . $templateName;
        elseif (Tools::file_exists_cache(_PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/' . $templateName))
            return _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/' . $templateName;

        $path = _PS_MODULE_DIR_ . $this->module->name . '/views/templates/front/' . $templateName;
        if (!file_exists($path)) throw new Exception("Template does not exist: " . $path);
        return $path;
    }

    protected function assignBreadcrumb() {

        $tplFile = $this->getTplPath("breadcrumb.tpl");
        $this->context->smarty->assign("breadcrumb", $tplFile);
    }

    protected function assignNavigation() {

        $tplFile = $this->getTplPath("navigation.tpl");
        $this->context->smarty->assign("navigation", $tplFile);
    }

    public function verifyUserLogged() {

        global $cookie;
        if (!$cookie->isLogged()) {
            Tools::redirect("authentication.php?back=order.php");
        }
    }

    private function hideColumns() {

        if ($this->config->hideLeftColumn) $this->display_column_left = !$this->config->hideLeftColumn;
        if ($this->config->hideRightColumn) $this->display_column_right = !$this->config->hideRightColumn;
    }
}
<?php

interface SeqrConfig {

    const SEQR_MODE = 'SEQR_MODE';
    const SEQR_MODE_DEMO = 'demo';
    const SEQR_MODE_LIVE = 'live';
    const SEQR_DEMO_WSDL = 'SEQR_DEMO_WSDL';
    const SEQR_LIVE_WSDL = 'SEQR_LIVE_WSDL';
    const SEQR_USER_ID = 'SEQR_USER_ID';
    const SEQR_TERMINAL_ID = 'SEQR_TERMINAL_ID';
    const SEQR_TERMINAL_PASS = 'SEQR_TERMINAL_PASS';
    const SEQR_MODULE_INSTALLED = 'SEQR_MODULE_INSTALLED';
    const SEQR_PAYMENT_TIMEOUT = 'SEQR_PAYMENT_TIMEOUT';

    // Statuses
    const SEQR_PAYMENT_ISSUED = "ISSUED";
    const SEQR_PAYMENT_PAID = "PAID";
    const SEQR_PAYMENT_CANCELED = "CANCELED";
    const SEQR_PAYMENT_ERROR = "ERROR";

    public function populate($params);
    public function save();
    public function load();
    public function install();
    public function uninstall();
    public function isInstalled();
    public function isValid();
    public function isDemoMode();

    public function getUserId();
    public function getTerminalId();
    public function getTerminalPass();
    public function getMode();
    public function getWsdl();

    public function getSeqrModuleUrl();
    public function getTimeout();

}
<?php

class SeqrRefundsController extends ModuleAdminController {

	public function __construct()
	{
		parent::__construct();
		$this->bootstrap = true;
		
 		$this->context = Context::getContext();
		$this->tpl_folder = '';
		$this->override_folder = '';
	}
	
	public function initContent() {
		parent::initContent();
		
        $smarty = $this->context->smarty;
        
        $smarty->assign('seqrPayments', $this->seqrTransactionsAndRefunds());

        $this->setTemplate('refunds.tpl');
	}

	private function seqrTransactionsAndRefunds() {
 		$fields = 'o.id_order, 
 				  CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customerName`,
 				  total_paid,
 				  s.amount_refunded as returned,
 				  o.total_shipping_tax_incl as shipping_cost';
		$sql = 'SELECT '.$fields.' FROM '._DB_PREFIX_.'orders o
 				LEFT JOIN '._DB_PREFIX_.'customer c ON o.id_customer = c.id_customer
 				INNER JOIN '._DB_PREFIX_.'seqr s ON o.id_order = s.id_order
 			    WHERE o.payment = \'SEQR\' AND s.status=\'PAID\'
 				ORDER BY id_order DESC';

 		$results = Db::getInstance()->ExecuteS($sql);
 		if (sizeof($results) > 0) {
 			$link = $this->context->link;
 			foreach ($results as &$row) {
 				$row["order_link"] = $link->getAdminLink('AdminOrders').'&vieworder&id_order='.$row["id_order"];
 				$suggested = $row['total_paid'] - $row['shipping_cost'] - $row['returned'];
 				$row["suggested_return"] = $suggested > 0 ? $suggested : 0;
 			}
 		}

		return $results;
	}

	/**
	 * Process refund.
	 */
	public function postProcess() {
		if (!isset($_POST['id_order']) || !isset($_POST['return'])) {
            return false;
        }

		$order = new Order($_POST['id_order']);
		$service = new PsSeqrService($this->module->config, $order);
		$service->refundPayment($_POST['return']);
	}

	public function initModal()
	{
		parent::initModal();

		$modal_content = $this->context->smarty->fetch($this->getTemplatePath() . 'refund_confirmation.tpl');
		$this->modals[] = array(
			'modal_id' => 'refundConfirmation',
			'modal_class' => 'modal-lg',
			'modal_title' => $this->l('SEQR refund confirmation'),
			'modal_content' => $modal_content
		);
	}
}
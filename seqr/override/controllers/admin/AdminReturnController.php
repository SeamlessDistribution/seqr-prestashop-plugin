<?php

/**
 * Created by IntelliJ IDEA.
 * User: nietsnie
 * Date: 29.12.15
 * Time: 10:19
 */
class AdminReturnController extends AdminReturnControllerCore
{
    public function postProcess()
    {
        $this->context = Context::getContext();
        if (Tools::isSubmit('deleteorder_return_detail')) {
            if ($this->tabAccess['delete'] === '1') {
                if (($id_order_detail = (int)(Tools::getValue('id_order_detail'))) && Validate::isUnsignedId($id_order_detail)) {
                    if (($id_order_return = (int)(Tools::getValue('id_order_return'))) && Validate::isUnsignedId($id_order_return)) {
                        $orderReturn = new OrderReturn($id_order_return);
                        if (!Validate::isLoadedObject($orderReturn)) {
                            die(Tools::displayError());
                        }
                        if ((int)($orderReturn->countProduct()) > 1) {
                            if (OrderReturn::deleteOrderReturnDetail($id_order_return, $id_order_detail, (int)(Tools::getValue('id_customization', 0)))) {
                                Tools::redirectAdmin(self::$currentIndex . '&conf=4token=' . $this->token);
                            } else {
                                $this->errors[] = Tools::displayError('An error occurred while deleting the details of your order return.');
                            }
                        } else {
                            $this->errors[] = Tools::displayError('You need at least one product.');
                        }
                    } else {
                        $this->errors[] = Tools::displayError('The order return is invalid.');
                    }
                } else {
                    $this->errors[] = Tools::displayError('The order return content is invalid.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
        } elseif (Tools::isSubmit('submitAddorder_return') || Tools::isSubmit('submitAddorder_returnAndStay')) {
            if ($this->tabAccess['edit'] === '1') {
                if (($id_order_return = (int)(Tools::getValue('id_order_return'))) && Validate::isUnsignedId($id_order_return)) {
                    $orderReturn = new OrderReturn($id_order_return);
                    $order = new Order($orderReturn->id_order);
                    $customer = new Customer($orderReturn->id_customer);
                    $orderReturn->state = (int)(Tools::getValue('state'));
                    if ($orderReturn->save()) {
                        $orderReturnState = new OrderReturnState($orderReturn->state);
                        $vars = array(
                            '{lastname}' => $customer->lastname,
                            '{firstname}' => $customer->firstname,
                            '{id_order_return}' => $id_order_return,
                            '{state_order_return}' => (isset($orderReturnState->name[(int)$order->id_lang]) ? $orderReturnState->name[(int)$order->id_lang] : $orderReturnState->name[(int)Configuration::get('PS_LANG_DEFAULT')]));
                        Mail::Send((int)$order->id_lang, 'order_return_state', Mail::l('Your order return status has changed', $order->id_lang),
                            $vars, $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null,
                            null, _PS_MAIL_DIR_, true, (int)$order->id_shop);

                        // Added to react on order return status changes
                        Hook::exec('actionOrderReturnState', array(
                            'orderReturn' => $orderReturn,
                            'orderReturnState' => $orderReturnState,
                            'order' => $order
                        ));

                        if (Tools::isSubmit('submitAddorder_returnAndStay')) {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token . '&updateorder_return&id_order_return=' . (int)$id_order_return);
                        } else {
                            Tools::redirectAdmin(self::$currentIndex . '&conf=4&token=' . $this->token);
                        }
                    }
                } else {
                    $this->errors[] = Tools::displayError('No order return ID has been specified.');
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        AdminReturnControllerCore::postProcess();
    }
}
{assign var='headName' value="Payment summary"}

{include file="$breadcrumb"}

{if $nbProducts <= 0}
    <p class="warning">{l s='Your shopping cart is empty.' mod='seqr'}</p>
{else}
    <form action="{$link->getModuleLink('seqr', 'paymentcode', [], true)|escape:'html'}" method="post">

        <div class="box cheque-box">
            <h3 class="page-subheading">
                SEQR payment.
            </h3>

            <p class="cheque-indent">
                <strong class="dark">
                    {l s='You have chosen to pay by SEQR.' mod='seqr'}
                    {l s='Here is a short summary of your order:' mod='seqr'}
                </strong>
            </p>

            <p>
                - {l s='The total amount of your order comes to:' mod='seqr'}
                <strong>
                    <span id="amount" class="price">{displayPrice price=$total}</span>
                </strong>
                {if $use_taxes == 1}
                    {l s='(tax incl.)' mod='seqr'}
                {/if}
            </p>

            <p>
                - {l s='We allow the following currency to be sent via SEQR:' mod='seqr'}&nbsp;
                <b>{$currency->iso_code}</b>
                <input type="hidden" name="currency_payment" value="{$currency->id}"/>
            </p>

            <p>
                - {l s='The QR code for this payment will be displayed on the next page.' mod='seqr'}
                <br/><br/>
            </p>

            <p>
                <strong class="dark">
                    <b>{l s='Please confirm your order by clicking "I confirm my order".' mod='seqr'}</b>
                </strong>
            </p>
        </div>
        <p class="cart_navigation clearfix" id="cart_navigation">
            {if $shopVersion >= 16}
                <a class="button-exclusive btn btn-default"
                   href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}">
                    <i class="icon-chevron-left"></i>
                    {l s='Other payment methods' mod='seqr'}
                </a>
                <button class="button btn btn-default button-medium" type="submit">
                    <span>
                        {l s='I confirm my order' mod='seqr'}
                        <i class="icon-chevron-right right"></i>
                    </span>
                </button>
            {/if}
            {if $shopVersion == 15}
                <a class="button_large"
                   href="{$link->getPageLink('order', true, NULL, "step=3")|escape:'html'}">
                    {l s='Other payment methods' mod='seqr'}
                </a>
                <input class="exclusive_large" type="submit" value="{l s='I confirm my order' mod='seqr'}" />
            {/if}
        </p>
    </form>
{/if}

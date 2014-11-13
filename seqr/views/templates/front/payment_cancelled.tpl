{capture name=path}{l s='SEQR payment' mod='seqr'}{/capture}
{*{include file="$tpl_dir./breadcrumb.tpl"}*}

<h1 class="page-heading">{l s='Payment cancelled' mod='seqr'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="col-sm-10 col-lg-10 col-lg-offset-1 col-sm-offset-1 col-xs-12 seqr-confirmation">
    <h2>{l s="Your payment was cancelled by SEQR, if you still want to buy chosen goods please checkout them again."}</h2>
</div>

<p class="cart_navigation clearfix" id="cart_navigation">
    <a class="button-exclusive btn btn-default" href="{$shopUrl}">
        <i class="icon-chevron-left"></i>
        {l s='Back to shop' mod='seqr'}
    </a>
    <a class="button btn btn-default button-medium" href="{$shopUrl}order-history">
        <span>
            {l s='Orders' mod='seqr'}
            <i class="icon-chevron-right right"></i>
        </span>
    </a>
</p>



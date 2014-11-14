{include file="$breadcrumb"}

<h1 class="page-heading">{l s='Payment failed' mod='seqr'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

<div class="col-sm-10 col-lg-10 col-lg-offset-1 col-sm-offset-1 col-xs-12 seqr-confirmation">
    <h2>{l s="Your payment was failed, if you still want to buy chosen goods please checkout them again."}</h2>
</div>

{include file="$navigation"}

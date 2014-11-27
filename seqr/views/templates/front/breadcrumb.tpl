{capture name=path}{l s='SEQR payment' mod='seqr'}{/capture}


{if $shopVersion == 15}
    {include file="$tpl_dir./breadcrumb.tpl"}
{/if}

<h2 class="page-heading">{l s=$headName mod="seqr"}</h2>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}

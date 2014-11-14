{include file="$breadcrumb"}

<h1 class="page-heading">{l s='SEQR payment code' mod='seqr'}</h1>

{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}


<link rel="stylesheet" type="text/css" href="https://cdn.seqr.com/webshop-plugin/css/seqrShop.css">
<script type="text/javascript">
    (function () {
        window.seqr.id = "{$orderId}";
        window.seqr.backUrl = "{$backUrl}";
    }());
</script>

<div class="col-lg-6 col-lg-offset-3 col-sm-12 col-xs-12 seqr-box">
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <h2>{l s="Total amount:"}</h2>
    </div>
    <div class="col-lg-6 col-sm-12 col-xs-12">
        <h2>{$total} {$currencies.0.iso_code}</h2>
    </div>
</div>
<div class="col-lg-6 col-lg-offset-3 col-sm-12 col-xs-12 seqr-box">
    <script id="seqrShop" src="{$webPluginUrl}" type="text/javascript"></script>
</div>


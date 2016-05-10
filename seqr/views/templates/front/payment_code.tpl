{assign var='headName' value='SEQR payment code'}

{include file="$breadcrumb"}

<link rel="stylesheet" type="text/css" href="https://cdn.seqr.com/webshop-plugin/css/seqrShop.css">
<script type="text/javascript">
    (function () {
        window.seqr.backUrl = "{$backUrl}";
    }());
</script>

<div class="seqr-box">
    <h2>{l s="Total amount: "} {$total} {$currency->iso_code}</h2>
</div>
<div class="seqr-box">
    <div id="seqrQRCode"></div>
    <script id="seqrShop" src="{$webPluginUrl}" type="text/javascript"></script>
</div>


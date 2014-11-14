<div class="row">
    <div class="col-xs-12 col-md-6">
        <p class="payment_module">
            <a class="seqr" href="{$link->getModuleLink('seqr', 'payment')|escape:'html'}" title="{l s='Pay by SEQR' mod='seqr'}">
                {if $shopVersion == 15 }
                    <img src="{$this_path}/img/seqr-logo.png" alt="{l s='Pay by SEQR' mod='seqr'} " width="86">
                {/if}
                {l s='Pay by SEQR' mod='seqr'}
                <span>{l s="(Secure and fast payments)" mod='seqr'}</span>
            </a>
        </p>
    </div>
</div>


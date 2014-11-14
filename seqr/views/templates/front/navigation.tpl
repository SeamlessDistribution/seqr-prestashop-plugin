<p class="cart_navigation clearfix" id="cart_navigation">
    {if $shopVersion >= 16}
        <a class="button-exclusive btn btn-default button_large" href="{$link->getPageLink('', true, NULL)|escape:'html'}">
            <i class="icon-chevron-left"></i>
            {l s='Continue shopping' mod='seqr'}
        </a>
        <a class="button btn btn-default button-medium" href="{$link->getPageLink('history', true, NULL)|escape:'html'}">
            <span>
                {l s='Orders' mod='seqr'}
                <i class="icon-chevron-right right"></i>
            </span>
        </a>
    {/if}
    {if $shopVersion == 15}
        <a class="button_large" href="{$link->getPageLink('', true, NULL)|escape:'html'}">
            {l s='Continue shopping' mod='seqr'}
        </a>
        <a class="exclusive_large"  href="{$link->getPageLink('history', true, NULL)|escape:'html'}">
            <span>
                {l s='Orders' mod='seqr'}
            </span>
        </a>
    {/if}
</p>

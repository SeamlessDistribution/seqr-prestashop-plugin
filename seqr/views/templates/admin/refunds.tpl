<style type="text/css">
    {literal}
    .table {
        display: table;
        width: 100%;
    }

    .table > * {
        display: table-row;

    }

    .table > *.table-header {
        font-weight: bold;
    }

    .table > * > * {
        display: table-cell;
        padding: 5px 0;
    }

    {/literal}
</style>
<script type="text/javascript">
    window.refund = {
        orderId: null,

        submitRefund: function() {
            $('#' + this.orderId + "_form").submit();
        },

        selectRefund: function(orderId) {
            this.orderId = orderId;
            $("#refund-confirmation").trigger("beforeShow");
        }
    };
</script>

{include file="$tpl_dir./errors.tpl"}

<div class="panel">
    <div class="panel-heading">
        SEQR Payments
    </div>
    <div class="table">
        <div class="table-header">
            <div>Id</div>
            <div>Customer Name</div>
            <div>Order value</div>
            <div>Shipping cost</div>
            <div>Returned</div>
            <div>Return value</div>
        </div>
        {foreach $seqrPayments as $row}
            <form method="post" id="{$row['id_order']}_form" action="{$link->getAdminLink('SeqrRefunds',true)|escape:'html':'UTF-8'}">
                <div>
                    <a href="{$row['order_link']}">{$row['id_order']}</a>
                    <input type="hidden" name="id_order" value="{$row['id_order']}"/>
                </div>
                <div id="{$row['id_order']}_customer_name">{$row['customerName']}</div>
                <div id="{$row['id_order']}_total">{convertPrice price=$row['total_paid']}</div>
                <div id="{$row['id_order']}_shipping">{convertPrice price=$row['shipping_cost']}</div>
                <div id="{$row['id_order']}_returned">{convertPrice price=$row['returned']}</div>
                <div>
                    {*<input class="button btn btn-default button-medium" type="submit" value="Refund"/>*}
                    {if $row['total_paid'] - $row['returned'] == 0}
                    	Fully refunded
                    {else}
                    	<input id="{$row['id_order']}_to_return" name="return" type="number" step="0.01" min="0"
                           max="{$row['total_paid'] - $row['returned']}" value="{$row['suggested_return']}"/>
                    	<a id="{$row['id_order']}_refund_btn" class="btn btn-primary" style="margin-left: 10px;" href="#" data-target="#refundConfirmation"
                       data-toggle="modal" data-link="" data-module-display-name="SEQR" data-module-name="seqr"
                       data-module-image="/modules/seqr/logo.png" data-author-name="SEQR Team" data-author-uri="" onclick="window.refund.selectRefund({$row['id_order']})">
                        Refund
                    	</a>
                    {/if}
                </div>
            </form>
        {/foreach}
    </div>
</div>

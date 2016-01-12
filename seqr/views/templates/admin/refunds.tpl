{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<style type="text/css">
{literal}
.table { display: table; width: 100%;} 
.table>* { display: table-row; }
.table>*.table-header { font-weight: bold; }
.table>*>* { display: table-cell; }
{/literal}
</style>
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
			<form method ="post" action="{$link->getAdminLink('SeqrRefunds',true)|escape:'html':'UTF-8'}">
				<div>
					<a href = "{$row['order_link']}">{$row['id_order']}</a>
					<input type="hidden" name="id_order" value="{$row['id_order']}" />
				</div>
				<div>{$row['customerName']}</div>
				<div>{convertPrice price=$row['total_paid']}</div>
				<div>{convertPrice price=$row['shipping_cost']}</div>
				<div>{convertPrice price=$row['returned']}</div>
				<div>
					<input name="return" type="number" step="0.01" min="0" max="{$row['total_paid'] - $row['returned']}" value="{$row['suggested_return']}"/>
					<input class="button btn btn-default button-medium" type="submit" value="Refund"/>
				</div>
			</form>
		{/foreach}
	</div>
</div>
{/block}

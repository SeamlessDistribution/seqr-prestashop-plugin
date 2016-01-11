{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<div class="table">
	<div>
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
			<div>{$row['total_paid']}</div>
			<div>{$row['shipping_cost']}</div>
			<div>{$row['returned']}</div>
			<div>
				<input name="return" type="number" step="any" min="0" max="{$row['total_paid'] - $row['returned']}" value="{$row['total_paid'] - $row['shipping_cost'] - $row['returned']}"/>
				<input class="button btn btn-default button-medium" type="submit" value="Refund"/>
			</div>
		</form>
	{/foreach}
</div>
{/block}

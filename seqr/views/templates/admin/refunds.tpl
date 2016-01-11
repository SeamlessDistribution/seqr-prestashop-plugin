{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<div class="table">
	<div>
		<div>Id</div><div>Customer Name</div><div>Order value</div><div>Returned</div><div>Return value</div>
	</div>
	{foreach $seqrPayments as $row}
		<form method ="post" action="{$link->getAdminLink('SeqrRefunds',true)|escape:'html':'UTF-8'}">
			<div>
				<a href = "{$row['order_link']}">{$row['id_order']}</a>
				<input type="hidden" name="id_order" value="{$row['id_order']}" />
			</div>
			<div>{$row['customerName']}</div>
			<div>{$row['total_paid']}</div>
			<div>{$row['returned']}</div>
			<div>
				<input name="return" type="number" step="any" />
				<input class="button btn btn-default button-medium" type="submit" value="Refund"/>
			</div>
		</form>
	{/foreach}
</div>
{/block}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
<div class="table">
	<div>
		<div>Id</div><div>Customer Name</div><div>Order value</div><div>Returned</div><div>Return value</div><div></div>
	</div>
	{foreach $seqrPayments as $row}
		<form>
			<div><a href = "{$row['order_link']}">{$row['id_order']}</a></div>
			<div>{$row['customerName']}</div>
			<div>{$row['total_paid']}</div>
			<div>{$row['returned']}</div>
			<div><input type="number" step="any"></div>
			<div><input type="submit" value="Refund"/></div>
		</form>
	{/foreach}
</div>
{/block}

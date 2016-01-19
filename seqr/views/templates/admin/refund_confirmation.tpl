{assign var=module_name value='<strong><span class="module-display-name-placeholder"></span></strong>'}

<div class="modal-body" id="refund-confirmation">
    <div class="alert alert-info">
        <h4>{l s='You are about to refund money to the customer'}</h4>
    </div>
    <h3>{l s='Order details:'}</h3>
    <div class="center">
        <div><b>{l s='Customer name'}:</b> <span id="customer_name"></span></div>
        <div><b>{l s='Order id'}:</b> <span id="order_id"></span></div>
        <div><b>{l s='Total amount'}:</b> <span id="total"></span></div>
        <div><b>{l s='Shipping'}:</b> <span id="shipping"></span></div>
        <div><b>{l s='Already returned'}:</b> <span id="returned"></span></div>
        <div><b>{l s='Return'}:</b> <span class="return" style="font-style: oblique"></span></div>
    </div>
    <h4>{l s='Do you want to return '}<span class="return" style="font-style: oblique"></span> to the cutomer?</h4>
    <script type="text/javascript">
        $("#refund-confirmation").bind("beforeShow", function() {
            var orderId = window.refund.orderId;
            $("#customer_name").html($("#" + orderId + "_customer_name").text());
            $("#order_id").html(orderId);
            $("#total").html($("#" + orderId + "_total").text());
            $("#shipping").html($("#" + orderId + "_shipping").text());
            $("#returned").html($("#" + orderId + "_returned").text());
            $(".return").html($("#" + orderId + "_to_return").val());
        });
    </script>

</div>

<div class="modal-footer">
    <div class="row">
        <div class="col-sm-12 text-center">
            <a id="proceed-refund-anyway" href="#" class="btn btn-primary" onclick="window.refund.submitRefund()">{l s='Proceed with the refund'}</a>
            <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancel'}</button>
        </div>
    </div>
</div>

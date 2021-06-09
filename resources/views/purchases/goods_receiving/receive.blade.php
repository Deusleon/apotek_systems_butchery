<style>
    #select1 {
        z-index: 10050;
    }
</style>

<div class="modal fade" id="receive" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Item Receive</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="order_reveice" name="return-form"
                  enctype="multipart/form-data">
                @csrf()
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Item Name</label>
                                <input type="text" name="product" class="form-control" id="name_of_item" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Invoice #</label>
                                <select name="invoice" class="form-control" id="invoice_ids">
                                    <option value="" selected="true" disabled>Select Option</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="batch_number">Batch Number</label>
                                <input type="text" name="batch_number" class="form-control" id="batch_number">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_category">Price Category</label>
                                <select name="price_category" class="form-control" id="price_cat">
                                    @foreach($price_categories as $price_category)
                                        <option value="{{$price_category->id}}">{{$price_category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                @if($expire_date === "YES")
                                    <label for="expire_date">Expire Date</label>
                                    <!-- <input type="text" name="expire_date" class="form-control" id="expire_date_1"
                                            autocomplete="off"> -->
                                    <input type="text" name = "expire_date" class="form-control" id="expire_date_1">
                                    <div class="form-group form-check">
                                        <input type="checkbox" class="form-check-input" id="expire_check_id"
                                                style="padding:10px" value="true" onchange="findchecked()">
                                        <label class="form-check-label" for="expire_check">No Expire Date</label>
                                    </div>
                                @else
                                    <label for="batch_number">Expire Date</label>
                                    <input type="text" name="expire_date" class="form-control" id="expire_date_1"
                                            autocomplete="off" readonly disabled>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supplier">Quantity Received</label>
                                <input type="text" name="quantity" id="rec_qty" class="form-control" required="true"
                                       min="1" onkeypress="return isNumberKey(event,this)"
                                       onchange="toCommas(this.value)">
                                <span class="help-inline">
                                  <div class="text text-danger" id="qty_error"></div>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Buying Price</label>
                                <input type="text" name="price" class="form-control" id="pr_id"
                                       required='true' onchange="orderamountCheck()"
                                       onkeypress="return isNumberKey(event,this)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="supplier">Selling Price</label>
                                <input type="text" name="sell_price" id="sell_price_i" class="form-control"
                                       required="true" onchange="orderamountCheck()"
                                       onkeypress="return isNumberKey(event,this)">
                                <span class="help-inline"></span>
                                <div class=" amount_error text text-danger">
                                </div>
                            </div>
                            <input type="hidden" id="product-id" name="product_id">
                            <input type="hidden" id="order-item-id" name="order_details_id">
                            <input type="hidden" id="id_of_supplier" name="supplier_id">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group" style="float: right;">
                                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                    <button class="btn btn-primary" id="save_btn">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    var a = 1;

    function findchecked() {
        a = -a;
        if (a < 1) {
            document.getElementById("expire_date_1").setAttribute('disabled', false);
        } else {
            document.getElementById("expire_date_1").removeAttribute('disabled');
        }
    }

    var configurations = {
        routes: {
            goodsreceiving: '{{route('product-price-category')}}'
        }
    };

</script>


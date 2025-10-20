<style>
    #select1 {
        z-index: 10050;
    }
</style>

<div class="modal fade" id="editselected_product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="width: 60%">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Selected Product</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="order_receive" name="return-form"
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
                                <label for="code">Quantity</label>
                                <input type="text" name="quantity" class="form-control" id="item_quantity">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group" style="padding-top: 10px">
                                {{-- <div style="width: 99%">
                                    <label for="invoiceprice_category">Price Category <font color="red">*</font></label>
                                    <select name="price_category" class="form-control"
                                            id="invoiceprice_category" required="true" onchange="invoicepriceByCategory()">
                                        @foreach($price_categories as $price_category)
                                            <option value="{{$price_category->id}}">{{$price_category->name}}</option>
                                        @endforeach
                                    </select>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="padding-top: 10px">
                                <label for="invoice_buy_price">Buy Price<font color="red">*</font></label>
                                <input type="text" id="invoice_buy_price" name="unit_cost" class="form-control" min="0"
                                       value="0" required="true" onchange="amountCheck()"
                                       onkeypress="return isNumberKey(event,this)">
                                <span class="help-inline"></span>
                                <div class="text text-danger" class="price_error"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group" style="padding-top: 10px">
                                <label for="invoice_sell_price_id">Sell Price<font color="red">*</font></label>
                                <input type="text" name="sell_price" class="form-control" min="0" value="0"
                                       required="true" id="invoice_sell_price_id" onchange="amountCheck()"
                                       onkeypress="return isNumberKey(event,this)">
                                <span class="help-inline"></span>
                                <div class="amount_error text text-danger"></div>
                            </div>
                        </div>
                        {{-- <div class="col-md-6">
                            <div class="form-group">
                                <label for="price_category">Price Category</label>
                                <select name="price_category" class="form-control" id="price_cat">
                                    @foreach($price_categories as $price_category)
                                        <option value="{{$price_category->id}}">{{$price_category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> --}}
                        <input type="hidden" id="product_id" name="product_id">
                        <input type="hidden" id="id_of_supplier" name="supplier_id">
                    </div>
                    <div class="col-md-12">
                        <div class="form-group" style="padding-top: 10px">
                            <label>Expiry Date <font color="red">*</font></label>
                                <input type="text" name="expire_date" class="form-control" id="expire_date_21"
                                       autocomplete="off" required="true">

                                <div class="form-group form-check">
                                    <input type="checkbox" class="form-check-input" id="expire_check"
                                           style="padding:10px" value="true" onchange="findselected()">
                                    <label class="form-check-label" for="expire_check">No Expiry Date</label>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="btn-group" style="float: right;">
                                    <button class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                    <button type="submit" class="btn btn-primary" id="save_btn">Save</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>



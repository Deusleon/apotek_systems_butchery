<div style="display: none" class="modal hide fade" id="delete" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">
    <div class="modal-dialog " role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Price Category</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('price-categories.destroy','id') }}" method="post">
                @csrf
                @method("DELETE")

                <div class="modal-body">
                    <div id="message"></div>

                    <input type="hidden" name="price_category_id" id="price_category_id" value="">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-danger btn-sm">Yes</button>
                </div>
            </form>

        </div>
    </div>
</div>

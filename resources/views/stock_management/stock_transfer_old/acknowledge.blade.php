<div class="modal fade" id="acknowledge_all" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">

            <form action="{{route('acknowledge-all')}}" method="POST">
                @csrf

                @if(isset($all_transfers[0]))
                <input type="hidden" name="transfer_no" value="{{ $all_transfers[0]->transfer_no }}">
                @endif
                <div class="modal-body">
                    <p class="text-center" id = "prompt_message"> Dear {{ Auth::user()->name ?? '' }} , Are you sure you want to acknowledge all ? </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">No</button>
                    <button type="submit" class="btn btn-primary btn-sm">Yes</button>
                </div>
            </form>

        </div>
    </div>
</div>

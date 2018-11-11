@extends('Views.layouts.master')

@section('Content')
<div class="row AdminLogin">
    <div class="col-md-2">&nbsp;</div>
    <div class="col-md-8">


        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="login">
                    <div class="modal-header">
                        <button type="button" class="close CloseIt" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">Forgot Password</h4>
                    </div>
                    <div class="modal-body">
                        <?=ShowMessage($SESSION->message(), $SESSION->messageType())?>
                        <div class="form-group">
                            <label for="uname" class="control-label">Username:</label>
                            <input type="text" class="form-control" id="uname" name="uname" value="{{$UName}}" />
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default CloseIt" data-dismiss="modal">Close</button>
                        <input type="submit" class="btn btn-primary" value="Get Password">
                    </div>
                </form>
            </div>
        </div>


    </div>
    <div class="col-md-2">&nbsp;</div>
</div>
@endsection
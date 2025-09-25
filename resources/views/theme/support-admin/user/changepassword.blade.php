@extends('theme.support-admin.layouts.main')
{{app()->setLocale(session()->get("locale"))}}
<?php $heading_title = trans('auth.change password'); ?>
@section('title', $heading_title)
@section('content')
{{app()->setLocale(session()->get("locale"))}}
    
        <section class="content-header">
            <h1>
                @lang('auth.change password')
            </h1>
            <ol class="breadcrumb">
                <li><a href="{{url('support-admin/home')}}"><i class="fa fa-dashboard"></i>@lang('auth.home')</a></li>
                <li class="active">@lang('auth.change password')</li>
            </ol>
        </section>
        @if(Session::has('flash_message_success'))
            <div class="col-md-12" style="margin-top: 10px;">
                <div class="alert alert-success alert-block">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>{!! session('flash_message_success') !!}</strong>
                </div>
            </div>
        @endif
        <section class="content">
            <div class="row">
                <div class="col-md-12">
                    <div class="box box-primary">
                        <div class="box-header with-border">
                            <h3 class="box-title">@lang('auth.change password')</h3>
                        </div>
                        <form method="post" enctype="multipart/form-data" action="{{ route('support-admin.changepassword.store') }}">
                        {{ csrf_field() }}
                            <div class="box-body">
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="control-label">@lang('auth.old password')</label>
                                        <input type="password" class="form-control" id="old_password" name="old_password" placeholder="@lang('auth.Enter Old Password')">
                                        @error('old_password')
                                            <span class="error">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="control-label">@lang('auth.new password')</label>
                                        <input type="password" class="form-control" name="new_password" id="new_password" placeholder="@lang('auth.Enter New Password')">
                                        @error('new_password')
                                            <span class="error">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name" class="control-label">@lang('auth.confirm password')</label>
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="@lang('auth.Enter Confirm Password')">
                                        @error('confirm_password')
                                            <span class="error">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="box-footer">
                                <button type="submit" class="btn btn-primary pull-right">@lang('auth.save')</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
   
@endsection

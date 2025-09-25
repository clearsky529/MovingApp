@extends('theme.support-admin.layouts.main')
@section('title', 'Cms')
@section('page-style')
<style>
/* custom model start */
.stop-scrolling .sweet-alert h2 {
  font-family: 'Source Sans Pro',sans-serif;
  font-weight: 500;
  font-size: 18px;
  margin: 10px 0 !important;
  line-height: 28px;
}

.stop-scrolling .modal {
    background: rgba(0,0,0,0.3);
}

.stop-scrolling .sweet-alert .confirm {
    margin-top:10px;
    padding: 8px 11px;
    background-color:#fff !important;
}

.stop-scrolling .sweet-alert .confirm:hover {
  background-color:#00a65a !important;
}

.stop-scrolling {
    background-color: transparent;
    border: 1px solid #00a65a;
    color: #000;
    font-size: 14px;
  }

/* custom model end */

</style>
@stop
@section('content')
<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Edit Agent
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.agents') }}">Manage Cms</a></li>
        <li class="active">Edit Cms</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Edit Cms</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
                  <!-- form start -->
              <form role="form" action="{{ route('support-admin.cms.update',$cms->id) }}" method="post">
              {{ csrf_field() }}
              <div class="box-body">
              <input type="hidden" name="slug" id="slug-text" value="">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="title">Title*</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" value="{{ $cms->title }}">
                    @if ($errors->has('title'))
                        <span class="text-danger">{{ $errors->first('title') }}</span>
                    @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="status">Status*</label>
                    <select name="status" class="form-control">
                           <option value="">Select Status</option>
                           <option <?php if(old('status') == "1") echo "selected"; ?>  value="1">Active</option>
                           <option <?php if(old('status') == "0") echo "selected"; ?> value="0">Inactive</option>
                        </select>
                        @if ($errors->has('status'))
                           <span class="text-danger">{{ $errors->first('status') }}</span>
                        @endif
                  </div>
                </div>
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="title">Field Status*</label>
                    <input type="text" class="form-control" name="field_status" id="field_status" placeholder="Enter Field Status" value="{{ $cms->field_status }}">
                    @if ($errors->has('field_status'))
                        <span class="text-danger">{{ $errors->first('field_status') }}</span>
                    @endif
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="confirm-password">Description*</label>
                    <textarea id="description" name="description" class="ckeditor form-control">{{$cms->description}}</textarea>
                     @if ($errors->has('description'))
                           <span class="text-danger">{{ $errors->first('description') }}</span>
                     @endif
                  </div>
                </div>
              </div>
              <div class="box-body">
                <div class="box-footer">
                  <button type="submit" class="btn btn-primary">Submit</button>
                </div>
              </div>
            </form>
          </div>
            
        </div>
      </div>
      
    </section>
    <!-- /.content -->

    @endsection
@section('page-script')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>
<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
       $('.ckeditor').ckeditor();
    });
</script>
<script>
    CKEDITOR.config.allowedContent = true;
    CKEDITOR.replace( 'description' );

     $('#title').on('keyup',function(){
      var slug = function(str) {
        var $slug = '';
        var trimmed = $.trim(str);
        $slug = trimmed.replace(/[^a-z0-9-]/gi, '').
        replace(/-+/g, '-').
        replace(/^-|-$/g, '');
        return $slug.toLowerCase();
      };
      $('#slug-text').val(slug($('#title').val()));
    })
    
</script>



  
@stop
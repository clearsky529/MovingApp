@extends('theme.support-admin.layouts.main')
@section('title', 'CMS')
@section('page-style')
@stop
@section('content')

<!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
      Add Cms
        <!-- <small>Control panel</small> -->
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{route('company-admin.home')}}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ route('company-admin.user') }}">Manage Cms</a></li>
        <li class="active">Add Cms</li>
      </ol>
    </section>

    <!-- Main content -->
     <section class="content">
      <div class="row">
        <div class="col-lg-12">
          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title">Add Cms</h3>
            </div>
            <!-- /.box-header -->
            <!-- form start -->
            <form role="form" action="{{ route('support-admin.cms.store') }}" method="post">
              {{ csrf_field() }}
              <div class="box-body">
              <input type="hidden" name="slug" id="slug-text" value="">
                <div class="col-lg-6">
                  <div class="form-group">
                    <label for="title">Title*</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" value="{{ old('title') }}">
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
                    <input type="text" class="form-control" name="field_status" id="field_status" placeholder="Enter Field Status" value="{{ old('field_status') }}">
                    @if ($errors->has('field_status'))
                        <span class="text-danger">{{ $errors->first('field_status') }}</span>
                    @endif
                  </div>
                </div>
                <div class="clearfix"></div>
                <div class="col-lg-12">
                  <div class="form-group">
                    <label for="confirm-password">Description*</label>
                    <textarea id="description" name="description" class="ckeditor form-control"></textarea>
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
<!-- select2 -->

<script src="//cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function() {
       $('.ckeditor').ckeditor();
    });
</script>
<script>
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
</script>
  
@stop
@extends(backpack_view('blank'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.add') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp

@section('after_styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('packages/select2/css/select2.min.css') }}">
@endsection

@section('header')
    <section class="container-fluid">
      <h2>
        <span class="text-capitalize">{!! $crud->getHeading() ?? $crud->entity_name_plural !!}</span>
        <small>{!! $crud->getSubheading() ?? trans('backpack::crud.add').' '.$crud->entity_name !!}.</small>

        @if ($crud->hasAccess('list'))
          <small><a href="{{ url($crud->route) }}" class="d-print-none font-sm"><i class="la la-angle-double-{{ config('backpack.base.html_direction') == 'rtl' ? 'right' : 'left' }}"></i> {{ trans('backpack::crud.back_to_all') }} <span>{{ $crud->entity_name_plural }}</span></a></small>
        @endif
      </h2>
    </section>
@endsection

@section('content')

<div class="row">
    <div class="{{ $crud->getCreateContentClass() }}">
        <!-- Default box -->

        @include('crud::inc.grouped_errors')

        <form method="post" action="{{ url($crud->route) }}"
            @if ($crud->hasUploadFields('create'))
            enctype="multipart/form-data"
            @endif
            >
            {!! csrf_field() !!}
            <!-- load the view from the application if it exists, otherwise load the one in the package -->
            <input type="hidden" name="_http_referrer" value="http://aso.test/admin/related-keyword/create">
            <div class="card">
                <div class="card-body">
                    <label>Keyword</label>
                    <select class="form-control select2 ajax-select2-keyword-id" name="keyword_id">
                        <option value="">--</option>
                    </select>
                    <div class="row">
                        <div class="col-md-6 col-sm-6 col-12 p-3">
                            <div class="card">
                                <div class="card-header text-center bg-primary">
                                    <label class="m-0">Keywords</label>
                                </div>
                                <div class="card-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control keyword keyword-addon" id="keyword-addon" placeholder="Input Keyword" aria-describedby="keyword-addon">
                                        <span class="input-group-text" id="keyword-addon">
                                            <i class="nav-icon la la-search"></i>
                                        </span>
                                    </div>
                                    <ul class="list-group keywords mt-2" style="max-height: 300px;overflow-y: auto;">
                                        
                                    </ul>
                                </div>
                                
                            </div>
                            
                        </div>
                        <div class="col-md-6 col-sm-6 col-12 p-3">
                            <div class="card">
                                <div class="card-header text-center bg-primary">
                                    <label class="m-0">Related Keywords</label>
                                </div>
                                <div class="card-body">
                                    <div class="input-group">
                                        <input type="text" class="form-control keyword related-keyword-addon" id="related-keyword-addon" placeholder="Input Related Keyword" aria-describedby="related-keyword-addon" onkeyup="myFunction()">
                                        <span class="input-group-text" id="related-keyword-addon">
                                            <i class="nav-icon la la-search"></i>
                                        </span>
                                    </div>
                                    <ul class="list-group related-keywords mt-2" id="related-keywords">
                                        
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- This makes sure that all field assets are loaded. -->
            <div class="d-none" id="parentLoadedAssets">{{ json_encode(Assets::loaded()) }}</div>
            @include('crud::inc.form_save_buttons')
        </form>
    </div>
</div>

@endsection
@section('after_scripts')
    <script type="text/javascript" src="{{ asset('packages/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('js/related_keyword/create.js') }}"></script>
@endsection


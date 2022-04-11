@extends(backpack_view('blank'))

@section('content')
    <div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-info"></i> Please take time to read steps on importing job's data...</h4>
        <ol>
            <li>file type must be <b>.csv</b>, <b>.xlxx</b> or <b>xls</b> only.</li>
            <li>Follow excel table heading name (<a class="text-bold text-white" href="{{ asset('keywords.xlsx') }}" onclick="alert('Download this example file?')">
                Download Example to view</a>) to avoid data descrepancy
            </li>
            <li>Once The ASO detected that the "Keword" is existing on the database, it will automatically merge the record between ASO database and csv file
            <li>Make sure that the Keyword is unique</li>
            <li>Click <b>Choose file</b></li>
            <li>Click <b>Import</b> button to submit</li>
            <li>Wait until the loading is finish</li>
        </ol>
        <p class="text-red"><span class="fa fa-info-circle"></span> Keyword importing only allow's 1000 line per file... so if it is greater than 1000 line you need to slice it. </p>
    </div>
    <form class="form-horizontal" method="POST" action="{{ url('admin/import/keyword') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="form-group @if($errors->has('keywords')) has-error @endif">
            <div class="col-md-12">
                <label>File input</label>
                <input type="file" name="keywords" class="@error('file') is-invalid @enderror @error('extension') is-invalid @enderror" required>
                @if ($errors->has('file'))
                    <span class="text-danger" role="alert">
                        <strong class="">{{ $errors->first('file') }}</strong>
                    </span>
                @endif
                @if ($errors->has('extension'))
                    <span class="text-danger" role="alert">
                        <strong class="">{{ $errors->first('extension') }}</strong>
                    </span>
                @endif
            </div>
        </div>
        <button type="submit" class="btn btn-primary btn-sm">
            <span class="fa fa-file-excel-o"></span>  
            Import
        </button>
    </form>
@endsection
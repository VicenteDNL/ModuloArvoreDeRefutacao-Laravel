@extends('base')

@section('content')



<div class="container-fluid">
    <div class="row">
        <div class="col-8">
            <div class="container-fluid">
                <div class="card" style="margin-top: 20px; baxc; background-color: #F5FFFA;">
                    <div class="card-body">
                        @yield('upload')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-4">

            <div class="container-fluid">
                    <div class="card" style="margin-top: 20px;">
                        <div class="card-body">
                            <p>Informação da formula</p>
                        </div>
                    </div>
                </div>

            <div class="container-fluid">
                <div class="card" style="margin-top: 20px;">
                    <div class="card-body">
                        <form method="post" action="{{URL::to('/submit')}}" enctype="multipart/form-data">
                            <div class="form-group">
                                <label >Arquivo XML</label>
                                <input type="file" name = "arquivo"  accept=".xml" class="form-control-file">
                            </div>
                                <input type="hidden" value={{csrf_token()}}>
                            <div class="form-group">
                                <button type="submit" class="btn btn-info">Gerar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>


@endsection

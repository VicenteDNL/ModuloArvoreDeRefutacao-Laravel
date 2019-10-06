@extends('base')

@section('content')

<div class="container-fluid">
        <div class="row">
            <div class="col-8">
                <div class="container-fluid">
                    <div class="card" style="margin-top: 20px; baxc; background-color: #F5FFFA;">
                        <div class="card-body">
                            @yield('col-8')
                        </div>
                    </div>
                </div>


                <div class="container-fluid">
                        @yield('alternativas')
                </div>


            </div>
            <div class="col-4">

                <div class="container-fluid">
                        <div class="card" style="margin-top: 20px;">
                            <div class="card-body">
                                <p>{{$formulaGerada++}}</p>
                            </div>
                        </div>
                    </div>

                <div class="container-fluid">
                    <div class="card" style="margin-top: 20px;">
                        <div class="card-body">
                            <form method="post" action="{{URL::to('/porEtapa/inicializa')}}" enctype="multipart/form-data">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label >Arquivo XML</label>
                                    <input type="file" name = "arquivo"  accept=".xml" class="form-control-file">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-info">Gerar</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="container-fluid">
                    <div class="card" style="margin-top: 20px;">
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    @foreach($listaFormulas as $formula)
                                    <tr>
                                    <th scope="row">{{$formula['xml']}}</th>
                                    <td>{{$formula['str']}}</td>
                                    <td>
                                        <form method="post" action="{{URL::to('/porEtapa/Inicializado')}}">
                                            {{ csrf_field() }}
                                            <input type="hidden" name='idFormula' value={{$formula['xml']}}>
                                            <button type="submit" class="btn btn-outline-info">Inicializar</button>
                                        </form>
                                    </tr>
                                    @endforeach
                                </tbody>
                                </table>
                        </div>
                    </div>
                </div>




            </div>
        </div>
    </div>

@endsection

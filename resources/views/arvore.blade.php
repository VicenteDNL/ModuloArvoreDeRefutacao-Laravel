@extends('base')

@section('content')
<!-- Texto -->

<div class="container p-0 mt-3 mb-3">
    <div class="row">
        <div class="col-8">
            <div class="card shadow-sm bg-white rounded-15">
                <div class="card-header bg-gradient-blue text-white rounded-top-15 d-flex justify-content-center negrito m-0">
                    Árvore de refutação
                </div>
                <div class="card-body d-flex justify-content-center">
                    @yield('upload' ,' Escolha uma formula')
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow-sm bg-white rounded-15">
                <div class="card-header bg-gradient-blue text-white rounded-top-15 d-flex justify-content-center negrito m-0">
                    Fórmula
                </div>
                <div class="card-body">
                    <div class="col d-flex justify-content-center">
                        <div class="row badge-custom bg-pink d-flex align-items-center justify-content-center rounded-05rem">
                            <span class="text-pink">{{$formulaGerada}}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm bg-white rounded-15 mt-4">
                <div class="card-header bg-gradient-blue text-white rounded-top-15 d-flex justify-content-center negrito m-0">
                        Arquivo XML
                </div>
                <div class="card-body">
                    <form method="post" action="{{URL::to('/submit')}}" enctype="multipart/form-data">
                        <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" name = "arquivo"  accept=".xml" required>
                            <label class="custom-file-label" for="arquivo">Escolha o arquivo</label>
                        </div>
                        <input type="hidden" value={{csrf_token()}}>
                        <div class="col d-flex justify-content-center mt-2">
                            <div class="row">
                                <button type="submit" class="btn shadow  {{--bg-gradient-blue--}}bg-gradient-green rounded-05rem">
                                    <span class="text-white ml-2"><i class="fas fa-cloud-upload-alt text-18"></i></span>
                                    <span class="text-white ml-2 mr-2">Enviar</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card shadow-sm bg-white rounded-15 mt-4">
                <div class="card-header bg-gradient-blue text-white rounded-top-15 d-flex justify-content-center negrito m-0">
                    Fórmulas
                </div>
                <div class="p-2">
                    <div class="overflow-auto" style="height: 300px">
                    <table class="table table-bordered">
                        <tbody>
                            @foreach($listaFormulas as $formula)
                            <tr>
                                <th class="text-center align-middle m-0" scope="row">{{$formula['xml']}}</th>
                                <td class="text-center align-middle" width="60%">{{$formula['str']}}</td>
                                <td class="text-center align-middle m-0 p-0">
                                    <form method="post" action="{{URL::to('/Arvore')}}">
                                        {{ csrf_field() }}
                                        <input type="hidden" name='idFormula' value={{$formula['xml']}}>
                                        <button type="submit" class="btn btn-sm shadow bg-gradient-green rounded-05rem">
                                            <span class="text-white">Gerar</span>
                                            <span class="text-white ml-2"><i class="fas fa-arrow-right text-18"></i></span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                        </div>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer fixed-bottom mt-4 mb-4">
    <div class="container-fluid">
        <div class="col d-flex justify-content-center">
            <div class="row">
                <span data-toggle="tooltip" data-placement="top" title="Design by 💁🏻‍♂️ Jheymerson">Feito com ❤️ pelo Danilo Saraiva</span>
            </div>
        </div>
    </div>
</div>
@endsection

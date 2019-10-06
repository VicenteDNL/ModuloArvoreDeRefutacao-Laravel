@extends('base')

@section('content')
<!-- Texto -->
<div class="container-fluid mt-3 mb-3">
    <div class="col-8 d-flex justify-content-center">
        <div class="row">
            <span>Árvore de refutação</span>
        </div>
    </div>
</div>
<div class="container p-0">
    <div class="row">
        <div class="col-8">
            <div class="card shadow-sm bg-white rounded-15">
                <div class="card-body d-flex justify-content-center">
                    @yield('upload')
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card shadow-sm bg-white rounded-15">
                <div class="card-body">
                    <div class="col d-flex justify-content-center">
                        <div class="row">
                            <p>Fórmula</p>
                        </div>
                    </div>
                    <div class="col d-flex justify-content-center">
                        <div class="row">
                            <p>{{$formulaGerada}}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card shadow-sm bg-white rounded-15 mt-4">
                <div class="card-body">
                    <div class="col d-flex justify-content-center">
                        <div class="row">
                            <p>Arquivo XML</p>
                        </div>
                    </div>
                    <form method="post" action="{{URL::to('/submit')}}" enctype="multipart/form-data">
                        <div class="custom-file mb-2">
                            <input type="file" class="custom-file-input" name = "arquivo"  accept=".xml">
                            <label class="custom-file-label" for="arquivo">Escolha o arquivo</label>
                        </div>
                        <input type="hidden" value={{csrf_token()}}>
                        <div class="col d-flex justify-content-center mt-2">
                            <div class="row">
                                <button type="submit" class="btn shadow bg-gradient-blue rounded-05rem">
                                    <span class="text-white ml-2"><i class="fas fa-cloud-upload-alt text-18"></i></span>
                                    <span class="text-white ml-2 mr-2">Enviar</span>
                                </button>
                            </div>
                        </div>
                    </form>
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
                                    <form method="post" action="{{URL::to('/Arvore')}}">
                                        {{ csrf_field() }}
                                      <input type="hidden" name='idFormula' value={{$formula['xml']}}>
                                      <button type="submit" class="btn btn-outline-info">Gerar</button>
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
<div class="footer mt-4 mb-4">
    <div class="container-fluid">
        <div class="col d-flex justify-content-center">
            <div class="row">
                <span data-toggle="tooltip" data-placement="top" title="Design by 💁🏻‍♂️ Jheymerson">Feito com ❤️ pelo Danilo Saraiva</span>
            </div>
        </div>
    </div>
</div>
@endsection

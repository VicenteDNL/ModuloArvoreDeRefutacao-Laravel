@extends('base')

@section('content')
<form method="post" action="{{URL::to('/submit')}}" enctype="multipart/form-data">
  <input  name = "arquivo" type="file" accept=".xml">
  <input type="hidden" value={{csrf_token()}}>
  <br> <br>
  <button type="submit">Enviar</button>
</form>


@endsection
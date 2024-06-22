@extends('layouts.app')



@section('content')

<div class="grid grid-cols-12 gap-6">


    <div class="col-span-1 col"></div>
    <div class="col-span-10 px-4 px-6 bg-white col rounded-xl">
        {{-- <h2></h2> --}}
        {{-- titulos pequeño en tailwind con H--}}
        <h4>traducir en el arcivo de lang</h4>
        <p>admin</p>
        @foreach ($admins as $admin)
            <p>'{{ $admin->label }}'=>'{{ $admin->label }}',</p>
        @endforeach
        <hr>
        <p>field</p>
        @foreach ($fields as $field)
            <p>'{{ $field->trans_name }}'=>'{{ $field->trans_name }}',</p>
        @endforeach
        <hr>
        <p>node</p>
        @foreach ($singulars as $singular)
            <p>'{{ $singular->singular }}'=>'{{ $singular->singular }}',</p>
        @endforeach
        <hr>
        <p>nodes</p>
        @foreach ($plurals as $plural)
            <p>'{{ $plural->plural }}'=>'{{ $plural->plural }}',</p>
        @endforeach
    </div>
</div>


@endsection

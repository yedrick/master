@extends('master::layouts.app')

@section('content')

<div class="grid grid-cols-12 gap-6">


    <div class="col-span-1 col"></div>
    <div class="col-span-10 px-4 px-6 bg-white col rounded-xl">

        @if( session('success'))
        <div class="p-4 mt-4 text-green-800 bg-green-100 border-l-4 border-green-500" role="alert">
            {{-- <p class="font-bold">Be Warned</p> --}}
            <p  > {{ session('success') }}</p>
          </div>
        {{-- <div class="alert alert-success">
            {{ session('success') }}
        </div> --}}
        @elseif (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
        @endif
        <div class="flex-none mt-6 ">
            <h1 class="mb-10 text-2xl font-bold text-center uppercase text-black-400">
                {{ isset($model) ? 'Editar' : 'Crear' }} {{ $node->singular ? __($node->singular) : $node->name}}</h1>

            <a href="{{ url('model-list/'.$node->name) }}"
                class="px-4 py-2 ml-6 font-bold text-white bg-blue-500 rounded hover:bg-blue-700">  {{ $node->plural ? __($node->plural) : $node->name}}</a>
        </div>

        <div class="flex flex-col mt-4">
            <div class="mx-16 mt-8 overflow-auto lg:overflow-visible sm:mt-2">
                <div class="container ">

                    @if ($node->type=='child')
                        <form class="w-auto mx-4 ml-4 " action="{{ isset($model) ? route('model.update', ['nodeName' => $node->name, 'id' => $model->parent_id]) : route('model.store', $node->name) }}" method="POST" enctype="multipart/form-data">
                    @else
                        <form action="{{ isset($model) ? route('model.update', ['nodeName' => $node->name, 'id' => $model->id]) : route('model.store', $node->name) }}" method="POST" enctype="multipart/form-data">
                    @endif
                    @csrf
                    @if (isset($model))
                    @method('PUT')
                    @endif
                    @foreach ($fields as $field)
                            <div class="mb-5">
                                <label for="{{ $field->name }}" class="block mb-2 text-xs font-bold tracking-wide text-gray-700 uppercase">{{ $field->label ? __($field->label) : $field->name }}</label>
                                @if (in_array($field->type, ['select']))
                                    @if ($field->relation == 1)
                                        <select name="{{ $field->name }}" id="{{ $field->name }} " class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            <option value="">Selecione ...</option>
                                            @foreach (app($field->relation_cond)->all() as $relatedModel)
                                            <option value="{{ $relatedModel->id }}"
                                                {{ (isset($model) && $model->{$field->name} == $relatedModel->id) ? 'selected' : '' }}>
                                                {{$relatedModel->name }}</option>
                                            @endforeach
                                        </select>
                                    @else
                                        <select name="{{ $field->name }}" id="{{ $field->name }}" class= "bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                            @foreach ($options->where('parent_id', $field->id) as $option)
                                            <option value="{{ $option->name }}"
                                                {{ (isset($model) && $model->{$field->name} == $option->name) ? 'selected' : '' }}>
                                                {{ __($option->label) }}</option>
                                            @endforeach
                                        </select>
                                    @endif
                                @elseif ($field->type=='image')
                                    <input type="file" name="{{ $field->name }}" id="{{ $field->name }}" accept="image/*" class="block w-full px-4 py-3 leading-tight text-gray-700 border border-gray-200 rounded appearance-none bg-gray-50 focus:outline-none focus:bg-white focus:border-gray-500">
                                @elseif ($field->type=='file')
                                    <input type="file" name="{{ $field->name }}" id="{{ $field->name }}"
                                        accept=".pdf, .doc, .docx, .xls, .xlsx" class="block w-full px-4 py-3 leading-tight text-gray-700 border border-gray-200 rounded appearance-none bg-gray-50 focus:outline-none focus:bg-white focus:border-gray-500">
                                @else
                                    <input type="{{ $field->type }}" name="{{ $field->name }}" placeholder="{{ $field->placeholder}}" class="w-full p-3 border border-gray-300 rounded shadow mb-" id="{{ $field->name }}"
                                        value="{{ old($field->name, isset($model) ? $model->{$field->name} : '') }}" />
                                @endif

                                @error($field->name)
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror


                            </div>


                            {{-- <div class="mb-5">
                                <label for="twitter" class="block mb-2 font-bold text-gray-600">Twitter</label>
                                <input type="text" id="twitter" name="twitter" placeholder="Put in your fullname."
                                    class="w-full p-3 border border-red-300 rounded shadow mb-">
                                <p class="mt-2 text-sm text-red-400">Twitter username is required</p>
                            </div>

                            <button
                                class="block w-full p-4 mb-6 font-bold text-white bg-blue-500 rounded-lg">Submit</button> --}}
                                @endforeach
                                <button type="submit" class="px-4 py-2 my-4 ml-6 text-sm font-bold text-white bg-blue-500 rounded hover:bg-blue-700">
                                    {{ isset($model) ? 'Actualizar' : 'Crear' }}</button>

                            </form>

                </div>
            </div>
        </div>
    </div>
</div>



{{-- <h1>{{ isset($model) ? 'Editar' : 'Crear' }} {{ $node->name }}</h1>
<a href="{{ url('model-list/'.$node->name) }}">Listado</a>
@if ($node->type=='child')
<form
    action="{{ isset($model) ? route('model.update', ['nodeName' => $node->name, 'id' => $model->parent_id]) : route('model.store', $node->name) }}"
    method="POST" enctype="multipart/form-data">
    @else
    <form
        action="{{ isset($model) ? route('model.update', ['nodeName' => $node->name, 'id' => $model->id]) : route('model.store', $node->name) }}"
        method="POST" enctype="multipart/form-data">
        @endif

        @csrf
        @if (isset($model))
        @method('PUT')
        @endif
        @foreach ($fields as $field)
        <div>
            <label for="{{ $field->name }}">{{ $field->name }}</label>
            @if (in_array($field->type, ['select']))
            @if ($field->relation == 1)
            <select name="{{ $field->name }}" id="{{ $field->name }}">
                @foreach (app($field->relation_cond)->all() as $relatedModel)
                <option value="{{ $relatedModel->id }}"
                    {{ (isset($model) && $model->{$field->name} == $relatedModel->id) ? 'selected' : '' }}>
                    {{ $relatedModel->name }}</option>
                @endforeach
            </select>
            @else
            <select name="{{ $field->name }}" id="{{ $field->name }}">
                @foreach ($options->where('parent_id', $field->id) as $option)
                <option value="{{ $option->name }}"
                    {{ (isset($model) && $model->{$field->name} == $option->name) ? 'selected' : '' }}>
                    {{ $option->label }}</option>
                @endforeach
            </select>
            @endif
            @elseif ($field->type=='image')
            <input type="file" name="{{ $field->name }}" id="{{ $field->name }}" accept="image/*">
            @elseif ($field->type=='file')
            <input type="file" name="{{ $field->name }}" id="{{ $field->name }}"
                accept=".pdf, .doc, .docx, .xls, .xlsx">
            @else
            <input type="{{ $field->type }}" name="{{ $field->name }}" id="{{ $field->name }}"
                value="{{ old($field->name, isset($model) ? $model->{$field->name} : '') }}" />
            @endif
            @error($field->name)
            <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>
        @endforeach
        <button type="submit">{{ isset($model) ? 'Actualizar' : 'Crear' }}</button>
    </form> --}}
    @endsection

@extends('master::layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/table.css') }}">
@endsection

@section('content')
<div class="grid grid-cols-12 gap-6">
    <div class="col-span-1 col"></div>
    <div class="col-span-10 px-6 bg-white col rounded-xl">
        <h1 class="m-2 text-3xl text-center"> {{ $node->plural ? __($node->plural) : $node->name }}</h1>
        <div class="flex flex-row items-center justify-between lg:flex-row">
            <a href="{{ url('model/'.$node->name) }}"
                class="px-4 py-2 mx-5 my-5 text-sm font-bold text-white bg-blue-500 rounded lg:mr-2 lg:mb-0 hover:bg-blue-700 lg:inline-block">Crear
                {{ $node->singular ? __($node->singular) : $node->name }}</a>
            <a href="{{ url('export-node/'.$node->name) }}"
                class="px-4 py-2 mx-5 my-5 text-sm font-bold text-white bg-blue-500 rounded lg:ml-2 lg:mb-0 hover:bg-blue-700 lg:inline-block">Exportar
                {{ $node->plural ? __($node->plural) : $node->name }}</a>
        </div>
        <div class="flex flex-col items-center justify-between mt-4 lg:flex-row">
            {{-- filtros  --}}
            <form action="{{ url('model-list/'.$node->name) }}" method="GET">
                <h2 class="text-2xl">Filtros</h2>
                <div class="flex flex-col items-center justify-between lg:flex-row">
                    <div class="flex flex-col items-center justify-between lg:flex-row">
                        <label for="search" class="block m-2 text-sm font-medium text-gray-700">Buscar</label>
                        <input type="text" name="search" id="search"
                            class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring">
                    </div>
                    <div class="flex flex-col items-center justify-between ml-4 lg:flex-row">
                        <label for="filter" class="block m-2 text-sm font-medium text-gray-700">En</label>
                        <select name="field_name" id="field_name"
                            class="block w-full px-4 py-2 mt-2 text-gray-700 bg-white border border-gray-300 rounded-md focus:border-blue-500 focus:outline-none focus:ring" required>
                            <option value="">Seleccionar</option>
                            @foreach ($fields as $field)
                            <option value="{{ $field->name }}">{{ $field->label ? __($field->label): $field->trans_name}}
                            </option>
                            @endforeach
                        </select>
                        <div class="flex flex-row items-center justify-between lg:flex-row">
                            <button type="submit" class="px-4 py-2 my-5 text-sm font-bold text-white bg-blue-500 rounded lg:ml-2 lg:mb-0 hover:bg-blue-700 lg:inline-block mx-7">Filtrar</button>
                            <a type="submit" class="px-4 py-2 my-5 text-sm font-bold text-white bg-blue-500 rounded lg:ml-2 lg:mb-0 hover:bg-blue-700 lg:inline-block mx-7" href="{{route('model.list', ['nodeName'=> $node->name ])}}">Limpiar</a>
                        </div>
                    </div>
                </div>
            </form>
                    <div >
                        <span class="rounded-md shadow-sm">
                            <button id="dropdown" type="button" class="inline-flex justify-center w-full px-4 py-2 mt-2 text-sm font-medium text-gray-700 transition duration-150 ease-in-out bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-800 active:text-white">
                                Mostrar
                                <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </span>
                    </div>
                    <div id="dropdown-menu" class="absolute right-0 hidden w-56 mt-2 origin-top-right bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5  overflow-y-auto" style="max-height: 148px;">
                        <div id="sortable-list" class="py-1 space-y-2" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                            @foreach ($fields_models as $field)
                                <label class="flex items-center p-2 space-x-3 transition-colors duration-150 ease-in-out bg-white border border-gray-300 rounded-md cursor-pointer draggable hover:bg-gray-50" data-id="{{ $field->id }}">
                                    <input type="checkbox" value="{{ $field->id }}" name="{{ $field->name }}" id="" class="mr-2" {{ $field->display_list=='show'?'checked':null }} > {{ $field->label ? __($field->label): $field->trans_name}}
                                    {{-- <input type="checkbox" id="option1" class="w-5 h-5 text-blue-600 form-checkbox" value="Option 1"> Option 1 --}}
                                </label>

                            @endforeach
                            <div class="flex justify-center">
                                <button id="apply-btn" class="w-36 p-2 mt-2 text-white  bg-blue-500 rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Aplicar</button>
                            </div>
                        </div>
                    </div>

        </div>


        <div class="flex flex-col mt-4">
            <div class="mt-8 overflow-auto lg:overflow-visible sm:mt-2 ">
                <div class="container">
                    <div class="inline-block min-w-full m-4 overflow-hidden rounded-lg shadow-md">

                        <table id="table" class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th
                                        class="px-5 py-3 text-xs font-semibold tracking-wider text-center text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                                        #</th>
                                    @foreach ($fields as $field)
                                    <th
                                        class="px-5 py-3 text-xs font-semibold tracking-wider text-center text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                                        {{ $field->label ?__($field->label)  : $field->trans_name}}</th>
                                    @endforeach
                                    <th
                                        class="px-5 py-3 text-xs font-semibold tracking-wider text-center text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                                        Editar</th>
                                    <th
                                        class="px-5 py-3 text-xs font-semibold tracking-wider text-center text-gray-600 uppercase bg-gray-100 border-b-2 border-gray-200">
                                        Eliminar</th>
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach ($data as $key=>$item)
                                <tr>
                                    <td class="px-5 py-5 text-sm text-center bg-white border-b border-gray-200">
                                        {{ $key+1 }}</td>
                                    @foreach ($fields as $field)
                                    @if ($field->relation === 1)
                                    <td class="px-5 py-5 text-sm text-center bg-white border-b border-gray-200">
                                        {{ $item->{$field->value}->name??$item->{$field->name} }}</td>
                                    @else
                                    <td class="px-5 py-5 text-sm text-center bg-white border-b border-gray-200">
                                        {{ $item->{$field->name} }}</td>
                                    @endif
                                    @endforeach
                                    @if ($node->type=='child')
                                    <td class="px-5 py-5 text-sm text-center bg-white border-b border-gray-200">
                                        <a href="{{ url('model/'.$node->name.'/'.$item->parent_id) }}"><svg
                                                    class="w-6 h-6 ml-8 text-green-600 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z" />
                                                </svg>
                                            </a>
                                    </td>
                                    <td class="px-5 py-5 text-sm text-center bg-white border-b border-gray-200">
                                        <a href="{{ url('model/delete/'.$node->name.'/'.$item->parent_id) }}"> <svg
                                                class="w-6 h-6 ml-8 text-red-600 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M8.6 2.6A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4c0-.5.2-1 .6-1.4ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                    clip-rule="evenodd" />
                                            </svg></a>
                                    </td>
                                    @else
                                    <td class="px-5 py-5 text-sm text-center bg-white border-b border-gray-200">
                                        <a href="{{ url('model/'.$node->name.'/'.$item->id) }}"><svg
                                                    class="w-6 h-6 ml-8 text-green-600 dark:text-white" aria-hidden="true"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <path stroke="currentColor" stroke-linecap="round"
                                                        stroke-linejoin="round" stroke-width="2"
                                                        d="m14.3 4.8 2.9 2.9M7 7H4a1 1 0 0 0-1 1v10c0 .6.4 1 1 1h11c.6 0 1-.4 1-1v-4.5m2.4-10a2 2 0 0 1 0 3l-6.8 6.8L8 14l.7-3.6 6.9-6.8a2 2 0 0 1 2.8 0Z" />
                                                </svg>
                                            </a>
                                    </td>
                                    <td class="px-5 py-5 text-sm text-center bg-white border-b border-gray-200">
                                        <a href="{{ url('model/delete/'.$node->name.'/'.$item->id) }}"> <svg
                                                class="w-6 h-6 ml-8 text-red-600 dark:text-white" aria-hidden="true"
                                                xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                                viewBox="0 0 24 24">
                                                <path fill-rule="evenodd"
                                                    d="M8.6 2.6A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4c0-.5.2-1 .6-1.4ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                                    clip-rule="evenodd" />
                                            </svg></a>
                                    </td>
                                    @endif

                                </tr>
                                @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@section('script')
<script>

// Obtener todos los elementos de la lista
const draggableItems = document.querySelectorAll('.draggable');

// Variable para almacenar el elemento que está siendo arrastrado
let draggedItem = null;

// Iterar sobre cada elemento de la lista y agregar los listeners de arrastre
draggableItems.forEach(item => {
    item.draggable = true; // Habilitar el arrastre para los elementos
    item.addEventListener('dragstart', function() {
        draggedItem = this;
        this.classList.add('opacity-50'); // Reducir opacidad durante el arrastre
    });

    item.addEventListener('dragend', function() {
        draggedItem = null;
        this.classList.remove('opacity-50'); // Restaurar opacidad después del arrastre
    });

    item.addEventListener('dragover', function(e) {
        e.preventDefault();
    });

    item.addEventListener('dragenter', function() {
        this.classList.add('bg-gray-200'); // Resaltar el área de destino durante el arrastre
    });

    item.addEventListener('dragleave', function() {
        this.classList.remove('bg-gray-200'); // Restaurar el color de fondo después de salir del área de destino
    });

    item.addEventListener('drop', function() {
        this.classList.remove('bg-gray-200');
        if (draggedItem !== null && draggedItem !== this) {
            const items = Array.from(this.parentNode.children);
            const indexDragged = items.indexOf(draggedItem);
            const indexTarget = items.indexOf(this);
            if (indexDragged < indexTarget) {
                this.parentNode.insertBefore(draggedItem, this.nextSibling);
            } else {
                this.parentNode.insertBefore(draggedItem, this);
            }
        }
    });
});




    $(document).ready(function () {
    $("#dropdown").on("click", function (e) {
        e.stopPropagation();
        $("#dropdown-menu").toggle();
    });

    $(document).on("click", function (e) {
        if (!$("#dropdown").is(e.target) && !$("#dropdown-menu").has(e.target).length) {
            $("#dropdown-menu").hide();
        }
    });

    $("#apply-btn").on("click", function () {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        let node ={{ $node->id }};
        let selectedOptions = [];
        $(":checkbox:checked").each(function () {
            // selectedOptions.push(parseInt($(this).val()));
            selectedOptions.push($(this).val());
        });
        $.ajax({
            url: "{{url('ajax/change-table')}}", // Reemplaza con la URL correcta de tu servidor
            type: 'POST', // O el método que estés utilizando
            headers: {
                'X-CSRF-TOKEN': csrfToken // Incluye el token CSRF en los encabezados
            },
            data: { options: selectedOptions ,node : node }, // Pasa las opciones seleccionadas al servidor
            success: function (data) {
                console.log("Consulta exitosa", data);
                // Recarga la página después de la consulta exitosa
                location.reload();
            },
            error: function (error) {
                console.error("Error en la consulta", error);
            }
        });
        console.log("Seleccionaste las opciones: ", selectedOptions);
        // console.log("Realizar consulta y recargar página");
    });
});
</script>
@endsection

<?php

namespace yedrick\Master\App\Http\Controllers;

use yedrick\Master\App\Exports\NodeModelExport;
use yedrick\Master\App\Models\Field;
use yedrick\Master\App\Models\FieldOption;
use yedrick\Master\App\Models\Node ;
use Illuminate\Http\Request;
use Illuminate\Routing\UrlGenerator;

class MainController extends Controller{

    public function __construct(UrlGenerator $url){
		$this->prev = $url->previous();
	}


    public function modelList($nodeName){
        \Log::info('modelList');
        
        $node = Node::where('name', $nodeName)->first();
        if ($node) {
            if (class_exists($node->model)) {
                $model = app($node->model);// Resuelve la ruta del modelo
                if($model){
                    $fields_model = Field::where('parent_id', $node->id)->where('display_list', 'show')->orderBy('order', 'asc')->get();
                    $fields_models =Field::where('parent_id', $node->id)->orderBy('order', 'asc')->get();
                    // filtros
                    // $filters = Field::where('parent_id', $node->id)->where('display_filter', 'show')->orderBy('order', 'asc')->get();
                    // $filters = $fields_model->filter(function ($field) use ($request) {
                    //     return $request->input($field->name);
                    // });
                    $data = $model::query();
                    $request=request()->all();
                    if($request){
                        $search=request()->input('search');
                        $fiel_name=request()->input('field_name');
                        if($search){
                            $data = $data->where($fiel_name, 'like', '%'.$search.'%');
                        }
                    }
                    $data = $data->get();
                    // Obtén los atributos ocultos del modelo
                    $hiddenAttributes = $model->getHidden();

                    // Filtra los campos para excluir los atributos ocultos
                    $fields = $fields_model->filter(function ($field) use ($hiddenAttributes) {
                        return !in_array($field->name, $hiddenAttributes);
                    });


                    return view('master::model.list', compact('data', 'fields','model','node','fields_models'));
                }else{
                    abort(404); // Maneja el caso donde el modelo no se encuentra en la base de datos
                }
            }else{
                abort(404); // Maneja el caso donde el nodo no se encuentra en la base de datos
            }
        } else {
            abort(404); // Maneja el caso donde el nodo no se encuentra en la base de datos
        }
    }

    public function modelCreate($nodeName){
        $node = Node::where('name', $nodeName)->first();
        if ($node) {
            $fields = Field::where('parent_id', $node->id)
                ->where('display_item', 'show')->orderBy('order', 'asc')
                ->get();

            $options = FieldOption::whereIn('parent_id', $fields->pluck('id')->toArray())
                ->get();

            return view('master::model.create', compact('fields', 'options','node'));
        } else {
            abort(404); // Maneja el caso donde el nodo no se encuentra en la base de datos
        }
    }

    public function edit($nodeName, $id){
        $node = Node::where('name', $nodeName)->first();
        if ($node) {
            $fields = Field::where('parent_id', $node->id)
                ->where('display_item', 'show')
                ->get();

            $options = FieldOption::whereIn('parent_id', $fields->pluck('id')->toArray())->get();

            if($node->type=='child'){
                $model = app($node->model)->where('parent_id',$id)->first();
            }else{
                $model = app($node->model)->find($id);
            }
            if(!$model){
                abort(404);
            }
            // Obtén los atributos ocultos del modelo
            $hiddenAttributes = $model->getHidden();
            $fields = $fields->filter(function ($field) use ($hiddenAttributes) {
                return !in_array($field->name, $hiddenAttributes);
            });
            // $model->makeHidden(['password', 'remember_token', 'email_verified_at']);

            return view('master::model.create', compact('fields', 'options', 'model','node'));
        } else {
            abort(404); // Maneja el caso donde el nodo no se encuentra en la base de datos
        }
    }

    public function store(Request $request, $nodeName){
        $node = Node::where('name', $nodeName)->first();

        if ($node) {
            $fields = Field::where('parent_id', $node->id)
                ->where('display_item', 'show')
                ->get();

            $model = app($node->model);
            if($model){
                $rules = $model::$rules_create;
                $request->validate($rules);
                foreach ($fields as $field) {
                    $fieldName = $field->name;
                    if ($field->type === 'file') {
                        // Obtener el archivo de la solicitud
                        $file = $request->file($fieldName);

                        // Verificar si se proporcionó un archivo
                        if ($file) {
                            // Generar un nombre único para el archivo
                            $fileName = uniqid() . '_' . $file->getClientOriginalName();

                            // Almacenar el archivo en el sistema de archivos de Laravel (por defecto, en la carpeta storage/app)
                            $file->storeAs('public/images', $fileName);

                            // Guardar la ruta del archivo en el modelo
                            $model->{$fieldName} = 'images/' . $fileName;
                        }
                    }elseif ($field->type === 'select' && $field->relation === 1) {
                        $relatio_id=$request->input($fieldName);
                        $relatedModel = app($field->relation_cond);
                        $model->{$fieldName} = $relatedModel::find($relatio_id)->id;
                    } else {
                        $model->{$fieldName} = $request->input($fieldName);
                    }
                }

                $model->save();
                if($node->type!='child'){
                    return redirect('model/'.$node->name.'/'.$model->id)->with('success', 'Datos guardados exitosamente.');
                }
                return redirect('model/'.$node->name.'/'.$model->parent_id)->with('success', 'Datos guardados exitosamente.');
            }else{
                abort(404,'No Model'); // Maneja el caso donde el modelo no se encuentra en la base de datos
            }
        } else {
            abort(404); // Maneja el caso donde el nodo no se encuentra en la base de datos
        }
    }

    public function update(Request $request, $nodeName, $id){
        $node = Node::where('name', $nodeName)->first();

        if ($node) {
            $fields = Field::where('parent_id', $node->id)
                ->where('display_item', 'show')
                ->get();

            $model = app($node->model)->find($id);
            if($model){
                $request->merge(['id' => $id]);
                $rules = $model::$rules_edit;
                $request->validate($rules);
                foreach ($fields as $field) {
                    $fieldName = $field->name;

                    if ($field->type === 'select' && $field->relation === 1) {
                        // Handle the case where the field is related to another model
                        $relatedModel = app($field->relation_cond);
                        $model->{$fieldName} = $relatedModel->find($request->input($fieldName))->id;
                    } else {
                        $model->{$fieldName} = $request->input($fieldName);
                    }
                }
                $model->update();
                return redirect('model/'.$node->name.'/'.$model->id)->with('success', 'Datos Editados exitosamente.');
            }else{
                abort(404);
            }

        } else {
            abort(404); // Maneja el caso donde el nodo no se encuentra en la base de datos
        }
    }

    public function delete($nodeName, $id){
        $node = Node::where('name', $nodeName)->first();
        if ($node) {
            $model = app($node->model)->find($id);

            if ($model) {
                $model->delete();
                return redirect()->route('model.list', $node->name)
                    ->with('success', 'Registro eliminado exitosamente.');
            } else {
                return redirect()->route('model.list', $node->name)
                    ->with('error', 'El registro no se encontró.');
            }
        } else {
            abort(404); // Maneja el caso donde el nodo no se encuentra en la base de datos
        }
    }

    public function exportNode($nodeName){
        $node = Node::where('name', $nodeName)->first();
        if ($node) {
            $modelo = $node->modelo;
            return \Excel::download(new NodeModelExport($node), 'reporte-'.__($node->plural).'.xlsx');
        }else {
            abort(404); // Maneja el caso donde el nodo no se encuentra en la base de datos
        }
    }
    // ajax para change de tabla
    public function changeTable(Request $request){
        $optiones=$request->input('options');
        $node_input=$request->input('node');
        $node=Node::find($node_input);
        $fields=$node->fields;
        foreach ($fields as $field) {
            if(in_array($field->id,$optiones)){
                $field->display_list='show';
            }else{
                $field->display_list='none';
            }
            $field->save();
        }

        return response()->json(['message'=>'Se actualizaron las columnas correctamente']);
    }

    // controlador para traduciones
    public function traducer(){
        $singulars=\App\Models\Node::where('singular','like','%node%')->get();
        $plurals=\App\Models\Node::where('plural','like','%nodes%')->get();
        $fields=\App\Models\Field::where('trans_name','like','%field%')->get();
        $admins=\App\Models\FieldOption::where('label','like','%admin%')->get();
        // return view('model.traducer',['singulars'=>$singulars,$plurals=>'plurals','fields'=>$fields,'admins'=>$admins]);
        return view('model.traducer',compact('singulars','plurals','fields','admins'));
    }

}

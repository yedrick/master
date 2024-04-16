<?php

namespace Mastery\Master\App\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Mastery\Master\App\Models\Field;
use Mastery\Master\App\Models\Node;

trait HasDynamicRelations{
    // creamos una funcion llamada relation donde resive el nombre de la relacion y hace la relacion
    public function relationModel($relation){
        $nodeName = $this->node_name;

        // Intenta encontrar una configuración de campo que corresponda al método llamado y que sea una relación belongsTo
        $node = Node::where('name', $nodeName)->first();

        if (!$node) {
            \Log::error("Node '{$nodeName}' not found.");
            return null;
        }

        $field = Field::where('parent_id', $node->id)->where('value', $relation)->first();

        if ($field && !empty($field->relation_cond)) {
            // 'relation_cond' debe contener el nombre completo de la clase del modelo relacionado, incluyendo el espacio de nombres
            $relationClass = $field->relation_cond;



            if (class_exists($relationClass)) {
                \Log::info('Relation class: ' . $relationClass);
                // Devuelve la relación BelongsTo
                // la primera mayuscula en $relation es para que busque la relacion en el modelo
                $relation = ucfirst($relation);
                $relation = get_class(new $relationClass);
                return $this->belongsTo($relation);
            } else {
                \Log::error("Relation class '{$relationClass}' does not exist.");
            }
        } else {
            \Log::error("Field for relation '{$relation}' not found or relation_cond is empty.");
        }

        return null;
    }


}

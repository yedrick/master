<?php
    namespace Mastery\Master\App\Traits;

    use Mastery\Master\App\Models\Node;
    use Mastery\Master\App\Models\Field;

    trait NodeTrait{

        public function fromNode(){
            $nodeName=$this->node_name;
            $node=Node::where('name',$nodeName)->first();
            $fields_model=Field::where('parent_id',$node->id)->where('display_list', 'show')->orderBy('order', 'asc')->get();
            $hiddenAttributes = $this->getHidden();
            $fields = $fields_model->filter(function ($field) use ($hiddenAttributes) {
                return !in_array($field->name, $hiddenAttributes);
            });
            $data=[];
            foreach ($fields as $field) {
                if($field->relation==true){
                    if(method_exists($this, $field->value)){
                        $data[$field->name]=$this->{$field->value}->fromNode();
                    }else{
                        $data[$field->name]=$this->{$field->name};
                    }
                }else{
                    $data[$field->name]=$this->{$field->name};
                }
            }
            return $data;
        }

        public function fromNodeModel(){
            $nodeName=$this->node_name;
            $node=Node::where('name',$nodeName)->first();
            $fields_model=Field::where('parent_id',$node->id)->where('display_list', 'show')->orderBy('order', 'asc')->get();
            $hiddenAttributes = $this->getHidden();
            $fields = $fields_model->filter(function ($field) use ($hiddenAttributes) {
                return !in_array($field->name, $hiddenAttributes);
            });
            $data=[];
            foreach ($fields as $field) {
                if($field->relation==true){
                    if(method_exists($this, $field->value)){
                        $data[$field->name]=$this->{$field->value}->name;
                    }else{
                        $data[$field->name]=$this->{$field->name};
                    }
                }else{
                    $data[$field->name]=$this->{$field->name};
                }

            }
            return $data;
        }

        public function fromDataModel(){
            $nodeName=$this->node_name;
            $node=Node::where('name',$nodeName)->first();
            $fields_model=Field::where('parent_id',$node->id)->where('display_list', 'show')->orderBy('order', 'asc')->get();
            $hiddenAttributes = $this->getHidden();
            $fields = $fields_model->filter(function ($field) use ($hiddenAttributes) {
                return !in_array($field->name, $hiddenAttributes);
            });
            $data=[];
            foreach ($fields as $field) {
                if($field->relation==true){
                    if(method_exists($this, $field->value)){
                        $data[$field->name]=$this->{$field->value}->name;
                    }else{
                        $data[$field->name]=$this->{$field->name};
                    }
                }else{
                    $data[$field->name]=$this->{$field->name};
                }
            }
            return $data;
        }

    }

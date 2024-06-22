<?php

namespace yedrick\Master\App\Exports;

use yedrick\Master\App\Models\Field;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class NodeModelExport implements FromQuery,WithHeadings,WithMapping,WithTitle{
    /**
    * @return \Illuminate\Support\Collection
    */
    // use Exportable;

    protected $node;
    protected $fields;
    protected $model;

    public function __construct($node){
        $this->node=$node;
        $this->model=$node->model;
        $this->fields=Field::where('parent_id',$node->id)->get();
    }

    public function headings(): array{
        $headings = [];
        foreach ($this->fields as $field) {
            $headings[]=__($field->label) ;
        }
        return $headings;
    }

    public function map($item): array{
        $data=[];
        foreach ($this->fields as $key => $field) {
            if($field->relation === 1){
                $data[]=$item->{$field->value}->name??$item->{$field->name};
            }else{
                $data[]=$item->{$field->name};
            }
        }
        return $data;
    }

    // public function collection(){
    //     return PagoServicio::whereIn('id',$this->pagos)->get();
    // }

    public function query(){
        return app($this->model)::query();
        // return PagoProveedor::query()->whereIn('id',$this->pagos);
    }

    public function title(): string{
        return __($this->node->plural);
    }

}

<?php

namespace yedrick\Master\App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ExcelExportReport implements WithMultipleSheets
{
    /**
     * @return \Illuminate\Support\Collection
     */
    private $data;
    protected $sheetsData;

    public function __construct(array $sheetsData)
    {
        $this->sheetsData = $sheetsData;
    }


    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->sheetsData as $sheetName => $sheetData) {
            $sheets[] = new class($sheetData, $sheetName) implements FromCollection, WithTitle {
                protected $data;
                protected $name;

                public function __construct(array $data, string $name)
                {
                    $this->data = $data;
                    $this->name = $name;
                }

                public function collection()
                {
                    return new Collection($this->data);
                }

                public function title(): string
                {
                    return $this->name;
                }
            };
        }

        return $sheets;
    }
}

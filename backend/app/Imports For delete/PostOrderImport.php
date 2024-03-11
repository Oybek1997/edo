<?php

namespace App\Imports;

use App\PostOrder;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithLimit;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PostOrderImport implements WithMultipleSheets
{
    public function model(array $row)
    {
        return new PostOrder([
            "diller" => $row["Дилер"],
            "number" => $row["№"],
            "client" => $row["Клиент"],
            "model" => $row["Модель"],
            "modification" => $row["Модификация"],
            "color" => $row["Цвет"],
            "contract_number" => $row["Код договора"],
            "user_type" => $row["Тип пользователя"] == "Юридическое лицо" ? 1 : 0,
            "estimated_delivery_date" => $row["Предполагаемая дата поставки"] ? $row["Предполагаемая дата поставки"] : null,
            "inn" => $row["ИНН"],
            "client_type" => $row["Тип клиента"],
            "pinfl" => $row["ПИНФЛ"],
            "client_region" => $row["Регион клиента"],
            "address" => $row["Адрес"],
            "group_code" => $row["Группа"],
            "part_number" => time(),
            "status" => 0,
        ]);

    }

    public $startRow = 13;

    public function limit(): int
    {
        return 5; // only take 100 rows
    }
    
    public function headingRow(): int
    {
        return 2;
    }

    public function sheets(): array
    {
        return [
            new FirstSheetImport()
        ];
    }
}
<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;      // para trabajar con colecciones y obtener la data
use Maatwebsite\Excel\Concerns\WithHeadings;        // para definir los títulos de encabezado
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;   // para interactuar con el libro
use Maatwebsite\Excel\Concerns\WithCustomStartCell; // para definir la celda donde inicia el reporte
use Maatwebsite\Excel\Concerns\WithTitle;           // para colocar nombre a las hojas del libro
use Maatwebsite\Excel\Concerns\WithStyles;          // para dar formato a las celdas
use Carbon\Carbon;


class CRMExport implements FromCollection, WithHeadings, WithCustomStartCell, WithTitle, WithStyles
{

    public function collection()
    {

        $data =[];

        $data = User::select('id','name','phone','email',User::raw("DATE_FORMAT(created_at,'%d-%m-%Y') as fecha_creado"),
        User::raw("DATE_FORMAT(created_at,'%H:%i') as hora_creado"),
        'profile',
         User::raw('CASE WHEN email_verified_at THEN cantidad_login ELSE "" END'),
         User::raw('CASE WHEN email_verified_at THEN DATE_FORMAT(last_login,"%d-%m-%Y %H:%i") ELSE "" END')
        )
        ->orderBy('id', 'desc')
        ->where('comercio_id',1)
        ->where('sucursal','<>',1)
        ->get();    

        return $data;
    }

    //cabeceras del reporte
    public function headings() : array
    {
      return [
            'ID',
            'Nombre',
            'Teléfono',
            'Correo electrónico',
            'Fecha creación',
            'Hora creación',
            'Perfil',
            'Cantidad de logueos',
            'Último logueos'
            ];
    }


    public function startCell(): string
    {
        return 'A1';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true ]],
        ];
    }

    public function title(): string
    {
        return 'Catalogo de productos';
    }


}

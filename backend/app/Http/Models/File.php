<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\PdfToImage\Pdf;
use Illuminate\Support\Facades\Storage;
use App\Document;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use SoftDeletes;
    public function objectType()
    {
        return $this->hasOne('App\Http\models\ObjectType', 'id', 'object_type_id');
    }
    public function employeefiles()
    {
        return $this->hasOne('App\Http\models\Employee', 'id', 'object_id');
    }
    public function pdfSigner($id)
    {
        $doc_number = Document::find($id)->firs()->document_number;
        $files = File::where('object_id', $id)->get();

        foreach ($files as $key => $value) {
            $file = storage_path('app\documents\\' . $value->physical_name);
            $pdf = new Pdf($file);
            $numberOfpages = $pdf->getNumberOfPages();
            for ($i = 1; $i <= $numberOfpages; $i++) {
                $pdf->setPage($i)->saveImage(storage_path('app/temp//' . $value->physical_name . '_' .  $i . '.jpg'));
            }
            $pdf = \App::make('snappy.pdf.wrapper');
            $content = Self::style();
            for ($i = 1; $i <= $numberOfpages; $i++) {
                if ($i == 1) {
                    $content .= '<img  height="1950" src="/' . storage_path() . '/app/documents/' . $value->physical_name . '_' .  $i . '.jpg"/>';
                    $content .= '<img style="position: absolute; top: 1800; left: 80%;" height="150" src="/' . storage_path() . '/app/documents/pechat.png"/>';
                } else {
                    $content .= '<img  height="1950" src="/' . storage_path() . '/app/documents/' . $value->physical_name . '_' .  $i . '.jpg"/>';
                }
                Storage::delete(storage_path('app/temp//' . $value->physical_name . '_' .  $i . '.jpg'));
            }

            $pdf->setOption('images', true)
                ->setOption('footer-right', '[page] / [topage]')
                ->setOption('footer-font-name', 'times')
                ->setOption('footer-font-size', '10')
                ->setPaper('a4')
                ->setOption('margin-top', 15)
                ->setOption('margin-bottom', 15)
                ->setOption('margin-left', 20)
                ->setOption('margin-right', 15)
                ->loadHTML($content);
            try {
                 ($pdf->save(storage_path().'/app/documents/'.$value->physical_name));

                //  Storage::putFileAs(
                //     'documents',
                //     $base64,
                //     $value->physical_name
                // );       
                $file = File::find( $value->id)->first();
                $file->file_name =  $doc_number.'_'.($key+1);   
                $file->save();
            } catch (\Throwable $th) {
                throw $th;
                dd($th);
            }
        }
        return  ['status'=>200];

    }
}

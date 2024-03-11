<?php

namespace App\Http\Controllers;

use App\Http\Models\DataType;
use App\Http\Models\File;
use Illuminate\Http\Request;
use App\Http\Models\BlankTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Http\Models\BlankAttributeTemplate;
use App\Http\Models\Document;
use App\Http\Models\DocumentBlankTemplate;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BlankTemplatesController extends Controller
{
    public function download(Request $request)
    {
        $attributes = $request->blank_attribute_templates;
        $fileType = $request->fileType;
        $buyruq_date = $request->buyruq_date;
        $buyruq_num = $request->buyruq_num;

        $b_yil = substr($buyruq_date,0,4);
        $b_oy = substr($buyruq_date,5,2);
        $b_kun = substr($buyruq_date,8,2);

        $blank_id = $attributes[0]['blank_id'];

        $file = File::select('file_name')->where('object_id', $blank_id)->where('object_type_id', 10)->pluck('file_name')->first();

        $fileName = strstr($file, '.', true) . '_' . time();

        if ($fileType == 0) :

            $templateProcessor = new TemplateProcessor('../storage/app/blanks/' . $file);

            foreach ($attributes as $attribute) {
                if ($attribute['data_type_id'] == 10) {
                    $image_name = 'qr_code' . $blank_id . '_' . time() . rand();
                    QrCode::encoding('UTF-8')->format('png')->generate($attribute['set_name'], '../storage/app/blanks/' . $image_name . '.png');
                    $templateProcessor->setImageValue($attribute['parameter_name'], '../storage/app/blanks/' . $image_name . '.png');
                    Storage::delete('blanks/' . $image_name . '.png');

                } else
                    $templateProcessor->setValue($attribute['parameter_name'], str_replace('&',' and ',$attribute['set_name']));
            }

            $templateProcessor->setValue('b_yil', $b_yil);
            $templateProcessor->setValue('b_oy', $b_oy);
            $templateProcessor->setValue('b_kun', $b_kun);
            $templateProcessor->setValue('b_number', $buyruq_num);

            $templateProcessor->saveAs($fileName . '.docx');


            $file_url = ($fileName . '.docx');

        elseif ($fileType == 1) :
            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('../storage/app/blanks/' . $file);
            $sheet = $spreadsheet->getActiveSheet();

            foreach ($attributes as $attribute) {
                $sheet->setCellValue($attribute['parameter_name'], $attribute['set_name']);
            }

            $writer = new Xlsx($spreadsheet);
            $writer->save($fileName . '.xlsx');

            $file_url = ($fileName . '.xlsx');
        endif;

        return $file_url;
    }

    public function deleteFile(Request $request)
    {
        $file = $request->input('file');

        unlink($file);
        return 'successfully deleted';
    }

    public function index()
    {
        $blankTemplates = BlankTemplate::leftJoin('files', 'blank_templates.id', '=', 'files.object_id')->where('object_type_id', 10);

        return $blankTemplates->select('blank_templates.*', 'files.file_name')->get();
    }

    public function create()
    {
        //
    }

    public function updateAttribute(Request $request)
    {

        $att = $request->all();
        foreach ($att as $key => $r) {
            $model = BlankAttributeTemplate::find($r['id']);
            if (!$model) {
                $model = new BlankAttributeTemplate();
                $model->created_by = Auth::id();
            }
            $model->blank_id = $r['blank_id'];
            $model->attribute_name = $r['attribute_name'];
            $model->parameter_name = $r['parameter_name'];
            $model->data_type_id = $r['data_type_id'];
            $model->updated_by = Auth::id();
            $model->save();
        }
        return 'Success';
    }

    public function show($file)
    {
        $templateProcessor = new TemplateProcessor('../storage/app/blanks/' . $file);
        $templateProcessor->saveAs($file);
        $file_url = ($file);
        return $file_url;
    }

    public function edit(BlankTemplate $blankTemplate)
    {
        //
    }

    public function getRef($id)
    {
        $file = BlankTemplate::select('blank_name')->where('id', $id)->pluck('blank_name')->first();
        $fileType = BlankTemplate::select('file_type')->where('id', $id)->pluck('file_type')->first();
        $dataType = DataType::get();
        $blankAttributeTemplate = BlankAttributeTemplate::where('blank_id', $id)->get();
        return [
            'fileName' => $file,
            'fileType' => $fileType,
            'dataType' => $dataType,
            'blankAttributeTemplate' => $blankAttributeTemplate
        ];
    }

    public function getBlank()
    {
        $blankTemplates = BlankTemplate::get();
        return $blankTemplates;
    }

    public function deleteAttribute($id)
    {

        BlankAttributeTemplate::find($id)->delete();
        return 'Deleted successfully';
    }

    public function update(Request $request)
    {
        $model = BlankTemplate::find($request->input('id'));
        $file = $request->file('file');

        if (!$model) {
            $extension = $file->getClientOriginalExtension();
            $model = new BlankTemplate();
            if ($extension == 'doc' || $extension == 'docx') {
                $model->file_type = 0;
            } else {
                $model->file_type = 1;
            }
            $model->created_by = Auth::id();
        }
        $model->blank_name = $request->input('blank_name');
        $model->description = $request->input('description');
        $model->is_active = $request->input('is_active');
        $model->updated_by = Auth::id();
        $model->save();

        if ($file) {
            DB::beginTransaction();

            try {
                $fileName = $file->getClientOriginalName();
                $physicalName = time() . rand();
                $object_type = 10;
                $object_id = $model->id;

                $fileModel = File::where('object_id', $object_id)->where('object_type_id', $object_type)->first();
                if ($fileModel) Storage::delete('blanks/' . $fileModel->file_name);
                if (!$fileModel) {
                    $fileModel = new File();
                    $fileModel->created_by = Auth::id();
                }
                $fileModel->object_type_id = $object_type;
                $fileModel->file_name = $fileName;
                $fileModel->physical_name = $physicalName;
                $fileModel->object_id = $object_id;
                $fileModel->updated_by = Auth::id();
                $fileModel->save();
                Storage::putFileAs('blanks', $file, $fileName);
                DB::commit();
                return 'Saved successfully';
            } catch (\Throwable $th) {
                DB::rollBack();
                return $th;
            }
        }
    }

    public function destroy($id)
    {
        $model = BlankTemplate::find($id);
        foreach ($model->blank_attribute_template as $key => $value) {
            $value->delete();
        }
        $model->delete();
    }

    public function getBlanks()
    {
        return BlankTemplate::with('blank_attribute_template')->where('is_active', 1)->get();
    }
}

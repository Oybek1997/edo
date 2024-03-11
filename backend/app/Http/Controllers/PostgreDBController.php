<?php

namespace App\Http\Controllers;

// use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Http\Models\Document;

class PostgreDBController extends Controller
{
    public function migrate()
    {
        try {
            $documents = Document::where('pg_migrated', 0)
                ->whereNotNull('pdf_table')
                ->where('pg_migrated', 0)
                ->limit(100)
                ->orderBy('id', 'desc')
                ->get();
            foreach ($documents as $key => $value) {
                $table_name = $value->pdf_table;
                if (!Schema::connection('pg_edo')->hasTable($table_name)) {
                    Schema::connection('pg_edo')->create($table_name, function ($table) {
                        $table->increments('id');
                        $table->unsignedInteger('document_id');
                        $table->longText('pdfBase64');
                        $table->longText('eimzoBase64')->nullable();
                        $table->string('name', 10)->default('pdf');
                        $table->index('document_id');
                    });
                }

                $mysql_data = DB::connection('mysql_workflow_pdf')->select('select * from ' . $table_name . " where document_id=" . $value->id);
                if (isset($mysql_data[0])) {
                    $mysql_data = $mysql_data[0];
                    $pdf = DB::connection('pg_edo')->table($table_name)->where('document_id', $mysql_data->document_id)->first();
                    if (!$pdf) {

                        DB::connection('pg_edo')->table($table_name)
                            ->insert(
                                [
                                    'document_id' => $mysql_data->document_id,
                                    'pdfBase64' => $mysql_data->pdfBase64,
                                    'eimzoBase64' => $mysql_data->eimzoBase64,
                                    'name' => $mysql_data->name
                                ]
                            );
                        echo " - " . $value->id;
                    }
                    $value->pg_migrated = 1;
                    $value->save();
                } else {
                    $value->pg_migrated = 2;
                    $value->save();
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        echo "<br>" . date('Y-m-d H:i:s') . "<script>location.reload();window.reload();</script>";
    }

    public function migrate1()
    {
        try {
            $documents = Document::where('pg_migrated', 0)
                ->whereNotNull('pdf_table')
                ->where('pg_migrated', 0)
                ->limit(100)
                ->orderBy('id', 'asc')
                ->get();
            foreach ($documents as $key => $value) {
                $table_name = $value->pdf_table;
                if (!Schema::connection('pg_edo')->hasTable($table_name)) {
                    Schema::connection('pg_edo')->create($table_name, function ($table) {
                        $table->increments('id');
                        $table->unsignedInteger('document_id');
                        $table->longText('pdfBase64');
                        $table->longText('eimzoBase64')->nullable();
                        $table->string('name', 10)->default('pdf');
                        $table->index('document_id');
                    });
                }

                $mysql_data = DB::connection('mysql_workflow_pdf')->select('select * from ' . $table_name . " where document_id=" . $value->id);
                if (isset($mysql_data[0])) {
                    $mysql_data = $mysql_data[0];
                    $pdf = DB::connection('pg_edo')->table($table_name)->where('document_id', $mysql_data->document_id)->first();
                    if (!$pdf) {

                        DB::connection('pg_edo')->table($table_name)
                            ->insert(
                                [
                                    'document_id' => $mysql_data->document_id,
                                    'pdfBase64' => $mysql_data->pdfBase64,
                                    'eimzoBase64' => $mysql_data->eimzoBase64,
                                    'name' => $mysql_data->name
                                ]
                            );
                        echo " - " . $value->id;
                    }
                    $value->pg_migrated = 1;
                    $value->save();
                } else {
                    $value->pg_migrated = 2;
                    $value->save();
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        echo "<br>" . date('Y-m-d H:i:s') . "<script>location.reload();window.reload();</script>";
    }

    public function test()
    {
        if (Schema::connection('mysql_workflow_pdf')->hasTable('pdf' . $id) && DB::connection('mysql_workflow_pdf')->select('select count(id) count from pdf' . $id)[0]->count < 5000) {
            $table_name = 'pdf' . $id;
        } else {
            $id = LastPdfTable::insertGetId(['name' => 'pdf']);
            Schema::connection('mysql_workflow_pdf')->create('pdf' . $id, function ($table) {
                $table->increments('id');
                $table->unsignedInteger('document_id');
                $table->longText('pdfBase64');
                $table->longText('eimzoBase64')->nullable();
                $table->string('name', 10)->default('pdf');
                $table->index('document_id');
            });
            $table_name = 'pdf' . $id;
        }
    }
}

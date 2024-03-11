<?php

namespace App\Services;

use App\MailQueue;
use App\Http\Models\DocumentSigner;
use App\Http\Models\Document;
use App\Http\Models\Employee;
use App\User;

class SendEmail
{
    public static function addToQueue(string $mail, int $document_id,string $type)
    {
        $mailQueue = new MailQueue();
        $mailQueue->address = $mail;
        $document = Document::where('id', $document_id)->with('documentTemplate')->with('documentType')->first();
        $content = [
                                        'Hujjat turi' => $document->documentType->name_uz_latin,
                                        'Raqami' => $document->document_number,
                                        'Sanasi' => $document->document_date,
                                        'Yaratuvchi' => $document->employee->getFullname('uz_latin'),
                                        "Bo'lim" => Employee::parentDepartments($document->employee->tabel)['main_department'] ? Employee::parentDepartments($document->employee->tabel)['main_department']->name_uz_latin : '',
                                        "Talab etiladigan amal" => $type,
                                        'Link' => config('app.APP_EDO_URL') . '/#/document/' . $document->pdf_file_name,
                                    ];

        $mailQueue->content = json_encode($content);
        $mailQueue->title = $document->document_number.'. '.$document->documentTemplate->name_uz_latin;
        $mailQueue->document_id = $document_id;
        $mailQueue->user_phone = User::where('email', $mail)->first() ?
            (User::where('email', $mail)->first()->employee ? 
                (User::where('email', $mail)->first()->employee->employeePhones ? 
                    (User::where('email', $mail)->first()->employee->employeePhones->where('phone_type', 'Mobile')->first() ? 
                        User::where('email', $mail)->first()->employee->employeePhones->where('phone_type', 'Mobile')->first()->phone_number : null) : null) : null) : null;
        $mailQueue->save();
    }
    
}

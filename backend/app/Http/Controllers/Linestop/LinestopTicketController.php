<?php

namespace App\Http\Controllers\Linestop;

use App\Http\Models\Linestop\Ticket;
use App\Http\Models\Linestop\TicketUser;
use App\Http\Models\Linestop\TicketComment;
use App\Http\Models\Linestop\TicketCommentFile;
use App\Http\Models\Linestop\TicketFile;
use App\Http\Models\Linestop\Plcdata;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;


class LinestopTicketController extends Controller
{
    public function getticket(Request $request)
    {
        $page = $request->input('pagination.page');
        $itemsPerPage = $request->input('pagination.itemsPerPage');
        $ticketId = $request->input('id');

        if ($ticketId) {
            $singleTicketWithTicketUser = TicketUser::where('ticket_id', $ticketId)->with('employee')->get();
            $singleTicketWithPlcdata = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
                ->with([
                    'plcdata.line.shop',
                    'TicketFile',
                    'TicketComment.employee',
                    'TicketComment.ticketCommentFile',
                    'TicketUser' => function ($query) {
                        $query->whereIn('status', [0, 1])->select('staff_id', 'employee_id', 'ticket_id');
                        $query->with([
                            'employee' => function ($query) {
                            }
                        ]);
                    }
                ])
                ->selectRaw('ticket.*, EXTRACT(EPOCH FROM plcdata.startdt - plcdata.stopdt) AS duration')
                ->where('ticket.plcdata_id', $ticketId)
                ->first();

            return [
                'ticket' => $singleTicketWithPlcdata,
                'ticketuser' => $singleTicketWithTicketUser
            ];
        } else {
            $ticketsWithPlcdata = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
                ->with('plcdata.line.shop')
                ->selectRaw('ticket.*, EXTRACT(EPOCH FROM plcdata.startdt - plcdata.stopdt) AS duration')
                ->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page', $page);
            return [
                'tickets' => $ticketsWithPlcdata,
            ];
        }
    }

    public function getAllTicketsWithPlcdata(Request $request)
    {
        $page = $request->input('pagination.page');
        $itemsPerPage = $request->input('pagination.itemsPerPage');
        $line = $request->input('line');
        $status = $request->input('status');
        $search = $request->input('search');

        $query = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
            ->with('plcdata.line.shop')
            ->with(['ticketUser' => function ($query) {
                $query->where('status', 0)
                    ->orWhere('status', 1);
                $query->with([
                    'employee' => function ($query) {
                    }
                ]);
            }])
            ->selectRaw('ticket.*, EXTRACT(EPOCH FROM (plcdata.startdt - plcdata.stopdt)) AS duration')
            ->where('plcdata.status', 0);


        if ($line) {
            $query->where('plcdata.lineid', $line);
        }
        if ($status !== null) {
            $query->where('ticket.status', $status);
        }
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->where('plcdata.sector', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.startdt', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.id', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.stopdt', 'LIKE', '%' . $search . '%');
            });
        }

        $ticketsWithPlcdata = $query->orderBy('id', 'desc')
            ->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page', $page);

        return [
            'tickets' => $ticketsWithPlcdata
        ];
    }
    public function getAllTicketsWithPlcdataExcel(Request $request)
    {
        $results = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
            ->with('plcdata.line.shop')
            ->selectRaw('ticket.*, EXTRACT(EPOCH FROM plcdata.startdt - plcdata.stopdt) AS duration')
            ->where('plcdata.status', 0)
            ->get();

        $modifiedData = [];

        foreach ($results as $item) {
            $modifiedItem = [
                'ид' => 'T-00' . $item->plcdata_id,
                'Цех' => $item->plcdata->line->shop->name,
                'Линия' => $item->plcdata->line->line,
                'Сектор' => $item->plcdata->sector,
                'Время_остановки' => $item->plcdata->stopdt,
                'Время_запуска' => $item->plcdata->startdt,
                'Продолжительность' => $item->duration,
                'Создан' => $item->plcdata->status,
                'Статус' => $item->status,
            ];

            $modifiedData[] = $modifiedItem;
        }

        return response()->json($modifiedData);
    }

    public function getAllTickets(Request $request)
    {
        $page = $request->input('pagination.page');
        $itemsPerPage = $request->input('pagination.itemsPerPage');
        $line = $request->input('line');
        $status = $request->input('status');
        $search = $request->input('search');
        $creater = $request->input('creater');

        $AllTickets = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
            ->with('plcdata.line.shop')
            ->with([
                'ticketUser' => function ($query) {
                    $query->where('status', 0)
                        ->orWhere('status', 1);
                    $query->with([
                        'employee' => function ($query) {
                        }
                    ]);
                }
            ])
            ->selectRaw('ticket.*, EXTRACT(EPOCH FROM plcdata.startdt - plcdata.stopdt) AS duration');

        if ($line) {
            $AllTickets->where('plcdata.lineid', $line);
        }
        if ($status !== null) {
            $AllTickets->where('ticket.status', $status);
        }
        if ($creater !== null) {
            $AllTickets->where('plcdata.status', $creater);
        }
        if ($search) {
            $AllTickets->where(function ($query) use ($search) {
                $query->where('plcdata.sector', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.startdt', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.id', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.stopdt', 'LIKE', '%' . $search . '%');
            });
        }

        $AllTickets = $AllTickets->orderBy('ticket.id', 'desc')
            ->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page', $page);

        return [
            'tickets' => $AllTickets,
        ];
    }
    public function getAllTicketsExcel(Request $request)
    {
        $results = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
            ->with('plcdata.line.shop')
            ->selectRaw('ticket.*, EXTRACT(EPOCH FROM plcdata.startdt - plcdata.stopdt) AS duration')
            ->get();

        $modifiedData = [];

        foreach ($results as $item) {
            $modifiedItem = [
                'ид' => 'T-00' . $item->plcdata_id,
                'Цех' => $item->plcdata->line->shop->name,
                'Линия' => $item->plcdata->line->line,
                'Сектор' => $item->plcdata->sector,
                'Время_остановки' => $item->plcdata->stopdt,
                'Время_запуска' => $item->plcdata->startdt,
                'Продолжительность' => $item->duration,
                'Создан' => $item->plcdata->status,
                'Статус' => $item->status,
            ];

            $modifiedData[] = $modifiedItem;
        }

        return response()->json($modifiedData);
    }


    public function getAllOpenTickets(Request $request)
    {
        $page = $request->input('pagination.page');
        $itemsPerPage = $request->input('pagination.itemsPerPage');
        $status = $request->input('status');
        $search = $request->input('search');
        $creater = $request->input('creater');
        $line = $request->input('line');

        $AllOpenTickets = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
            ->with('plcdata.line.shop')
            ->with(['ticketUser' => function ($query) {
                $query->where('status', 0)
                    ->orWhere('status', 1);
                $query->with([
                    'employee' => function ($query) {
                    }
                ]);
            }])
            ->where('ticket.status', '!=', '3')
            ->selectRaw('ticket.*, EXTRACT(EPOCH FROM plcdata.startdt - plcdata.stopdt) AS duration');

        if ($line) {
            $AllOpenTickets->where('plcdata.lineid', $line);
        }
        if ($status !== null) {
            $AllOpenTickets->where('ticket.status', $status);
        }
        if ($creater !== null) {
            $AllOpenTickets->where('plcdata.status', $creater);
        }
        if ($search) {
            $AllOpenTickets->where(function ($query) use ($search) {
                $query->where('plcdata.sector', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.startdt', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.id', 'LIKE', '%' . $search . '%')
                    ->orWhere('plcdata.stopdt', 'LIKE', '%' . $search . '%');
            });
        }

        $AllOpenTickets = $AllOpenTickets->orderBy('ticket.id', 'desc')
            ->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page', $page);

        return [
            'tickets' => $AllOpenTickets,
        ];
    }
    public function getAllOpenTicketsExcel(Request $request)
    {
        $results = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
            ->with('plcdata.line.shop')
            ->where('ticket.status', '!=', '2')
            ->selectRaw('ticket.*, EXTRACT(EPOCH FROM plcdata.startdt - plcdata.stopdt) AS duration')
            ->get();

        $modifiedData = [];

        foreach ($results as $item) {
            $modifiedItem = [
                'ид' => 'T-00' . $item->plcdata_id,
                'Цех' => $item->plcdata->line->shop->name,
                'Линия' => $item->plcdata->line->line,
                'Сектор' => $item->plcdata->sector,
                'Время_остановки' => $item->plcdata->stopdt,
                'Время_запуска' => $item->plcdata->startdt,
                'Продолжительность' => $item->duration,
                'Создан' => $item->plcdata->status,
                'Статус' => $item->status,
            ];

            $modifiedData[] = $modifiedItem;
        }

        return response()->json($modifiedData);
    }

    public function getTicketsBySelectedDate(Request $request)
    {
        $page = $request->input('pagination.page');
        $itemsPerPage = $request->input('pagination.itemsPerPage');
        $selectedDate = $request->input('selectedDate');

        $query = Ticket::join('plcdata', 'ticket.plcdata_id', '=', 'plcdata.id')
            ->with('plcdata.line.shop')
            ->selectRaw('ticket.*, EXTRACT(EPOCH FROM plcdata.startdt - plcdata.stopdt) AS duration')
            ->orderBy('id', 'desc');

        if ($selectedDate) {
            $query->whereDate('plcdata.startdt', '=', $selectedDate);
        }

        $allTickets = $query->paginate($itemsPerPage == '-1' ? 1000000 : $itemsPerPage, ['*'], 'page', $page);

        return [
            'tickets' => $allTickets,
        ];
    }

    public function getDepartments(Request $request)
    {
        $searchTerm = $request->input('search');

        $query = "
    SELECT s.id AS staff_id, d.id as dep_id, CONCAT(e.firstname_uz_latin, ' ', e.lastname_uz_latin) AS fio, d.department_code AS dep_code, d.name_uz_latin AS department, p.name_uz_latin AS position, e.id AS employee_id
    FROM staff s
    INNER JOIN departments d ON d.id = s.department_id
    INNER JOIN positions p ON p.id = s.position_id
    INNER JOIN employee_staff es ON es.staff_id = s.id
    INNER JOIN employees e ON e.id = es.employee_id
    WHERE CAST(s.is_active AS BOOLEAN) = true
    AND CAST(es.is_active AS BOOLEAN) = true
    AND s.deleted_at IS NULL
    AND es.deleted_at IS NULL
    AND e.deleted_at IS NULL
    AND d.deleted_at IS NULL
    AND p.deleted_at IS NULL
    AND (
        CONCAT(e.firstname_uz_latin, ' ', e.lastname_uz_latin) LIKE '%$searchTerm%'
        OR d.department_code LIKE '%$searchTerm%'
        OR d.name_uz_latin LIKE '%$searchTerm%'
        OR p.name_uz_latin LIKE '%$searchTerm%'
    )
    ORDER BY d.department_code
";
        $departments = DB::select($query);

        return $departments;
    }
    public function updateTicket(Request $request)
    {
        $ticketId = $request->input('ticketId');
        $selectedDepartmentData = json_decode($request->input('selectedDepartment'));
        $staff_id = $request->input('staff_id');
        $employee_id = $request->input('employee_id');
        $selectedReason = $request->input('selectedReason');
        $selectedProductmodel = $request->input("selectedProductmodel");
        $detailNumber = $request->input("detailNumber");
        $selectedProductmodelint = intval($selectedProductmodel);
        $detailNumberint = intval($detailNumber);
        $selectedProvider = $request->input('selectedProvider');
        $duration = $request->input('duration');
        $description = $request->input('description');

        // Tiketga oid file yozish qismi boshlandi
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $fileContents = file_get_contents($file);
                Storage::put('linestop/' . $fileName, $fileContents);

                $ticketFile = new TicketFile();
                $ticketFile->ticket_id = $ticketId;
                $ticketFile->pythiscal_name = $fileName;
                $ticketFile->save();
            }
        }
        // Tiketga oid file yozish qismi tugadi

        // Tiket update qilish qismi boshlandi
        $ticket = Ticket::where('plcdata_id', $ticketId)->first();
        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        $ticket->update([
            'duration' => $duration,
            'reason_id' => $selectedReason,
            'provider_id' => $selectedProvider,
            'product_id' => $selectedProductmodelint,
            'detail_number' => $detailNumberint,
            'description' => $description,
            'status' => '1',
        ]);
        // Tiket update qilish qismi tugadi

        // TiketUser yozish qismi boshlandi
        $existingRecord = TicketUser::where('ticket_id', $ticketId)
            ->where('staff_id', $staff_id)
            ->where('employee_id', $employee_id)
            // ->where('status', 2)
            ->first();

        if (!$existingRecord) {
            $firstTicketUser = new TicketUser([
                'ticket_id' => $ticketId,
                'staff_id' => $staff_id,
                'employee_id' => $employee_id,
                'status' => 2,
            ]);
            $firstTicketUser->save();
        }

        if (!$selectedDepartmentData) {
            return response()->json(['error' => 'Invalid selectedDepartment']);
        }

        if (is_object($selectedDepartmentData)) {
            $staff_id_department = $selectedDepartmentData->staff_id;
            $employee_id_department = $selectedDepartmentData->employee_id;
        } else {
            return response()->json(['error' => 'SelectedDepartment is not a valid object']);
        }

        $existingTicketUser = TicketUser::where([
            'ticket_id' => $ticketId,
            'staff_id' => $staff_id_department,
            'employee_id' => $employee_id_department,
            'parent_staff_id' => $staff_id,
            'parent_employee_id' => $employee_id,
            'status' => 0,
        ])->first();

        if (!$existingTicketUser) {
            $ticketUser = new TicketUser([
                'ticket_id' => $ticketId,
                'staff_id' => $staff_id_department,
                'employee_id' => $employee_id_department,
                'parent_staff_id' => $staff_id,
                'parent_employee_id' => $employee_id,
                'status' => 0,
            ]);
            $ticketUser->save();
            $resolution = TicketUser::where([
                'ticket_id' => $ticketId,
                'staff_id' => $staff_id,
                'employee_id' => $employee_id,
                'status' => 0,
            ])->first();
            if ($resolution) {
                $resolution->status = 2;
                $resolution->save();
            }
        }
        // TiketUser yozish qismi tugadi
        return response()->json(['message' => 'Ticket updated successfully']);
    }

    public function closeTicket(Request $request)
    {
        $ticketId = $request->input('ticketId');
        $ticket = Ticket::where('id', $ticketId)->first();
        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
        $ticket->update([
            'status' => '3',
        ]);

        $ticketuser = TicketUser::where('ticket_id', $ticketId)->where('status', 0)->first();
        if (!$ticketuser) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
        $ticketuser->update([
            'status' => '1',
        ]);

        return response()->json(['message' => 'Ticket closed successfully']);
    }
    public function createTicket(Request $request)
    {
        $selectedLineid = $request->input("selectedLineid");
        $selectedSector = $request->input("selectedSector");
        $startLinedate = $request->input("startLinedate");
        $stopLinedate = $request->input("stopLinedate");
        $statusPlcdata = $request->input("statusPlcdata");
        $createdbyTabel = $request->input("createdbyTabel");
        $selectedDepartmentData = json_decode($request->input('selectedDepartment'));
        $staff_id = $request->input('staff_id');
        $employee_id = $request->input('employee_id');

        $selectedReason = $request->input("selectedReason");
        $selectedProductmodel = $request->input("selectedProductmodel");
        $detailNumber = $request->input("detailNumber");
        $selectedProvider = $request->input("selectedProvider");
        $statusTicket = $request->input("statusTicket");
        $selectedDepartment = $request->input("selectedDepartment");
        $selectedDepartmentData = json_decode($request->input('selectedDepartment'));
        $user_id = $request->input("user_id");
        $description = $request->input("text");



        if (isset($selectedLineid, $selectedSector, $startLinedate, $stopLinedate, $statusPlcdata, $createdbyTabel)) {
            $plcData = new Plcdata();
            $plcData->lineid = $selectedLineid;
            $plcData->sector = $selectedSector;
            $plcData->stopdt = $stopLinedate;
            $plcData->startdt = $startLinedate;
            $plcData->status = $statusPlcdata;
            $plcData->created_by = $createdbyTabel;
            $plcData->save();
        }

        if (isset($plcData) && isset($selectedReason, $selectedProvider, $statusTicket)) {
            $ticket = new Ticket();
            $ticket->plcdata_id = $plcData->id;
            $ticket->reason_id = $selectedReason;
            $ticket->product_id = $selectedProductmodel;
            $ticket->provider_id = $selectedProvider;
            $ticket->detail_number = $detailNumber;
            $ticket->description = $description;
            $ticket->status = $statusTicket;
            $ticket->created_at = now();
            $ticket->save();

            $ticketId = $ticket->id;
        }

        $existingRecord = TicketUser::where('ticket_id', $ticketId)
            ->where('staff_id', $staff_id)
            ->where('employee_id', $employee_id)
            ->where('status', 0)
            ->first();

        if (!$existingRecord) {
            $firstTicketUser = new TicketUser([
                'ticket_id' => $ticketId,
                'staff_id' => $staff_id,
                'employee_id' => $employee_id,
                'status' => 0,
            ]);

            $firstTicketUser->save();
        }

        if ($selectedDepartmentData) {
            $existingTicketUser = TicketUser::where([
                'ticket_id' => $ticketId,
                'parent_staff_id' => $staff_id,
                'parent_employee_id' => $employee_id,
                'staff_id' => $selectedDepartmentData->staff_id,
                'employee_id' => $selectedDepartmentData->employee_id,
            ])->first();

            if (!$existingTicketUser) {
                $ticketUser = new TicketUser([
                    'ticket_id' => $ticketId,
                    'staff_id' => $selectedDepartmentData->staff_id,
                    'employee_id' => $selectedDepartmentData->employee_id,
                    'parent_staff_id' => $staff_id,
                    'parent_employee_id' => $employee_id,
                    'status' => 1,
                ]);
                $ticketUser->save();
            }
        } else {
            return response()->json(['error' => 'Invalid selectedDepartment'], 400);
        }

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileName = time() . '_' . $file->getClientOriginalName();
                $fileContents = file_get_contents($file);
                Storage::put('linestop/' . $fileName, $fileContents);

                $ticketFile = new TicketFile();
                $ticketFile->ticket_id = $ticketId;
                $ticketFile->pythiscal_name = $fileName;
                $ticketFile->save();
            }
        }

        return response()->json([
            'plcData' => $plcData,
            'ticket' => $ticket,
        ], 201);
    }

    public function getTicketUser(Request $request)
    {
        $ticketId = $request->input('ticketId');
        $getalldata = TicketUser::query()
            ->with('ticket.plcdata.line.shop')
            ->with('staff')
            ->with('employee')
            ->with('parentStaff')
            ->with('parent_employee')
            ->where('ticket_id', $ticketId)
            ->get();
        return $getalldata;
    }
    public function getTicketViewers(Request $request)
    {
        $ticketId = $request->input('ticketId');
        $getviewers = TicketUser::where('ticket_id', $ticketId)
            ->with('staff.department')
            ->with('employee')
            ->distinct('employee_id')
            ->get();

        return $getviewers;
    }

    public function sendFileComment(Request $request)
    {
        $ticketId = $request->input('ticketId');
        $comment = $request->input('comment');
        $created_by = $request->input('created_by');

        if (isset($comment)) {
            $ticketComment = new TicketComment();
            $ticketComment->ticket_id = $ticketId;
            $ticketComment->comment = $comment;
            $ticketComment->created_by = $created_by;
            $ticketComment->save();

            $ticketCommentId = $ticketComment->id;

            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . '_' . $file->getClientOriginalName();
                    $fileContents = file_get_contents($file);
                    Storage::put('linestop/' . $fileName, $fileContents);

                    $ticketFile = new TicketCommentFile();
                    $ticketFile->ticket_comment_id = $ticketCommentId;
                    $ticketFile->pythiscal_name = $fileName;
                    $ticketFile->save();
                }
            }
        }
        return 'Omadli yakun';
    }
    public function ticketDownload($filename)
    {
        if (!Storage::exists('linestop/' . $filename)) {
            return response()->json(['message' => 'File not found.'], Response::HTTP_NOT_FOUND);
        }

        $fileContents = Storage::get('linestop/' . $filename);
        return response($fileContents)
            ->header('Content-Type', Storage::mimeType('linestop/' . $filename))
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    public function allMyLineStops(Request $request)
    {
        $page = $request->input('pagination.page', 1);
        $itemsPerPage = $request->input('pagination.itemsPerPage', 10);
        $search = $request->input('search');
        $myLineId = $request->input('mylineid');

        $query = Ticket::with('plcdata.line.shop')
            ->whereHas('ticketUser', function ($query) use ($myLineId) {
                $query->where('employee_id', $myLineId);
            });
        $queryCount = Ticket::with('plcdata.line.shop')
            ->whereHas('ticketUser', function ($query) use ($myLineId) {
                $query->where('employee_id', $myLineId)
                    ->where('status', 0);
            })
            ->count();


        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('sector', 'LIKE', "%{$search}%")
                    ->orWhere('startdt', 'LIKE', "%{$search}%")
                    ->orWhere('id', 'LIKE', "%{$search}%")
                    ->orWhere('stopdt', 'LIKE', "%{$search}%");
            });
        }

        $AllMylinestops = $query->orderBy('created_at', 'desc')
                        ->paginate($itemsPerPage, ['*'], 'pagination.page', $page);

        return [
            'mylinestops' => $AllMylinestops,
            'mylinestopsq' => $queryCount,
        ];
    }

    public function acceptTicket(Request $request)
    {
        $ticketId = $request->input('ticketId');
        $ticket = Ticket::where('plcdata_id', $ticketId)->first();
        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }
        $ticket->update([
            'status' => '2',
        ]);
    }

    /// daily report
    public function dailyReport(Request $request)
    {
        $query = "
        SELECT 
        l.line AS LineName,
        DATE(t.created_at) AS Date,
        COUNT(t.id) AS TicketCount
    FROM 
        ticket t
        JOIN plcdata p ON t.plcdata_id = p.id
        JOIN line l ON p.lineid = l.id
    GROUP BY 
        LineName, 
        DATE(t.created_at)
    ORDER BY 
        LineName, 
        DATE(t.created_at);"
            ;
    $dailyreasons = DB::connection('linestop')->select($query);
        return $dailyreasons;
    }
}

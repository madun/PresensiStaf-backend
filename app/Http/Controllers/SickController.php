<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\User;
use App\Sick;
use App\Days;
use App\Attendance;
use App\Schedule;
use Yajra\DataTables\Datatables;

class SickController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('sick.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('sick.form', ['action' => 'create']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $date = explode(" - ", request()->date_sick, 2);
        
        $dateStart = Carbon::parse($date[0]);
        $dateEnd = Carbon::parse($date[1]);
        $date1 = date('Y-m-d', strtotime($dateStart->toDateTimeString()));
        $date2 = date('Y-m-d', strtotime($dateEnd->toDateTimeString()));

        $period = CarbonPeriod::create($date1, $date2);

        // Iterate over the period
        $dates = [];
        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }

        $sick = new Sick;
        $sick->users_id = request()->users_id;
        $sick->date_sick = json_encode($dates);
        $sick->amount = count($dates);
        $sick->status = request()->status;

        if(request()->status == 'approved'){
            foreach($dates as $date){
                $dateSick = Carbon::parse($date);
                $getDays = Days::where('name_day', $dateSick->format('l'))->first();
                $getSchedule = Schedule::where('users_id', $sick->users_id)->where('days_id', $getDays->id)->first();
                
                if($getSchedule){ // jika ada jadwalnya, maka create
                    Attendance::create([
                        "user_id" => $sick->users_id,
                        // "user_id" => 1,
                        "start" => Carbon::parse($date.' '.$getSchedule->clock_in)->addHour(7),
                        "end" => Carbon::parse($date.' '.$getSchedule->clock_out)->addHour(7),
                        "is_on_area" => "0",
                        "hours" => $getSchedule->hours,
                        "note_start" => '',
                        "note_end" => '',
                        "status" => 'sakit'
                    ]);
                }
            }
        }

        $sick->is_sick_letter = 1;
        $sick->note = request()->note;
        $sick->request_to = 1;
        $sick->note_from_manager = 'note_from_manager';


        if(request()->hasFile('foto')){
            $file = request()->file('foto');
     
            $nama_file = Carbon::now()->format('dmy').'-'.request()->users_id.'.'.$file->getClientOriginalExtension();
     
            $tujuan_upload = 'foto/sick';

            // replace image with new image
            if($sick->foto && file_exists($tujuan_upload.'/'.$sick->foto)) {
                unlink(public_path($tujuan_upload . '/' . $sick->foto));
            }
            
            $file->move($tujuan_upload,$nama_file);
            $sick->foto = $nama_file;
        }

        $sick->save();

        if($sick){
            return redirect()->route('sick.index')->with('success','Data berhasil disimpan!');
        }

        return redirect()->route('sick.create')->with('danger','Terjadi masalah!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sick = Sick::findOrFail($id);
        return view('sick.form', ['action' => 'edit', 'sick' => $sick]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $date = explode(" - ", request()->date_sick, 2);
        
        $dateStart = Carbon::parse($date[0]);
        $dateEnd = Carbon::parse($date[1]);
        $date1 = date('Y-m-d', strtotime($dateStart->toDateTimeString()));
        $date2 = date('Y-m-d', strtotime($dateEnd->toDateTimeString()));

        $period = CarbonPeriod::create($date1, $date2);

        // Iterate over the period
        $dates = [];
        foreach ($period as $date) {
            array_push($dates, $date->format('Y-m-d'));
        }

        $sick = Sick::findOrFail($id);
        $sick->users_id = request()->users_id;
        $sick->date_sick = json_encode($dates);
        $sick->amount = count($dates);
        $sick->status = request()->status;

        // bersihkan data sebelumnya
        $sickOld = Sick::findOrFail($id);
        foreach(json_decode($sickOld->date_sick) as $date){
            $dateSick = Carbon::parse($date);
            $getDays = Days::where('name_day', $dateSick->format('l'))->first();
            $getSchedule = Schedule::where('users_id', $sick->users_id)->where('days_id', $getDays->id)->first();
            
            if($getSchedule){ // jika ada jadwalnya, maka create
                $dataAttendance = Attendance::whereDate('start',$date)->where('user_id', $sick->users_id)->first();
                if($dataAttendance){
                    $dataAttendance->delete();
                }
            }
        }

        if(request()->status == 'approved'){
            foreach($dates as $date){
                $dateSick = Carbon::parse($date);
                $getDays = Days::where('name_day', $dateSick->format('l'))->first();
                $getSchedule = Schedule::where('users_id', $sick->users_id)->where('days_id', $getDays->id)->first();
                
                if($getSchedule){ // jika ada jadwalnya, maka create
                    Attendance::create([
                        "user_id" => $sick->users_id,
                        // "user_id" => 1,
                        "start" => Carbon::parse($date.' '.$getSchedule->clock_in)->addHour(7),
                        "end" => Carbon::parse($date.' '.$getSchedule->clock_out)->addHour(7),
                        "is_on_area" => "0",
                        "hours" => $getSchedule->hours,
                        "note_start" => '',
                        "note_end" => '',
                        "status" => 'sakit'
                    ]);
                }
            }
        } else {
            foreach($dates as $date){
                $dateSick = Carbon::parse($date);
                $getDays = Days::where('name_day', $dateSick->format('l'))->first();
                $getSchedule = Schedule::where('users_id', $sick->users_id)->where('days_id', $getDays->id)->first();
                
                if($getSchedule){ // jika ada jadwalnya, maka create
                    $dataAttend = Attendance::whereDate('start',$date)->where('user_id', $sick->users_id)->first();
                    if($dataAttend){
                        $dataAttend->delete();
                    }
                }
            }
        }

        $sick->is_sick_letter = 1;
        $sick->note = request()->note;
        $sick->request_to = 1;
        $sick->note_from_manager = 'note_from_manager';


        if(request()->hasFile('foto')){
            $file = request()->file('foto');
     
            $nama_file = Carbon::now()->format('dmy').'-'.request()->users_id.'.'.$file->getClientOriginalExtension();
     
            $tujuan_upload = 'foto/sick';

            // replace image with new image
            if($sick->foto && file_exists($tujuan_upload.'/'.$sick->foto)) {
                unlink(public_path($tujuan_upload . '/' . $sick->foto));
            }
            
            $file->move($tujuan_upload,$nama_file);
            $sick->foto = $nama_file;
        }

        $sick->save();

        if($sick){
            return redirect()->route('sick.index')->with('success','Data berhasil dirubah!');
        }

        return redirect()->route('sick.create')->with('danger','Terjadi masalah!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Sick::findOrFail($id);

        foreach(json_decode($data->date_sick) as $date){
            $dateSick = Carbon::parse($date);
            $getDays = Days::where('name_day', $dateSick->format('l'))->first();
            $getSchedule = Schedule::where('users_id', $data->users_id)->where('days_id', $getDays->id)->first();
            
            if($getSchedule){ // jika ada jadwalnya, maka delete
                $dataAttend = Attendance::whereDate('start',$date)->where('user_id', $data->users_id)->first();
                if($dataAttend){
                    $dataAttend->delete();
                }
            }
        }

        $data->delete();

        return redirect()->route('sick.index')->with('success','Data berhasil dihapus!');
    }

    public function apiSick(){
        if(Auth::user()->id == 1) { // jika admin
        $item = Sick::select('sicks.id', 'users.name', 'sicks.created_at', 'sicks.status')
                    ->leftJoin('users', 'users.id', '=', 'sicks.users_id')
                    ->orderBy('sicks.created_at', 'DESC')
                    ->get();
        } else {
        $item = Sick::select('sicks.id', 'users.name', 'sicks.created_at', 'sicks.status')
                    ->leftJoin('users', 'users.id', '=', 'sicks.users_id')
                    ->where('users_id', Auth::user()->id)
                    ->orderBy('sicks.created_at', 'DESC')
                    ->get();
        }

        return Datatables::of($item)
                ->addIndexColumn()
                ->editColumn('created_at', function ($item) {
                    return date('d-m-Y', strtotime($item->created_at));
                })
                ->addColumn('action', function($item){
                        return 
                        // '<a href="#" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-eye-open"></i> Show</a> '.
                        '<a href="'.route("sick.edit", $item->id).'" class="mr-2"><svg viewBox="0 0 24 24" width="18" height="18" stroke="#ffc107" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><path d="M12 20h9"></path><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"></path></svg></a> '.
                        '<form id="delete-form-'.$item->id.'" method="post" action="'.route("sick.destroy",$item->id).'" style="display: none">
                            '.csrf_field().'
                            '.method_field("DELETE").'
                        </form>'.
                        '<a
                        onclick="
                        if(confirm(\'Are you sure, You Want to delete '.$item->name.'?\'))
                            {
                                event.preventDefault();
                                document.getElementById(\'delete-form-'.$item->id.'\').submit();
                            }else{
                                event.preventDefault();
                        }" 
                        class=""><svg viewBox="0 0 24 24" width="18" height="18" stroke="#dc3545" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="css-i6dzq1"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>';
                    
                })->rawColumns(['action'])->make(true);
    }
}

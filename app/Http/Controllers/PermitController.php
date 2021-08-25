<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Permit;
use App\Attachment;
use App\Date;
use App\Http\Requests\StorePermit;
use Illuminate\Support\Facades\Validator;

class PermitController extends Controller
{
    
    public function index()
    {
        $data = Permit::with('dates', 'attachments')->orderBy('created_at', 'desc')->get();
        
        if(count($data) > 0) {
            $response = array(
                "success" => true,
                "status_code" => 200,
                "message" => "Data berhasil ditemukan",
                "data" => $data
            );
        } else {
            $response = array(
                "success" => false,
                "status_code" => 404,
                "message" => "Data tidak ditemukan"
            );
        }

        return response()->json($response, $response["status_code"]);
    }

    
    public function get($id)
    {
        $data = Permit::find($id);
        
        if($data !== null) {
            $response = array(
                "success" => true,
                "status_code" => 200,
                "message" => "Data berhasil ditemukan",
                "data" => $data
            );
        } else {
            $response = array(
                "success" => false,
                "status_code" => 404,
                "message" => "Data tidak ditemukan"
            );
        }

        return response()->json($response, $response["status_code"]);
    }


    public function store(Request $request) 
    {
        
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'description' => 'required',
            'attachments.*' => 'required|mimes:pdf,docx,doc,jpeg,png,jpg'
        ],[
            'user_id.required' => 'User id is required',
            'description.required' => 'A description is required',
            'attachments.mimes' => 'File needs to be pdf or a picture'
        ]);

        if ($validator->fails()) {
            $response = array(
                "success" => false,
                "status_code" => 400,
                "message" => "Validasi form gagal"
            );
            return response()->json($response, $response["status_code"]);
        }

        $permit = new Permit;
        $permit->user_id = $request->user_id;
        $permit->description = $request->description;
        $permit->save();

        $attachments = $request->file('attachments');
        foreach ($attachments as $item) 
        {

            $filenameWithExt = $item->getClientOriginalName();
            $filename = pathInfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $item->getClientOriginalExtension();
            $FileNameToStore = $filename.'_'.time().'_.'.$extension;
            $path = public_path('attachments/');
            $item->move($path, $FileNameToStore);

            $att_m = new Attachment;
            $att_m->permit_id = $permit->id;
            $att_m->file = "attachments/".$FileNameToStore;
            $att_m->save();
        }

        foreach($request->dates as $item)
        {
            $date = new Date;
            $date->permit_id = $permit->id;
            $date->date = $item;
            $date->save();
        }
        
        $response = array(
            "success" => true,
            "status_code" => 200,
            "message" => "Data berhasil disimpan"
        );
        
        return response()->json($response, $response["status_code"]);

    }

}

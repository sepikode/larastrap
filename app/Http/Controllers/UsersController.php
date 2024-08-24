<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;

class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(UsersDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    public function bulkUpdateStatus(Request $request, $status)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            User::whereIn('id', $ids)->update(['status' => $status]);
            return response()->json([
                'status' => 'success',
                'message' => 'Selected users have been updated successfully.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No users were updated.'
        ]);
    }

    public function bulkAktif(Request $request)
    {
        return $this->bulkUpdateStatus($request, true);
    }

    public function bulkNonAktif(Request $request)
    {
        return $this->bulkUpdateStatus($request, false);
    }


    public function bulkdelete(Request $request)
    {
        $ids = $request->input('ids', []);

        if (!empty($ids)) {
            User::destroy($ids);
            return response()->json([
                'status' => 'success',
                'message' => 'Selected users have been deleted successfully.'
            ]);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'No users were deleted.'
        ]);
    }

}

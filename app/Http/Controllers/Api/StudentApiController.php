<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StudentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        if ($user && method_exists($user, 'hasRole') && $user->hasRole('Siswa')) {
            $student = Student::where('email', $user->email)->with(['kelas', 'jurusan'])->firstOrFail();
            return response()->json(['success' => true, 'data' => $student]);
        }
        
        return response()->json(['success' => true, 'data' => Student::with(['kelas', 'jurusan'])->get()]);
    }
}

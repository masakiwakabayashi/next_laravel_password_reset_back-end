<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// 追記
use App\Http\Requests\StudentRequest;
use App\Models\Student;

class StudentController extends Controller
{
    // 一覧表示
    public function index() {
        $students = Student::all();
        return response()->json([
            'data' => $students
        ], 200);
    }

    // RequestをStudentRequestに変更
    public function store(StudentRequest $request) {
        $student = new Student();
        $student->name = $request->name;
        $student->save();
        return response()->json([
            'data' => $student
        ], 201);
    }

    // 指定のデータのみ取得
    public function edit($id) {
        $student = Student::find($id);
        return response()->json([
            'data' => $student
        ], 200);
    }

    // 更新
    public function update(Request $request, Student $student) {
        $student->fill($request->all());
        $student->save();
        return response()->json([
            'data' => $student
        ], 200);
    }

    // 削除
    public function delete(Student $student) {
        $student->delete();
        return response()->json([
            'message' => 'deleted successfully.'
        ], 200);
    }
}


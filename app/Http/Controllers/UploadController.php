<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserFile;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        // // ✅ التحقق من أن هناك ملف مرفوع
        // $request->validate([
        //     'file' => 'required|mimes:jpg,png,pdf|max:2048' // السماح فقط بـ JPG, PNG, PDF وحجم أقصى 2MB
        // ]);

        // ✅ الحصول على المستخدم الحالي
        $user = Auth::user();
        if (!$user) {
            return back()->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        // ✅ الحصول على الملف
        $file = $request->file('file');

        // ✅ تحديد اسم جديد لحفظ الملف
        $fileName = $file->getClientOriginalName();

        // ✅ نقل الملف إلى مجلد التخزين
        $file->move(public_path('uploads'), $fileName);

        // ✅ حفظ بيانات الملف في قاعدة البيانات
        UserFile::create([
            'user_id' => $user->id,
            'file_name' => $fileName,
        ]);

        // ✅ إرسال رسالة نجاح
        return back()->with('success', 'تم رفع الملف بنجاح!')->with('file', $fileName);
    }
}

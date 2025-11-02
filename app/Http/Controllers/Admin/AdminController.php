<?php

namespace App\Http\Controllers\Admin;
use Exception;
use App\Models\Status;
use App\Models\Kategori;
use App\Models\Priority;
use App\Models\Reminder;
use App\Models\AuditForm;
use App\Models\Subkategori;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Fileattachment;
use App\Models\Findlossdetail;
use Illuminate\Support\Carbon;
use App\Models\NotificationType;
use App\Mail\AuditNotificationMail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;


class AdminController extends Controller
{
        // Fungsi untuk menampilkan dasbor admin
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Fungsi untuk menampilkan halaman Findings
    public function findings(Request $request)
    {
        $exchangeRate = 15000;
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            if ($response->successful()) {
                $exchangeRate = $response->json('rates.IDR');
            }
        } catch (\Exception $e) {
            // ignore fallback
        }

        // Ambil semua opsi filter
        $statuses = \App\Models\Status::all();
        $priorities = \App\Models\Priority::all();
        $categories = \App\Models\Kategori::all();

        // Query dasar
        $query = \App\Models\AuditForm::with([
            'kategori',
            'priority',
            'status',
            'findlossdetails'
        ])->orderBy('id', 'asc');

        // 🔹 Filter Status
        if ($request->filled('status')) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('status', $request->status);
            });
        }

        // 🔹 Filter Priority
        if ($request->filled('priority')) {
            $query->whereHas('priority', function ($q) use ($request) {
                $q->where('name', $request->priority);
            });
        }

        // 🔹 Filter Kategori
        if ($request->filled('kategori')) {
            $query->whereHas('kategori', function ($q) use ($request) {
                $q->where('name', $request->kategori);
            });
        }

        // 🔹 Filter Due Date
        // 🔹 Filter Due Date Range
        if ($request->filled('due_start') && $request->filled('due_end')) {
            // Jika dua-duanya diisi: ambil antara dua tanggal itu
            $query->whereBetween('due_date', [$request->due_start, $request->due_end]);
        } elseif ($request->filled('due_start')) {
            // Jika hanya tanggal awal
            $query->whereDate('due_date', '>=', $request->due_start);
        } elseif ($request->filled('due_end')) {
            // Jika hanya tanggal akhir
            $query->whereDate('due_date', '<=', $request->due_end);
        }


         // 🔍 search judul_temuan
        if ($request->filled('search'))
            $query->where('judul_temuan', 'LIKE', '%' . $request->search . '%');

        $findings = $query->paginate(15)->withQueryString();

        return view('admin.findings', compact('findings', 'exchangeRate', 'statuses', 'priorities', 'categories'));
    }



    public function deleteFinding($id)
    {
        $finding = AuditForm::findOrFail($id);
        $finding->delete();

        return redirect()->route('admin.findings')->with('success', 'Finding deleted successfully.');
    }

    // In AdminController.php

    public function showAssessment($id)
    {
        // Hentikan eksekusi dan tampilkan ID yang diterima
        // dd('Controller berhasil diakses dengan ID: ' . $id);

        $finding = AuditForm::with([
            'kategori',
            'priority',
            'status',
            'reminder',
            'findlossdetails',
            'fileattachments'
        ])->findOrFail($id);

        $categories = Kategori::all();
        $subcategories = Subkategori::all();
        $priorities = Priority::all();

        // 🔹 Tambahkan ini
        $exchangeRate = 15000; // fallback
        try {
            $response = \Illuminate\Support\Facades\Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            if ($response->successful()) {
                $exchangeRate = $response->json('rates.IDR');
            }
        } catch (\Exception $e) {
            // fallback tetap
        }

        return view('admin.assessment', compact('finding', 'categories', 'subcategories', 'priorities', 'exchangeRate'));
    }

    public function autoSaveFinding(Request $request, $id)
    {
        $field = $request->input('field');
        $value = $request->input('value');

        $finding = AuditForm::with('reminder')->findOrFail($id);

        // 🧠 Daftar field yang boleh diubah
        $allowedFields = [
            'judul_temuan', 'temuan_audit', 'kategori_id',
            'subkategori_id', 'priority_id', 'rekomendasi_author',
            'catatan_tambahan', 'pic', 'start_date',
            'reminder_pt', 'reminder_nama', 'reminder_email'
        ];

        if (!in_array($field, $allowedFields)) {
            return response()->json(['error' => 'Invalid field'], 400);
        }

        // 🔹 Jika field milik reminder
        if (in_array($field, ['reminder_pt', 'reminder_nama', 'reminder_email'])) {
            $reminder = $finding->reminder;
            if ($reminder) {
                $column = str_replace('reminder_', '', $field);
                $reminder->$column = $value;
                $reminder->save();
            } else {
                return response()->json(['error' => 'Reminder not found'], 404);
            }
        } else {
            // 🔹 Field milik AuditForm
            $finding->$field = $value;
            $finding->save();
        }

        return response()->json(['success' => true]);
    }


    // Tampilkan form untuk membuat temuan audit baru
    public function createFinding()
    {
        // Ambil kurs real-time
        $exchangeRate = 15000; // fallback
        try {
            $response = Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            if ($response->successful()) {
                $exchangeRate = $response->json('rates.IDR');
            }
        } catch (\Exception $e) {
            // Tetap gunakan fallback jika API error
        }

        $categories = Kategori::all();
        $priorities = Priority::all();
        $subcategories = Subkategori::all();
        return view('admin.createAuditFindings', compact('categories', 'priorities', 'subcategories', 'exchangeRate'));
    }


    public function addFindLossDetail(Request $request, $auditFormId)
    {
        try {

            $exchangeRate = 15000;
            try {
                $response = \Illuminate\Support\Facades\Http::get('https://api.exchangerate-api.com/v4/latest/USD');
                if ($response->successful()) {
                    $exchangeRate = $response->json('rates.IDR');
                }
            } catch (\Exception $e) {
                // fallback
            }
            
            $detail = Findlossdetail::create([
                'item' => $request->item,
                'nilai' => $request->nilai,
                'audit_form_id' => $auditFormId,
            ]);

            $exchangeRate = 16253.62;

            $responseData = [
                'success' => true,
                'detail' => [
                    'id' => $detail->id,
                    'item' => $detail->item,
                    'nilai' => number_format($detail->nilai, 0, ',', '.'),
                    'usd' => number_format($detail->nilai / $exchangeRate, 2)
                ]
            ];

        Log::info('✅ Response data before return', $responseData);

        return response()->json($responseData, 200, [], JSON_UNESCAPED_UNICODE);

    } catch (\Throwable $e) {
        Log::error('❌ Error addFindLossDetail', ['msg' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }

    }


    public function deleteFindLossDetail($id)
    {
        try {
            $detail = \App\Models\Findlossdetail::findOrFail($id);
            $detail->delete();

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadAttachment(Request $request, $id)
    {
        $request->validate(['file' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120']); // <= 5 MB per file

        try {
            // Hitung total ukuran file yang sudah ada
            $currentTotalSize = Fileattachment::where('auditform_id', $id)->sum('file_size');
            $newFileSize = $request->file('file')->getSize();

            if (($currentTotalSize + $newFileSize) > (5 * 1024 * 1024)) {
                return response()->json([
                    'success' => false,
                    'error' => 'Total ukuran file untuk form ini melebihi 5 MB.'
                ], 400);
            }

            $file = $request->file('file');
            $path = $file->store('audit-attachments', 'public');

            $attachment = Fileattachment::create([
                'auditform_id' => $id,
                'file_path' => $path,
                'file_name' => $file->getClientOriginalName(),
                'file_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            return response()->json(['success' => true, 'attachment' => $attachment]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }


    public function deleteAttachment($id)
    {
        try {
            $file = Fileattachment::findOrFail($id);
            Storage::disk('public')->delete($file->file_path);
            $file->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function extendDueDate(Request $request, $id)
    {
        $request->validate([
            'due_date' => 'required|date|after_or_equal:today',
        ]);

        $form = AuditForm::with(['status', 'auditorUser', 'reminder'])->findOrFail($id);
        $oldDueDate = $form->due_date;
        $newDueDate = $request->due_date;

        // Jangan izinkan lebih kecil dari due_date lama
        if (Carbon::parse($newDueDate)->lt(Carbon::parse($oldDueDate))) {
            return response()->json(['success' => false, 'error' => 'Tanggal baru tidak boleh sebelum due date lama.'], 400);
        }

        // Update due date
        $form->due_date = $newDueDate;

        // Jika status sekarang Overdue → ubah jadi Open
        $openStatus = Status::where('status', 'Open')->first();
        if ($form->status->status === 'Overdue' && $openStatus) {
            $form->status_id = $openStatus->id;
        }

        $form->save();

        // Buat notifikasi
        Notification::create([
            'user_id' => $form->auditor,
            'auditform_id' => $form->id,
            'notificationstype_id' => 4, // tambahkan "Extend" di tabel notificationstype
            'title' => 'Due Date Diperpanjang',
            'message' => "Temuan '{$form->judul_temuan}' diperpanjang sampai {$newDueDate}.",
        ]);

        // Kirim email ke auditor & auditee
        $subject = '[Audit System] Due Date Diperpanjang';
        $title = 'Due Date Audit Diperpanjang';
        $message = "Temuan '{$form->judul_temuan}' telah diperpanjang sampai {$newDueDate}.";

        if ($form->auditorUser && $form->auditorUser->email) {
            Mail::to($form->auditorUser->email)->send(
                new AuditNotificationMail($subject, $title, $message, $form)
            );
        }

        if ($form->reminder && $form->reminder->email) {
            Mail::to($form->reminder->email)->send(
                new AuditNotificationMail($subject, $title, $message, $form)
            );
        }

        return response()->json(['success' => true]);
    }


    public function storeFinding(Request $request)
    {
        Log::info('Starting storeFinding...', ['input' => $request->all()]);

        // ================== BLOK DEBUGGING BARU ==================
        try {
            $validatedData = $request->validate([
                'title' => 'required|string|max:255',
                'department_pic' => 'nullable|string|max:255',
                'auditor' => 'required|exists:users,id',
                'description' => 'required|string',
                'category' => 'required|string',
                'priority' => 'required|string',
                'sub_category' => 'nullable|string',
                'finding_date' => 'required|date',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'client_pt' => 'required_if:category,Fin Loss|nullable|string|max:255',
                'client_name' => 'required_if:category,Fin Loss|nullable|string|max:255',
                'client_email' => 'required_if:category,Fin Loss|nullable|email',
                'reminder_name' => 'required_if:category,Non Compliance,Improvement|nullable|string|max:255',
                'reminder_email' => 'required_if:category,Non Compliance,Improvement|nullable|email',
                'internal_notes' => 'nullable|string',
                'auditee_notes' => 'nullable|string',
                'file_upload' => 'nullable|array',
                'file_upload.*' => 'file|mimes:png,jpg,jpeg,pdf|max:5120',
                'loss_description' => 'nullable|array',
                'loss_value' => 'nullable|array',
            ]);

            Log::info('VALIDATION PASSED.');

        } catch (ValidationException $e) {
            Log::error('VALIDATION FAILED.', [
                'errors' => $e->errors(), // Ini akan menunjukkan field mana yang gagal dan kenapa
                'input' => $request->all()
            ]);
            // Melempar kembali exception agar Laravel tetap redirect seperti biasa
            throw $e;
        }
        // ================== AKHIR BLOK DEBUGGING ==================

        try {

            // Validasi
            $request->validate([
                'title' => 'required|string|max:255',
                'department_pic' => 'nullable|string|max:255',
                'auditor' => 'required|exists:users,id',
                'description' => 'required|string',
                'category' => 'required|string',
                'priority' => 'required|string',
                'sub_category' => 'nullable|string',
                'finding_date' => 'required|date',
                'start_date' => 'required|date',
                'due_date' => 'required|date',
                'client_pt' => 'required_if:category,Fin Loss|string|max:255',
                'client_name' => 'required_if:category,Fin Loss|string|max:255',
                'client_email' => 'required_if:category,Fin Loss|email',
                'reminder_name' => 'required_if:category,Non Compliance,Improvement|nullable|string|max:255',
                'reminder_email' => 'required_if:category,Non Compliance,Improvement|nullable|email',
                'internal_notes' => 'nullable|string',
                'auditee_notes' => 'nullable|string',
                'file_upload' => 'nullable|array',
                'file_upload.*' => 'file|mimes:png,jpg,jpeg,pdf|max:5120',
                'loss_description' => 'nullable|array',
                'loss_value' => 'nullable|array',
            ]);

            $kategori = Kategori::where('name', $request->category)->first();
            $priority = Priority::where('name', $request->priority)->first();
            $subkategori = $request->category === 'Fin Loss' 
                ? Subkategori::where('name', $request->sub_category)->first()
                : null;

            // Validasi jika data tidak ditemukan
            if (!$kategori || !$priority) {
                return back()->withErrors(['error' => 'Kategori atau Priority tidak valid']);
            }
            // Simpan reminder/client
            $reminderId = null;
            if ($request->category === 'Fin Loss') {
                $reminder = Reminder::create([
                    'pt' => $request->client_pt,
                    'nama' => $request->client_name,
                    'email' => $request->client_email
                ]);
                $reminderId = $reminder->id;
            } else {
                $reminder = Reminder::create([
                    'pt' => null, // NULL untuk non-Fin Loss
                    'nama' => $request->reminder_name,
                    'email' => $request->reminder_email
                ]);
                $reminderId = $reminder->id;
            }


            // Simpan AuditForm
            $auditForm = AuditForm::create([
                'judul_temuan' => $request->title,
                'pic' => $request->department_pic,
                'auditor' => $request->auditor, // ID user
                'temuan_audit' => $request->description,
                'kategori_id' => $kategori->id,
                'priority_id' => $priority->id,
                'subkategori_id' => $subkategori?->id,
                'tanggal_temuan' => $request->finding_date,
                'start_date' => $request->start_date,
                'due_date' => $request->due_date,
                'reminder_id' => $reminderId,
                'rekomendasi_author' => $request->internal_notes,
                'catatan_tambahan' => $request->auditee_notes,
                'status_id' => 1 // Default 'Open'
                // 'attachment_path' => $attachmentPath
            ]);

            // Simpan Fin Loss Details (jika ada)
            if ($request->category === 'Fin Loss' && is_array($request->loss_description)) {
                foreach ($request->loss_description as $index => $description) {
                    $value = $request->loss_value[$index] ?? null;

                    // Simpan hanya jika deskripsi tidak kosong
                    if (!empty(trim($description)) && is_numeric($value)) {
                        Findlossdetail::create([
                            'item' => trim($description),
                            'nilai' => (float) $value,
                            'audit_form_id' => $auditForm->id
                        ]);
                    }
                }

                Log::info('Fin Loss Details to save:', [
                    'descriptions' => $request->loss_description,
                    'values' => $request->loss_value,
                    'audit_form_id' => $auditForm->id
                ]);

            }

            // Simpan lampiran (jika ada)
            // ================= FILE ATTACHMENT SECTION ======================
            if ($request->hasFile('file_upload')) {
                $files = $request->file('file_upload');
                
                foreach ($files as $file) {
                    $path = $file->store('audit-attachments', 'public');
                    
                    Fileattachment::create([
                        'auditform_id' => $auditForm->id,
                        'file_path' => $path,
                        'file_name' => $file->getClientOriginalName(),
                        'file_type' => $file->getMimeType(),
                        'file_size' => $file->getSize(),
                    ]);
                }

                Log::info('Files saved to database', [
                    'auditform_id' => $auditForm->id,
                    'file_count' => count($files),
                ]);
            } else {
                Log::warning('No file uploaded', ['input_files' => $request->allFiles()]);
            }


            Log::info('File uploaded?', [
                'has_file' => $request->hasFile('file_upload'),
                'files' => collect($request->file('file_upload'))->map(fn($f) => $f->getClientOriginalName())->toArray()
            ]);

            // Ambil data auditor & auditee
            $auditorUser = \App\Models\User::find($auditForm->auditor);
            $auditeeEmail = $reminder->email;

            // Buat notifikasi ke database (untuk web)
            Notification::create([
                'user_id' => $auditForm->auditor,
                'auditform_id' => $auditForm->id,
                'notificationstype_id' => 1, // Create
                'title' => 'Temuan Audit Baru Dibuat',
                'message' => "Pada tanggal '{$auditForm->tanggal_temuan}' Temuan '{$auditForm->judul_temuan}' telah dibuat dan ditugaskan kepada Anda.",
            ]);

            // Siapkan konten email
            $subject = '[Audit System] Temuan Audit Baru Dibuat';
            $title = 'Temuan Audit Baru Dibuat';
            $message = "Temuan '{$auditForm->judul_temuan}' telah dibuat.\n\nTanggal Temuan: {$auditForm->tanggal_temuan}\nDue Date: {$auditForm->due_date}";

            // Kirim email ke auditor
            if ($auditorUser && $auditorUser->email) {
                Mail::to($auditorUser->email)->send(
                    new AuditNotificationMail($subject, $title, $message, $auditForm)
                );
            }

            // Kirim email ke auditee (jika ada)
            if (!empty($auditeeEmail)) {
                Mail::to($auditeeEmail)->send(
                    new AuditNotificationMail($subject, $title, $message, $auditForm)
                );
            }

            return redirect()->route('admin.findings')->with('success', 'Audit finding created successfully!');
        
        } catch (Exception $e) {
        Log::error('StoreFinding Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        return back()->withErrors(['error' => 'Gagal menyimpan: ' . $e->getMessage()]);
        }
    }

    // Fungsi untuk menampilkan halaman Report
    public function report()
    {
        return view('admin.report');
    }

    // Fungsi untuk menampilkan halaman Manage Users
    public function manageUsers()
    {
        return view('admin.manage-users');
    }
}

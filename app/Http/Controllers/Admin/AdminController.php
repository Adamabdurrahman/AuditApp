<?php

namespace App\Http\Controllers\Admin;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\AuditForm;
use App\Models\Findlossdetail;
use App\Models\Kategori;
use App\Models\Priority;
use App\Models\Reminder;
use App\Models\Status;
use App\Models\Fileattachment;
use App\Models\Subkategori;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
        // Fungsi untuk menampilkan dasbor admin
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    // Fungsi untuk menampilkan halaman Findings
    public function findings()
    {
        return view('admin.findings');
    }

    // Tampilkan form untuk membuat temuan audit baru
    public function createFinding()
    {
        $categories = Kategori::all();
        $priorities = Priority::all();
        $subcategories = Subkategori::all();
        return view('admin.createAuditFindings', compact('categories', 'priorities', 'subcategories'));
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
                'client_pt' => 'required_if:category,Find Loss|nullable|string|max:255',
                'client_name' => 'required_if:category,Find Loss|nullable|string|max:255',
                'client_email' => 'required_if:category,Find Loss|nullable|email',
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
                'client_pt' => 'required_if:category,Find Loss|string|max:255',
                'client_name' => 'required_if:category,Find Loss|string|max:255',
                'client_email' => 'required_if:category,Find Loss|email',
                'reminder_name' => 'required_if:category,Non Compliance,Improvement|string|max:255',
                'reminder_email' => 'required_if:category,Non Compliance,Improvement|email',
                'internal_notes' => 'nullable|string',
                'auditee_notes' => 'nullable|string',
                'file_upload' => 'nullable|array',
                'file_upload.*' => 'file|mimes:png,jpg,jpeg,pdf|max:5120',
                'loss_description' => 'nullable|array',
                'loss_value' => 'nullable|array',
            ]);

            $kategori = Kategori::where('name', $request->category)->first();
            $priority = Priority::where('name', $request->priority)->first();
            $subkategori = $request->category === 'Find Loss' 
                ? Subkategori::where('name', $request->sub_category)->first()
                : null;

            // Validasi jika data tidak ditemukan
            if (!$kategori || !$priority) {
                return back()->withErrors(['error' => 'Kategori atau Priority tidak valid']);
            }
            // Simpan reminder/client
            $reminderId = null;
            if ($request->category === 'Find Loss') {
                $reminder = Reminder::create([
                    'pt' => $request->client_pt,
                    'nama' => $request->client_name,
                    'email' => $request->client_email
                ]);
                $reminderId = $reminder->id;
            } else {
                $reminder = Reminder::create([
                    'pt' => null, // NULL untuk non-Find Loss
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

            // Simpan Find Loss Details (jika ada)
            if ($request->category === 'Find Loss' && $request->loss_description) {
                foreach ($request->loss_description as $index => $item) {
                    if (!empty($item) && isset($request->loss_value[$index])) {
                        Findlossdetail::create([
                            'item' => $item,
                            'nilai' => $request->loss_value[$index],
                            'audit_form_id' => $auditForm->id
                        ]);
                    }
                }
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

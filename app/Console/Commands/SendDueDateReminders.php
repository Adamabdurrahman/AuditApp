<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuditForm;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\AuditNotificationMail;

class SendDueDateReminders extends Command
{
    protected $signature = 'reminders:send';
    protected $description = 'Kirim notifikasi reminder 7 hari sebelum due date dan notifikasi overdue setelah lewat.';

    public function handle()
    {
        $today = Carbon::today();
        $this->info("Menjalankan pengecekan reminder & overdue per {$today->toDateString()}...");

        // Ambil semua temuan status 'Open' beserta relasinya
        $forms = AuditForm::with(['status', 'auditorUser', 'reminder'])
            ->whereHas('status', fn($q) => $q->where('status', 'Open'))
            ->get();

        foreach ($forms as $form) {
            $due = Carbon::parse($form->due_date);
            $daysLeft = $today->diffInDays($due, false);

            /**
             * ==============
             * 1️⃣ Kirim Reminder (H-7)
             * ==============
             */
            if ($daysLeft <= 7 && $daysLeft >= 0) {
                $this->handleNotificationAndEmail($form, 2, 'Reminder');
            }

            /**
             * ==============
             * 2️⃣ Kirim Overdue (Lewat due date)
             * ==============
             */
            if ($due->isPast() && $form->status->status === 'Open') {
                $this->handleNotificationAndEmail($form, 3, 'Overdue');
            }
        }

        $this->info('✅ Semua reminder & overdue selesai diproses.');
    }

    /**
     * Handle Notifikasi & Email (terpadu)
     */
    protected function handleNotificationAndEmail($form, $type, $mode)
    {
        $today = Carbon::today();

        // Cegah duplikasi notifikasi di hari yang sama
        $alreadySent = Notification::where('auditform_id', $form->id)
            ->where('notificationstype_id', $type)
            ->whereDate('created_at', $today)
            ->exists();

        if ($alreadySent) {
            $this->line("⏩ Notifikasi {$mode} untuk temuan ID {$form->id} sudah pernah dikirim hari ini, dilewati.");
            return;
        }

        // Tentukan isi notifikasi
        if ($mode === 'Reminder') {
            $title = 'Pengingat: Due Date Semakin Dekat';
            $message = "Temuan '{$form->judul_temuan}' akan jatuh tempo pada {$form->due_date}. Mohon segera ditindaklanjuti.";
            $subject = '[Audit System] Reminder: Due Date Semakin Dekat';
        } else {
            $title = '⚠️ Temuan Sudah Melewati Batas Waktu';
            $message = "Temuan '{$form->judul_temuan}' telah melewati batas waktu penyelesaian ({$form->due_date}). Mohon segera tindak lanjuti.";
            $subject = '[Audit System] Temuan Audit Overdue!';
        }

        // Buat notifikasi di database
        Notification::create([
            'user_id' => $form->auditor,
            'auditform_id' => $form->id,
            'notificationstype_id' => $type,
            'title' => $title,
            'message' => $message,
        ]);

        $this->info("📬 {$mode} dikirim ke DB untuk temuan ID {$form->id}");

        // Kirim email ke auditor
        if ($form->auditorUser && $form->auditorUser->email) {
            Mail::to($form->auditorUser->email)->send(
                new AuditNotificationMail($subject, $title, $message, $form)
            );
            $this->line("   ✉️ Email {$mode} terkirim ke Auditor: {$form->auditorUser->email}");
        }

        // Kirim email ke auditee (reminder)
        if ($form->reminder && $form->reminder->email) {
            Mail::to($form->reminder->email)->send(
                new AuditNotificationMail($subject, $title, $message, $form)
            );
            $this->line("   ✉️ Email {$mode} terkirim ke Auditee: {$form->reminder->email}");
        }
    }
}

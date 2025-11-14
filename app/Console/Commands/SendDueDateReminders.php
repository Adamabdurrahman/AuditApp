<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AuditForm;
use App\Models\Notification;
use App\Models\NotificationType;
use App\Models\Status;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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
        $forms = AuditForm::with(['status', 'auditorUser', 'reminder', 'priority'])
            ->whereHas('status', fn($q) => $q->where('status', 'Open'))
            ->get();

        $openStatus = Status::where('status', 'Open')->first();

        $overdueStatus = Status::where('status', 'Overdue')->first();    

        $priorityReminderTypeId = $this->getOrCreateNotificationTypeId('Priority Reminder');

        foreach ($forms as $form) {

            if ($form->status && strtolower($form->status->status) === 'closed') {
                $this->line("⏹️ Form ID {$form->id} sudah Closed, dilewati.");
                continue;
            }

            if ($priorityReminderTypeId) {
                $this->handlePriorityIntervalReminder($form, $priorityReminderTypeId, $today);
            }
            
            $due = Carbon::parse($form->due_date);
            $daysLeft = $today->diffInDays($due, false);

            if ($daysLeft >= 0 && $form->status->status === 'Overdue') {
                if ($openStatus) {
                    $form->status_id = $openStatus->id;
                    $form->save();
                    $this->line("   🔄 Status form ID {$form->id} dikembalikan ke Open (extend aktif)");
                }
            }
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

                // 🔹 Update status ke Overdue
                if ($overdueStatus) {
                    $form->status_id = $overdueStatus->id;
                    $form->save();
                    $this->line("   ⚙️ Status form ID {$form->id} diubah menjadi Overdue");
                }

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

        if ($form->status && strtolower($form->status->status) === 'closed') {
            $this->line("⛔ Skip {$mode}: Form ID {$form->id} sudah Closed.");
            return;
        }

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

    protected function handlePriorityIntervalReminder($form, int $typeId, Carbon $today): void
    {
        $priorityName = strtolower($form->priority->name ?? '');

        $intervalMonths = match ($priorityName) {
            'high' => 1,
            'medium' => 2,
            'low' => 3,
            default => null,
        };

        if (! $intervalMonths) {
            return;
        }

        $startDate = $form->start_date
            ? Carbon::parse($form->start_date)
            : ($form->tanggal_temuan ? Carbon::parse($form->tanggal_temuan) : null);

        if (! $startDate) {
            return;
        }

        $alreadySentToday = Notification::where('auditform_id', $form->id)
            ->where('notificationstype_id', $typeId)
            ->whereDate('created_at', $today)
            ->exists();

        if ($alreadySentToday) {
            return;
        }

        $lastReminder = Notification::where('auditform_id', $form->id)
            ->where('notificationstype_id', $typeId)
            ->latest('created_at')
            ->first();

        $nextTriggerDate = $lastReminder
            ? Carbon::parse($lastReminder->created_at)->startOfDay()->addMonthsNoOverflow($intervalMonths)
            : $startDate->copy()->startOfDay()->addMonthsNoOverflow($intervalMonths);

        if ($today->lt($nextTriggerDate)) {
            return;
        }

        $priorityLabel = ucfirst($priorityName);
        $message = "Temuan '{$form->judul_temuan}' dengan prioritas {$priorityLabel} membutuhkan tindak lanjut berkala. Pengingat ini dijadwalkan tiap {$intervalMonths} bulan sejak {$startDate->toDateString()}";
        $title = "Pengingat Berkala Prioritas {$priorityLabel}";
        $subject = "[Audit System] Pengingat Berkala - Prioritas {$priorityLabel}";

        Notification::create([
            'user_id' => $form->auditor,
            'auditform_id' => $form->id,
            'notificationstype_id' => $typeId,
            'title' => $title,
            'message' => $message,
        ]);

        $recipients = $this->collectEmailRecipients($form);

        if ($recipients->isNotEmpty()) {
            $this->sendMailToRecipients($recipients, new AuditNotificationMail($subject, $title, $message, $form));
            $this->line("   ✉️ Email Reminder Berkala dikirim ke " . $recipients->implode(', '));
        }
    }

    protected function collectEmailRecipients($form): Collection
    {
        return collect([
            optional($form->auditorUser)->email,
            optional($form->reminder)->email,
        ])->merge(config('mail.extra_recipients', []))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values();
    }

    protected function sendMailToRecipients(Collection $emails, AuditNotificationMail $mailable): void
    {
        if ($emails->isEmpty()) {
            return;
        }

        $primary = $emails->shift();
        $mailer = Mail::to($primary);

        if ($emails->isNotEmpty()) {
            $mailer->bcc($emails->all());
        }

        $mailer->send($mailable);
    }

    protected function getOrCreateNotificationTypeId(string $name): ?int
    {
        $type = NotificationType::firstOrCreate(['name' => $name]);

        return $type->id ?? null;
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AuditNotificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $title;
    public $messageContent;
    public $auditForm;

    /**
     * Create a new message instance.
     */
    public function __construct($subjectLine, $title, $messageContent, $auditForm)
    {
        $this->subjectLine = $subjectLine;
        $this->title = $title;
        $this->messageContent = $messageContent;
        $this->auditForm = $auditForm;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->view('emails.audit_notification')
                    ->with([
                        'title' => $this->title,
                        'messageContent' => $this->messageContent,
                        'auditForm' => $this->auditForm,
                    ]);
    }
}

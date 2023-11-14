<?php

namespace App\Notifications\Api\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class activeNotification extends Notification
{
    use Queueable;
    private $serviceOrproduct;
    private $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($serviceOrproduct,$message)
    {
        $this->serviceOrproduct=$serviceOrproduct;
        $this->message=$message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->markdown('emails.serviceOrproduct',['message'=>$this->message,'serviceOrproduct'=>$this->serviceOrproduct]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
          'serviceOrproduct'=>$this->serviceOrproduct->name,
          'message'=>$this->message,
        ];
    }
}

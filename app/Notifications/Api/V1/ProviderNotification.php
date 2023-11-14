<?php

namespace App\Notifications\Api\V1;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProviderNotification extends Notification
{
    use Queueable;
    private $provider;
    private $message;

    /**
     * Create a new notification instance.
     */
    public function __construct($provider,$message)
    {
        $this->provider=$provider;
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
        return (new MailMessage)->markdown('emails.provider',['message'=>$this->message,'provider'=>$this->provider]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
          'provider'=>$this->provider->bussiness_name,
          'message'=>$this->message,
        ];
    }}

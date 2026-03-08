<?php

namespace App\Mail;

use App\Models\LiveClass;
use App\Models\Teacher;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;

class LiveClassCreatedNotification extends Mailable
{
    use Queueable;

    public $liveClass;
    public $teacher;

    public function __construct(LiveClass $liveClass, Teacher $teacher)
    {
        $this->liveClass = $liveClass;
        $this->teacher = $teacher;
    }

    public function envelope(): array
    {
        return [
            'subject' => 'New Live Class Assignment - ' . $this->liveClass->title,
        ];
    }

    public function content(): array
    {
        return [
            'html' => $this->buildHtmlContent(),
        ];
    }

    public function build()
    {
        return $this->html($this->buildHtmlContent());
    }

    private function buildHtmlContent()
    {
        $startTime = $this->liveClass->start_time ? $this->liveClass->start_time->format('M d, Y \a\t H:i') : 'Not set';
        $endTime = $this->liveClass->end_time ? $this->liveClass->end_time->format('M d, Y \a\t H:i') : 'Not set';

        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Live Class Assignment Notification</title>
            <style>
                body {
                    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
                    line-height: 1.6;
                    color: #333;
                    background-color: #f8f9fa;
                    margin: 0;
                    padding: 20px;
                }
                .container {
                    max-width: 600px;
                    margin: 0 auto;
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    overflow: hidden;
                }
                .header {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px;
                    text-align: center;
                }
                .header h1 {
                    margin: 0;
                    font-size: 24px;
                    font-weight: 600;
                }
                .content {
                    padding: 30px;
                }
                .info-grid {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 20px;
                    margin-bottom: 20px;
                }
                .info-item {
                    background: #f8f9fa;
                    padding: 15px;
                    border-radius: 6px;
                    border-left: 4px solid #667eea;
                }
                .info-label {
                    font-weight: 600;
                    color: #667eea;
                    margin-bottom: 5px;
                    font-size: 14px;
                }
                .info-value {
                    color: #333;
                    font-size: 16px;
                }
                .details {
                    background: #f1f5f9;
                    padding: 20px;
                    border-radius: 6px;
                    margin-top: 20px;
                }
                .footer {
                    text-align: center;
                    padding: 20px;
                    color: #666;
                    font-size: 12px;
                }
                .btn {
                    display: inline-block;
                    background: #667eea;
                    color: white;
                    padding: 12px 24px;
                    text-decoration: none;
                    border-radius: 6px;
                    font-weight: 600;
                    margin-top: 15px;
                }
                @media (max-width: 600px) {
                    .container {
                        margin: 10px;
                        border-radius: 0;
                    }
                    .info-grid {
                        grid-template-columns: 1fr;
                    }
                }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🎥 Live Class Assignment</h1>
                    <p>You have been assigned to conduct a new live class</p>
                </div>
                
                <div class="content">
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">📅 Class Title</div>
                            <div class="info-value">' . htmlspecialchars($this->liveClass->title) . '</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">⏰ Start Time</div>
                            <div class="info-value">' . $startTime . '</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">⏱️ End Time</div>
                            <div class="info-value">' . $endTime . '</div>
                        </div>
                    </div>
                    
                    <div class="details">
                        <h3 style="color: #667eea; margin-bottom: 15px;">📝 Description</h3>
                        <p style="margin: 0; line-height: 1.6;">' . 
                            ($this->liveClass->description ? htmlspecialchars($this->liveClass->description) : 'No description provided') . 
                        '</p>
                        
                        <h3 style="color: #667eea; margin-top: 20px; margin-bottom: 15px;">🔗 Meeting Details</h3>
                        <div style="background: white; padding: 15px; border-radius: 6px; border: 1px solid #e2e8f0;">
                            <p style="margin: 0 0 10px 0;"><strong>Meeting ID:</strong> ' . htmlspecialchars($this->liveClass->meeting_id) . '</p>
                            <p style="margin: 0 0 10px 0;"><strong>Password:</strong> ' . htmlspecialchars($this->liveClass->password) . '</p>
                            <p style="margin: 0;"><strong>Access Type:</strong> ' . ucfirst(str_replace('_', ' ', $this->liveClass->access_type)) . '</p>
                        </div>
                    </div>
                </div>
                
                <div class="footer">
                    <p>This is an automated notification from the ESA Learning Portal.</p>
                    <p>© 2026 ESA Learning Portal. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>';
    }
}

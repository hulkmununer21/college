<?php
/**
 * Email Helper Class
 * 
 * Handles email sending functionality
 * Note: In production, integrate with PHPMailer or a mail service like SendGrid
 * 
 * @author Senior PHP Developer
 * @version 1.0.0
 */

class EmailHelper
{
    private string $fromEmail;
    private string $fromName;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->fromEmail = 'noreply@' . str_replace(['http://', 'https://'], '', BASE_URL);
        $this->fromName = APP_NAME;
    }

    /**
     * Send verification email
     * 
     * @param string $to Recipient email
     * @param string $name Recipient name
     * @param string $token Verification token
     * @return bool
     */
    public function sendVerificationEmail(string $to, string $name, string $token): bool
    {
        $subject = 'Verify Your Email - ' . APP_NAME;
        $verifyUrl = BASE_URL . '/auth/verify-email/' . $token;

        $message = $this->getEmailTemplate([
            'name' => $name,
            'title' => 'Verify Your Email Address',
            'body' => 'Thank you for registering with ' . APP_NAME . '. Please click the button below to verify your email address.',
            'button_text' => 'Verify Email',
            'button_url' => $verifyUrl,
            'footer' => 'If you did not create an account, please ignore this email.'
        ]);

        return $this->send($to, $subject, $message);
    }

    /**
     * Send password reset email
     * 
     * @param string $to Recipient email
     * @param string $name Recipient name
     * @param string $token Reset token
     * @return bool
     */
    public function sendPasswordResetEmail(string $to, string $name, string $token): bool
    {
        $subject = 'Password Reset Request - ' . APP_NAME;
        $resetUrl = BASE_URL . '/auth/reset-password/' . $token;

        $message = $this->getEmailTemplate([
            'name' => $name,
            'title' => 'Reset Your Password',
            'body' => 'We received a request to reset your password. Click the button below to reset it. This link will expire in 1 hour.',
            'button_text' => 'Reset Password',
            'button_url' => $resetUrl,
            'footer' => 'If you did not request a password reset, please ignore this email or contact support if you have concerns.'
        ]);

        return $this->send($to, $subject, $message);
    }

    /**
     * Send welcome email
     * 
     * @param string $to Recipient email
     * @param string $name Recipient name
     * @return bool
     */
    public function sendWelcomeEmail(string $to, string $name): bool
    {
        $subject = 'Welcome to ' . APP_NAME;
        $loginUrl = BASE_URL . '/auth/login';

        $message = $this->getEmailTemplate([
            'name' => $name,
            'title' => 'Welcome to ' . APP_NAME,
            'body' => 'Your account has been successfully created. You can now login and start using our platform.',
            'button_text' => 'Login Now',
            'button_url' => $loginUrl,
            'footer' => 'If you have any questions, please contact our support team.'
        ]);

        return $this->send($to, $subject, $message);
    }

    /**
     * Send generic email
     * 
     * @param string $to Recipient email
     * @param string $subject Email subject
     * @param string $message Email message (HTML)
     * @return bool
     */
    public function send(string $to, string $subject, string $message): bool
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-type: text/html; charset=UTF-8',
            'From: ' . $this->fromName . ' <' . $this->fromEmail . '>',
            'Reply-To: ' . $this->fromEmail,
            'X-Mailer: PHP/' . phpversion()
        ];

        // In development, log emails instead of sending
        if (ini_get('display_errors') == 1) {
            $this->logEmail($to, $subject, $message);
            return true;
        }

        // Send email using PHP mail function
        // In production, replace with PHPMailer or mail service
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }

    /**
     * Get email template
     * 
     * @param array $data Template data
     * @return string HTML email
     */
    private function getEmailTemplate(array $data): string
    {
        $name = $data['name'] ?? 'User';
        $title = $data['title'] ?? '';
        $body = $data['body'] ?? '';
        $buttonText = $data['button_text'] ?? '';
        $buttonUrl = $data['button_url'] ?? '';
        $footer = $data['footer'] ?? '';

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        }
        .content {
            padding: 30px;
        }
        .content h2 {
            color: #667eea;
            margin-top: 0;
        }
        .button {
            display: inline-block;
            padding: 15px 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white !important;
            text-decoration: none;
            border-radius: 50px;
            margin: 20px 0;
            font-weight: bold;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{$this->fromName}</h1>
        </div>
        <div class="content">
            <h2>Hello, {$name}!</h2>
            <p>{$body}</p>
            {$this->renderButton($buttonText, $buttonUrl)}
        </div>
        <div class="footer">
            <p>{$footer}</p>
            <p>&copy; {$this->getCurrentYear()} {$this->fromName}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Render button in email
     * 
     * @param string $text Button text
     * @param string $url Button URL
     * @return string HTML button
     */
    private function renderButton(string $text, string $url): string
    {
        if (empty($text) || empty($url)) {
            return '';
        }

        return '<div style="text-align: center;"><a href="' . $url . '" class="button">' . $text . '</a></div>';
    }

    /**
     * Get current year
     * 
     * @return string
     */
    private function getCurrentYear(): string
    {
        return date('Y');
    }

    /**
     * Log email (for development)
     * 
     * @param string $to
     * @param string $subject
     * @param string $message
     * @return void
     */
    private function logEmail(string $to, string $subject, string $message): void
    {
        $logDir = ROOT_PATH . '/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/emails.log';
        $logEntry = sprintf(
            "[%s] To: %s | Subject: %s\n%s\n\n",
            date('Y-m-d H:i:s'),
            $to,
            $subject,
            strip_tags($message)
        );

        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}

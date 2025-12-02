# Contact Form Email Setup Guide

## Overview
The contact form has been implemented using PHPMailer to send emails. Follow the steps below to configure it properly.

## Configuration Steps

### Option 1: Gmail SMTP (Recommended for testing)

1. **Enable 2-Step Verification on your Gmail account:**
   - Go to myaccount.google.com → Security
   - Enable 2-Step Verification

2. **Generate an App Password:**
   - Go to myaccount.google.com → Security
   - Under "App passwords", select "Mail" and "Windows Computer"
   - Copy the 16-character password provided

3. **Update `forms/contact.php`:**
   - Line 28: Replace `your-email@gmail.com` with your Gmail address
   - Line 29: Replace `your-app-password` with the 16-character App Password

4. **Test the form:**
   - Visit http://localhost/PHPCLASS/MyProject/contact.html
   - Fill in the form and click "Send Message"
   - Check your email for the test message

### Option 2: Use PHP's mail() function (Local SMTP)

If you have PHP's mail() function configured:

Replace the SMTP configuration block (lines 21-26) with:

```php
// Use PHP's built-in mail function (requires local SMTP setup)
$mail->isMail();
// No additional SMTP configuration needed
```

### Option 3: Custom SMTP Server

If you have your own SMTP server:

Update lines 22-26 with your server details:
```php
$mail->Host = 'your.smtp.server.com';
$mail->Port = 587; // or 465 for SSL
$mail->Username = 'your-username';
$mail->Password = 'your-password';
```

## File Structure
- **`forms/contact.php`** - Email handler using PHPMailer
- **`contact.html`** - Contact form page
- **`assets/vendor/php-email-form/validate.js`** - Form validation and submission handler

## Form Fields
- **Name** - Sender's name
- **Email** - Sender's email (will be used for reply-to)
- **Subject** - Email subject
- **Message** - Email body

## Email Recipients
By default, all contact form submissions are sent to: **stellaukas@gmail.com**

To change this, edit line 14 in `forms/contact.php`:
```php
$recipient_email = 'your-email@example.com';
```

## Features
✓ HTML and plain text email formats
✓ Client-side form validation
✓ Server-side validation
✓ Error handling and user feedback
✓ Reply-to address automatically set from sender

## Troubleshooting

### Error: "Message could not be sent"
- Check if SMTP credentials are correct
- Verify Gmail app password (not your main password)
- Ensure 2-Step Verification is enabled on Gmail

### Email not received
- Check spam/promotions folder
- Verify recipient email address in `forms/contact.php`
- Test with a simpler message first

### Enable Debug Mode
To see detailed SMTP logs, change line 22:
```php
$mail->SMTPDebug = SMTP::DEBUG_SERVER; // Shows all SMTP communication
```

## Security Notes
- Always use HTTPS in production
- Never hardcode credentials; use environment variables in production
- Validate all user input before sending
- Consider rate limiting to prevent spam

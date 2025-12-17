# 📧 Gmail Email Setup Guide

## 🚀 Quick Setup

### 1. **Enable 2-Factor Authentication on Gmail**
1. Go to [Google Account Settings](https://myaccount.google.com/)
2. Click **Security** → **2-Step Verification**
3. Follow the setup process

### 2. **Generate App Password**
1. Go to [App Passwords](https://myaccount.google.com/apppasswords)
2. Select **Mail** and **Other (Custom name)**
3. Enter "Symfony App" as the name
4. **Copy the 16-character password** (e.g., `abcd efgh ijkl mnop`)

### 3. **Update .env Configuration**

Replace these placeholders in your `.env` file:

```env
# Replace with your actual Gmail email
MAILER_DSN="smtp://YOUR_GMAIL_EMAIL:YOUR_APP_PASSWORD@smtp.gmail.com:587?encryption=tls&auth_mode=login"

# Replace with your Gmail and app name
GMAIL_SENDER_EMAIL="your-email@gmail.com"
GMAIL_SENDER_NAME="Your App Name"
```

**Example:**
```env
MAILER_DSN="smtp://john.doe@gmail.com:abcdefghijklmnop@smtp.gmail.com:587?encryption=tls&auth_mode=login"
GMAIL_SENDER_EMAIL="john.doe@gmail.com"
GMAIL_SENDER_NAME="My Message System"
```

### 4. **Test the Configuration**

1. Clear Symfony cache: `php bin/console cache:clear`
2. Go to your messages page
3. Click the 📧 email icon next to any message
4. Check for success/error messages

## 🔧 Troubleshooting

### **"Authentication failed" Error**
- ✅ Verify 2FA is enabled on Gmail
- ✅ Use App Password, not regular password
- ✅ Remove spaces from App Password
- ✅ Check email address is correct

### **"Connection refused" Error**
- ✅ Check internet connection
- ✅ Verify SMTP settings (smtp.gmail.com:587)
- ✅ Ensure TLS encryption is enabled

### **"Invalid credentials" Error**
- ✅ Regenerate App Password
- ✅ Update .env file with new password
- ✅ Clear Symfony cache

## 📋 Configuration Details

### **MAILER_DSN Format:**
```
smtp://EMAIL:APP_PASSWORD@smtp.gmail.com:587?encryption=tls&auth_mode=login
```

### **Components:**
- **EMAIL**: Your Gmail address
- **APP_PASSWORD**: 16-character app password (no spaces)
- **smtp.gmail.com**: Gmail SMTP server
- **587**: SMTP port for TLS
- **encryption=tls**: Use TLS encryption
- **auth_mode=login**: Use LOGIN authentication

## 🎯 How It Works

1. **User clicks email icon** → Form submits to `/messages/{id}/send-email`
2. **Controller validates** → Checks permissions and CSRF token
3. **EmailService sends** → Uses Gmail SMTP to send email
4. **User sees feedback** → Success or error message displayed

## 🔒 Security Notes

- ✅ App passwords are safer than regular passwords
- ✅ CSRF tokens prevent unauthorized email sending
- ✅ Only message participants can send emails
- ✅ All email attempts are logged for debugging

## 📧 Email Template

The system uses `templates/emails/message.html.twig` for email formatting:
- Professional HTML design
- Sender and recipient information
- Message subject and content
- Timestamp and branding
- Mobile-responsive layout

## 🚨 Important Notes

1. **Never commit real credentials** to version control
2. **Use environment variables** for sensitive data
3. **Test with a real Gmail account** before production
4. **Monitor Gmail sending limits** (500 emails/day for free accounts)
5. **Consider using Gmail API** for higher volume applications

## 📞 Support

If you encounter issues:
1. Check the browser console for JavaScript errors
2. Check Symfony logs: `var/log/dev.log`
3. Verify Gmail App Password is working
4. Test with a simple email client first
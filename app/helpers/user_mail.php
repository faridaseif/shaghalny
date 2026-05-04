<?php
class UserMail {

    public static function sendVerificationEmail($email, $code) {
        $verifyUrl = "http://localhost/shaghalny/verify-email.php?code=" . urlencode($code);

        $subject = "Verify your account - Shaghalny";

        $message = "
            <html>
            <body>
                <h2>Welcome to Shaghalny 👋</h2>
                <p>Please verify your email by clicking the link below:</p>
                <p>
                    <a href='$verifyUrl'
                       style='display:inline-block;padding:10px 15px;
                              background:#2D6BE0;color:#fff;
                              text-decoration:none;border-radius:5px;'>
                        Verify Email
                    </a>
                </p>
                <p>If you didn’t create this account, ignore this email.</p>
            </body>
            </html>
        ";

        // HTML email headers (CORRECT)
        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Shaghalny <no-reply@shaghalny.com>\r\n";

        // Attempt send (will fail silently on localhost — expected)
        @mail($email, $subject, $message, $headers);

        // Localhost testing fallback (THIS IS YOUR REAL TEST)
        error_log("[EMAIL MOCK][VERIFY] $verifyUrl");

        return true;
    }

    public static function sendWelcomeEmail($email, $name) {
        $subject = "Welcome to Shaghalny!";

        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');

        $message = "
            <html>
            <body>
                <h2>Hi $safeName 👋</h2>
                <p>Thanks for completing your profile.</p>
                <p>You are now ready to apply for jobs on Shaghalny 🚀</p>
            </body>
            </html>
        ";

        $headers  = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $headers .= "From: Shaghalny <no-reply@shaghalny.com>\r\n";

        @mail($email, $subject, $message, $headers);

        error_log("[EMAIL MOCK][WELCOME] $email");

        return true;
    }
}

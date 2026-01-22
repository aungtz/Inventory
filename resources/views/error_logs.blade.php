<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Error Notification</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .error-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 800px;
            overflow: hidden;
            animation: fadeIn 0.5s ease-out;
        }
        
        .error-header {
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
        }
        
        .error-header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .error-header p {
            font-size: 1.1rem;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }
        
        .error-content {
            padding: 40px;
        }
        
        .apology-card {
            background: #fff9f9;
            border: 2px solid #ffebee;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .apology-card i {
            font-size: 3rem;
            color: #ff6b6b;
            margin-bottom: 20px;
        }
        
        .apology-card h2 {
            color: #d32f2f;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        
        .apology-card p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.7;
        }
        
        .error-details {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .error-details h3 {
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.4rem;
        }
        
        .detail-item {
            display: flex;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .detail-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .detail-label {
            font-weight: 600;
            color: #555;
            width: 150px;
            flex-shrink: 0;
        }
        
        .detail-value {
            color: #333;
            flex: 1;
        }
        
        .error-message-box {
            background: #ffebee;
            border-left: 4px solid #ff6b6b;
            padding: 20px;
            border-radius: 8px;
            margin-top: 15px;
            font-family: monospace;
            font-size: 0.95rem;
            color: #d32f2f;
        }
        
        .contact-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
        }
        
        .contact-section h3 {
            color: #333;
            margin-bottom: 25px;
            font-size: 1.5rem;
        }
        
        .contact-methods {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .contact-method {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s;
        }
        
        .contact-method:hover {
            transform: translateY(-5px);
        }
        
        .contact-method i {
            font-size: 2rem;
            color: #667eea;
            margin-bottom: 15px;
        }
        
        .contact-method h4 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .contact-method p {
            color: #666;
            font-size: 0.95rem;
        }
        
        .action-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
        
        .btn {
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a6fd8;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #f1f3f9;
            color: #667eea;
        }
        
        .btn-secondary:hover {
            background: #e4e7f4;
            transform: translateY(-2px);
        }
        
        .footer {
            text-align: center;
            padding: 25px;
            color: #777;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            margin-top: 20px;
        }
        
        .ticket-id {
            background: #667eea;
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
            margin-top: 10px;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @media (max-width: 768px) {
            .error-header {
                padding: 30px 20px;
            }
            
            .error-header h1 {
                font-size: 2rem;
                flex-direction: column;
                gap: 10px;
            }
            
            .error-content {
                padding: 25px 20px;
            }
            
            .detail-item {
                flex-direction: column;
                gap: 5px;
            }
            
            .detail-label {
                width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .contact-methods {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <h1>
                <i class="fas fa-exclamation-triangle"></i>
                System Error Detected
            </h1>
            <p>We've encountered an issue and our technical team has been notified.</p>
        </div>
        
        <div class="error-content">
            <div class="apology-card">
                <i class="fas fa-handshake"></i>
                <h2>We're Sorry for the Inconvenience</h2>
                <p>An unexpected error occurred while processing your request. Our development team has been automatically notified and is working to resolve this issue as quickly as possible.</p>
                <div class="ticket-id">
                    Error Reference: ERR-{{ $latestError->ID ?? 'CURRENT' }}
                </div>
            </div>
            
            <div class="error-details">
                <h3>
                    <i class="fas fa-info-circle"></i>
                    Error Details
                </h3>
                
                <div class="detail-item">
                    <div class="detail-label">Error ID:</div>
                    <div class="detail-value">
                        <strong>ERR-{{ $latestError->ID ?? 'N/A' }}</strong>
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Affected Page:</div>
                    <div class="detail-value">
                        <i class="fas fa-file-alt"></i>
                        {{ $currentError['path'] ?? ($latestError->FormName ?? 'Unknown') }}
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Reported By:</div>
                    <div class="detail-value">
                        <i class="fas fa-user"></i>
                        {{ $currentError['user'] ?? ($latestError->UserName ?? 'Guest') }}
                    </div>
                </div>
                
                <div class="detail-item">
                    <div class="detail-label">Time Occurred:</div>
                    <div class="detail-value">
                        <i class="far fa-clock"></i>
                        {{ $currentError['time'] ?? ($latestError->InsertedDate ?? now()->format('Y-m-d H:i:s')) }}
                    </div>
                </div>
                
                @php
                    // Convert technical error to customer-friendly message
                    $errorMessage = $currentError['message'] ?? ($latestError->ErrorMessage ?? 'An unexpected error occurred');
                    
                    // Simple conversion logic
                    $friendlyMessage = $errorMessage;
                    if (strpos($errorMessage, 'SQL') !== false) {
                        $friendlyMessage = 'Database connection issue';
                    } elseif (strpos($errorMessage, '404') !== false) {
                        $friendlyMessage = 'Requested page not found';
                    } elseif (strpos($errorMessage, '500') !== false) {
                        $friendlyMessage = 'Internal server error';
                    } elseif (strpos($errorMessage, 'timeout') !== false) {
                        $friendlyMessage = 'Request timeout';
                    }
                @endphp
                
                <div class="detail-item">
                    <div class="detail-label">Issue Description:</div>
                    <div class="detail-value">{{ $friendlyMessage }}</div>
                </div>
                
                @if($errorMessage != $friendlyMessage)
                <div class="error-message-box">
                    <strong>Technical Details (for support):</strong><br>
                    {{ substr($errorMessage, 0, 200) }}...
                </div>
                @endif
            </div>
            
            <div class="contact-section">
                <h3>Need Immediate Help?</h3>
                <p>If you need immediate assistance, please contact our support team:</p>
                
                <div class="contact-methods">
                    <div class="contact-method">
                        <i class="fas fa-phone-alt"></i>
                        <h4>Call Us</h4>
                        <p>093343132323532</p>
                        <!-- <small>Mon-Fri, 9AM-6PM EST</small> -->
                    </div>
                    
                    <div class="contact-method">
                        <i class="fas fa-envelope"></i>
                        <h4>Email Us</h4>
                        <p>ckm@gmail.com</p>
                        <small>Response within 1 hour</small>
                    </div>
                    
                    <!-- <div class="contact-method">
                        <i class="fas fa-comment-dots"></i>
                        <h4>Live Chat</h4>
                        <p>Available 24/7</p>
                        <small>Click the chat icon below</small>
                    </div> -->
                </div>
                
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="window.location.reload()">
                        <i class="fas fa-redo"></i> Retry Request
                    </button>
                    <button class="btn btn-secondary" onclick="goToHomepage()">
                        <i class="fas fa-home"></i> Go to Homepage
                    </button>
                </div>
            </div>
            
            <div class="footer">
                <p>&copy; {{ date('Y') }} Your Capital Knowledge Myanmar. All rights reserved.</p>
                <p style="margin-top: 10px; font-size: 0.85rem;">
                </p>
            </div>
        </div>
    </div>

    <script>
        function goToHomepage() {
            window.location.href = '/';
        }
        
        // Auto-refresh after 30 seconds (optional)
        setTimeout(() => {
            const shouldRefresh = confirm('Would you like to check if the issue has been resolved?');
            if (shouldRefresh) {
                window.location.reload();
            }
        }, 30000);
        
        // Fade in animation
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelector('.error-container').style.opacity = '1';
        });
    </script>
</body>
</html>
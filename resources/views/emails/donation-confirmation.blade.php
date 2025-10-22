<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .donation-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }
        .amount {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Thank You for Your Donation!</h1>
        <p>Your support means the world to us</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $donation->donor_name }},</h2>
        
        <p>Thank you for your generous donation to <strong>{{ $donation->club->name }}</strong>!</p>
        
        <div class="donation-details">
            <h3>Donation Details:</h3>
            <p><strong>Amount:</strong> <span class="amount">{{ $donation->formatted_amount }}</span></p>
            <p><strong>Club:</strong> {{ $donation->club->name }}</p>
            <p><strong>Date:</strong> {{ $donation->completed_at->format('F j, Y \a\t g:i A') }}</p>
            @if($donation->message)
                <p><strong>Your Message:</strong> "{{ $donation->message }}"</p>
            @endif
        </div>
        
        <p>Your donation will help support {{ $donation->club->name }}'s programs, equipment, and activities. Every contribution makes a difference in the lives of our athletes.</p>
        
        <p>If you have any questions about your donation, please don't hesitate to contact us.</p>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ route('public.club.profile', $donation->club->slug) }}" class="btn">Visit Club Profile</a>
        </div>
        
        <p>Thank you again for your support!</p>
        
        <p>Best regards,<br>
        The {{ $donation->club->name }} Team</p>
    </div>
    
    <div class="footer">
        <p>This is an automated message from the P2E Sports platform.</p>
        <p>If you have any questions, please contact us at support@play2earnsports.com</p>
    </div>
</body>
</html>

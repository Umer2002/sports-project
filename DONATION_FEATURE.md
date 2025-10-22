# Donation Functionality Implementation

## Overview
This document describes the donation functionality implemented for the P2E Sports platform, allowing users to make donations to clubs using Stripe payment processing.

## Features Implemented

### 1. Database Structure
- **Donations Table**: Stores donation records with donor information, amounts, and status
- **Payments Table**: Updated to include donation type for tracking
- **Club Model**: Enhanced with donation relationships and statistics

### 2. Core Functionality
- **Donation Modal**: Interactive modal for collecting donation information
- **Stripe Integration**: Secure payment processing using Stripe Checkout
- **Donation Tracking**: Complete tracking of donation status and history
- **Success/Cancel Pages**: User-friendly confirmation pages

### 3. Admin Management
- **Donations Dashboard**: View and manage all donations
- **Filtering & Search**: Filter by club, status, and search by donor information
- **Export Functionality**: Export donation data to CSV
- **Detailed Views**: Individual donation details with donor and club information

### 4. User Experience
- **Club Profile Integration**: Donation button on club profile pages
- **Real-time Statistics**: Display total donations and count on club profiles
- **Responsive Design**: Works on all device sizes

## Database Schema

### Donations Table
```sql
CREATE TABLE donations (
    id BIGINT PRIMARY KEY,
    donor_id BIGINT NULL,
    club_id BIGINT NOT NULL,
    stripe_session_id VARCHAR NULL,
    amount INTEGER NOT NULL, -- Amount in cents
    currency VARCHAR DEFAULT 'usd',
    donor_name VARCHAR NULL,
    donor_email VARCHAR NULL,
    message TEXT NULL,
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### Payments Table (Updated)
- Added support for 'donation' type in the type field

## API Endpoints

### Public Routes
- `POST /donation/checkout` - Create donation checkout session
- `GET /donation/success` - Handle successful donations
- `GET /donation/cancel` - Handle cancelled donations
- `POST /donation/webhook` - Stripe webhook for payment confirmation

### Admin Routes
- `GET /admin/donations` - List all donations
- `GET /admin/donations/{id}` - View donation details
- `GET /admin/donations/export/csv` - Export donations

## Models

### Donation Model
- Relationships with User (donor) and Club
- Helper methods for formatted amounts
- Status management methods

### Club Model (Enhanced)
- `donations()` relationship
- `total_donations` attribute
- `donations_count` attribute

## Controllers

### DonationController
- Handles donation creation and Stripe integration
- Manages success/cancel flows
- Processes Stripe webhooks

### Admin\DonationController
- Manages donation listing and filtering
- Provides export functionality
- Detailed donation views

## Views

### Public Views
- **Donation Modal**: Embedded in club profile pages
- **Success Page**: Confirmation page after successful donation
- **Club Profile**: Enhanced with donation statistics

### Admin Views
- **Donations Index**: List with filtering and search
- **Donation Show**: Detailed view of individual donations
- **Payments Dashboard**: Updated to include donation statistics

## Testing

### Test Coverage
- Donation modal functionality
- Donation creation and validation
- Success page handling
- Club donation statistics
- Amount validation

### Running Tests
```bash
php artisan test tests/Feature/DonationTest.php
```

## Configuration

### Environment Variables Required
```env
STRIPE_KEY=your_stripe_publishable_key
STRIPE_SECRET=your_stripe_secret_key
STRIPE_WEBHOOK_SECRET=your_stripe_webhook_secret
```

### Stripe Setup
1. Create a Stripe account
2. Configure webhook endpoint: `https://yourdomain.com/donation/webhook`
3. Set webhook events: `checkout.session.completed`

## Usage

### For Users
1. Navigate to a club profile page
2. Click "MAKE A DONATION" button
3. Fill in donation form (name, email, amount, optional message)
4. Complete payment via Stripe
5. Receive confirmation

### For Admins
1. Access admin dashboard
2. Navigate to "Donations" section
3. View, filter, and export donation data
4. Monitor donation statistics

## Security Features
- CSRF protection on all forms
- Stripe webhook signature verification
- Input validation and sanitization
- Secure payment processing via Stripe

## Future Enhancements
- Recurring donations
- Donation goals and progress bars
- Donor recognition features
- Email notifications
- Tax receipt generation
- Donation analytics and reporting

## Troubleshooting

### Common Issues
1. **Stripe session not found**: Check webhook configuration
2. **Payment not completing**: Verify Stripe keys and webhook setup
3. **Modal not opening**: Check JavaScript console for errors

### Debug Mode
Enable debug logging in `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Support
For technical support or questions about the donation functionality, please refer to the application logs or contact the development team.

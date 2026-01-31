# Payment Module Quick Reference Guide

## Installation Steps

### 1. Run Migrations
```bash
cd backend
php artisan migrate
```

### 2. Register Policy in AuthServiceProvider
Add to `app/Providers/AuthServiceProvider.php`:
```php
use App\Modules\Payment\Models\Payment;
use App\Modules\Payment\Policies\PaymentPolicy;

protected $policies = [
    Payment::class => PaymentPolicy::class,
];
```

### 3. Add Routes
Add to `routes/api.php`:
```php
use App\Modules\Payment\Http\Controllers\PaymentController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('payments', PaymentController::class);
    Route::post('payments/{id}/complete', [PaymentController::class, 'complete']);
    Route::post('payments/{id}/refund', [PaymentController::class, 'refund']);
    Route::get('payments/summary', [PaymentController::class, 'summary']);
});
```

### 4. Seed Permissions
```sql
INSERT INTO permissions (id, name, guard_name, created_at, updated_at) VALUES
(UUID(), 'payments.view', 'web', NOW(), NOW()),
(UUID(), 'payments.create', 'web', NOW(), NOW()),
(UUID(), 'payments.update', 'web', NOW(), NOW()),
(UUID(), 'payments.delete', 'web', NOW(), NOW()),
(UUID(), 'payments.restore', 'web', NOW(), NOW()),
(UUID(), 'payments.complete', 'web', NOW(), NOW()),
(UUID(), 'payments.refund', 'web', NOW(), NOW()),
(UUID(), 'payments.cancel', 'web', NOW(), NOW());
```

## API Usage Examples

### Create Payment
```bash
POST /api/v1/payments
Content-Type: application/json
Authorization: Bearer {token}

{
  "tenant_id": "uuid",
  "branch_id": "uuid",
  "customer_id": "uuid",
  "invoice_id": "uuid",  // optional
  "payment_number": "PAY-2026-001",
  "payment_date": "2026-01-31",
  "payment_method": "card",
  "amount": 1000.00,
  "currency": "USD",
  "status": "pending",
  "reference_number": "REF123",
  "transaction_id": "TXN456",
  "notes": "Payment for invoice INV-2026-001"
}
```

### Complete Payment
```bash
POST /api/v1/payments/{id}/complete
Content-Type: application/json
Authorization: Bearer {token}

{
  "transaction_id": "TXN789"  // optional
}
```

### Create Refund
```bash
POST /api/v1/payments/{id}/refund
Content-Type: application/json
Authorization: Bearer {token}

{
  "refund_number": "REF-2026-001",
  "refund_date": "2026-01-31",
  "amount": 500.00,
  "reason": "Customer requested refund",
  "processed_by": "user-uuid",  // optional
  "notes": "Partial refund approved"
}
```

### Get Payment Summary
```bash
GET /api/v1/payments/summary?from=2026-01-01&to=2026-01-31
Authorization: Bearer {token}
```

Response:
```json
{
  "success": true,
  "message": "Payment summary retrieved successfully",
  "data": {
    "total_payments": 150,
    "total_amount": 150000.00,
    "completed_amount": 140000.00,
    "pending_amount": 8000.00,
    "refunded_amount": 2000.00,
    "total_refunds": 2500.00,
    "by_method": {
      "cash": { "count": 50, "amount": 25000.00 },
      "card": { "count": 70, "amount": 95000.00 },
      "bank_transfer": { "count": 20, "amount": 25000.00 },
      "cheque": { "count": 5, "amount": 3000.00 },
      "online": { "count": 5, "amount": 2000.00 },
      "other": { "count": 0, "amount": 0 }
    },
    "by_status": {
      "pending": 10,
      "completed": 135,
      "failed": 2,
      "refunded": 2,
      "cancelled": 1
    }
  }
}
```

## Service Usage in Code

### Create Payment
```php
use App\Modules\Payment\Services\PaymentService;
use App\Modules\Payment\DTOs\PaymentDTO;

$paymentService = app(PaymentService::class);

$dto = PaymentDTO::fromArray([
    'tenant_id' => $tenantId,
    'branch_id' => $branchId,
    'customer_id' => $customerId,
    'payment_number' => 'PAY-2026-001',
    'payment_date' => '2026-01-31',
    'payment_method' => 'card',
    'amount' => 1000.00,
    'currency' => 'USD',
    'status' => 'pending',
]);

$payment = $paymentService->createPayment($dto);
```

### Record Payment for Invoice
```php
$dto = PaymentDTO::fromArray([
    'payment_number' => 'PAY-2026-001',
    'payment_date' => '2026-01-31',
    'payment_method' => 'card',
    'amount' => 1000.00,
    'status' => 'completed',
]);

$payment = $paymentService->recordPaymentForInvoice($invoiceId, $dto);
// Automatically updates invoice paid_amount and payment_status
```

### Complete Payment
```php
$result = $paymentService->completePayment($paymentId, 'TXN123');
// Automatically updates invoice if payment is linked
```

### Refund Payment
```php
use App\Modules\Payment\DTOs\PaymentRefundDTO;

$dto = PaymentRefundDTO::fromArray([
    'refund_number' => 'REF-2026-001',
    'refund_date' => '2026-01-31',
    'amount' => 500.00,
    'reason' => 'Customer requested refund',
]);

$refund = $paymentService->refundPayment($paymentId, $dto);
```

### Process Refund
```php
$result = $paymentService->processRefund($refundId);
// Updates payment status to 'refunded' if fully refunded
// Updates invoice paid_amount
```

### Get Payment Summary
```php
$summary = $paymentService->getPaymentSummary('2026-01-01', '2026-01-31');
```

## Repository Usage

### Find by Payment Number
```php
use App\Modules\Payment\Repositories\PaymentRepository;

$repository = app(PaymentRepository::class);
$payment = $repository->findByPaymentNumber('PAY-2026-001');
```

### Find by Customer
```php
$payments = $repository->findByCustomer($customerId, ['invoice', 'refunds']);
```

### Find by Invoice
```php
$payments = $repository->findByInvoice($invoiceId);
```

### Get by Status
```php
$pendingPayments = $repository->getByStatus('pending');
```

### Get by Payment Method
```php
$cardPayments = $repository->getByPaymentMethod('card');
```

### Get by Date Range
```php
$payments = $repository->getByDateRange('2026-01-01', '2026-01-31');
```

### Get Total by Method
```php
$totalCash = $repository->getTotalByMethod('cash', '2026-01-01', '2026-01-31');
```

## Model Helper Methods

### Check Payment Status
```php
$payment = Payment::find($id);

if ($payment->isPending()) {
    // Payment is pending
}

if ($payment->isCompleted()) {
    // Payment is completed
}

if ($payment->canBeCompleted()) {
    // Payment can be completed
}

if ($payment->canBeRefunded()) {
    // Payment can be refunded
}
```

### Refund Information
```php
$totalRefunded = $payment->getTotalRefunded();
$remainingAmount = $payment->getRemainingAmount();
```

## Query Scopes

```php
// Get pending payments
$pending = Payment::pending()->get();

// Get completed payments for a customer
$payments = Payment::completed()
    ->byCustomer($customerId)
    ->get();

// Get payments by method and date range
$payments = Payment::byPaymentMethod('card')
    ->byDateRange('2026-01-01', '2026-01-31')
    ->get();

// Search payments
$results = Payment::search('PAY-2026')->get();
```

## Events

Listen to payment events:

```php
// In EventServiceProvider.php
use App\Modules\Payment\Events\PaymentCompleted;
use App\Modules\Payment\Listeners\SendPaymentConfirmation;

protected $listen = [
    PaymentCompleted::class => [
        SendPaymentConfirmation::class,
    ],
];
```

Available events:
- PaymentCreated
- PaymentUpdated
- PaymentDeleted
- PaymentCompleted
- PaymentFailed
- PaymentRefunded
- RefundProcessed

## Validation Rules

### Payment Creation
- tenant_id: required, exists in tenants
- branch_id: required, exists in organizations
- customer_id: required, exists in customers
- invoice_id: optional, exists in invoices
- payment_number: required, unique, max 100 chars
- payment_date: required, valid date
- payment_method: required, one of: cash, card, bank_transfer, cheque, online, other
- amount: required, numeric, min 0.01
- currency: optional, string, max 3 chars
- status: optional, one of: pending, completed, failed, refunded, cancelled

### Refund Creation
- refund_number: required, unique, max 100 chars
- refund_date: required, valid date
- amount: required, numeric, min 0.01, must not exceed remaining payment amount
- reason: required, string
- processed_by: optional, exists in users

## Common Workflows

### 1. Record Invoice Payment
```php
// Create payment linked to invoice
$payment = $paymentService->recordPaymentForInvoice($invoiceId, $dto);

// Complete the payment
$paymentService->completePayment($payment->id, $transactionId);

// Invoice is automatically updated with paid_amount and payment_status
```

### 2. Process Refund
```php
// Create refund
$refund = $paymentService->refundPayment($paymentId, $dto);

// Process refund
$paymentService->processRefund($refund->id);

// Payment status updates to 'refunded' if fully refunded
// Invoice paid_amount is reduced
```

### 3. Handle Failed Payment
```php
$paymentService->failPayment($paymentId, 'Insufficient funds');
```

### 4. Generate Payment Reports
```php
// Get summary for month
$summary = $paymentService->getPaymentSummary(
    now()->startOfMonth()->toDateString(),
    now()->endOfMonth()->toDateString()
);

// Get card payment totals
$cardTotal = $repository->getTotalByMethod(
    'card',
    now()->startOfMonth()->toDateString(),
    now()->endOfMonth()->toDateString()
);
```

## Database Indexes

The following indexes are automatically created for optimal query performance:

### payments table
- tenant_id, branch_id, customer_id, invoice_id
- payment_number (unique)
- payment_date, payment_method, status, transaction_id
- Composite: tenant+status, branch+status, customer+status
- Composite: tenant+method, branch+method
- Composite: tenant+date, branch+date
- Composite: date+status, method+status

### payment_refunds table
- payment_id, status, refund_date
- refund_number (unique)
- Composite: payment+status, payment+date, date+status

## Best Practices

1. **Always use transactions** when creating/updating payments that affect invoices
2. **Validate refund amounts** to prevent over-refunding
3. **Use events** for side effects (notifications, logging, etc.)
4. **Eager load relationships** to prevent N+1 queries
5. **Check permissions** before allowing payment operations
6. **Log transaction IDs** for payment gateway integration
7. **Use payment_method** consistently for reporting
8. **Record reference_number** for external payment references

## Troubleshooting

### Payment not updating invoice
- Check if payment is linked to invoice (invoice_id not null)
- Verify payment status is 'completed'
- Check if invoice canReceivePayment() returns true

### Refund validation failing
- Verify refund amount doesn't exceed getRemainingAmount()
- Check payment status is 'completed'
- Ensure payment canBeRefunded() returns true

### Permission denied errors
- Verify user has required payment permissions
- Check if PaymentPolicy is registered in AuthServiceProvider
- Ensure permissions are seeded in database

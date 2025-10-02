<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model {
    use HasFactory;

    const STATUS_AWAITING = 'awaiting_payment';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELED = 'canceled';

    protected $fillable = [
        'user_id','specialty_id','clinic_id',
        'cep','city','state',
        'available_days','preferred_time',
        'indication','notes',
        'coparticipation_price','status',
        'asaas_payment_id','asaas_invoice_url','asaas_pix_qr_base64','asaas_pix_payload'
    ];

    public function specialty(){ return $this->belongsTo(Specialty::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
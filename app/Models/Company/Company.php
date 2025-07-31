<?php

declare(strict_types=1);

namespace App\Models\Company;

use App\Models\Address\Address;
use App\Models\User;
use Carbon\Carbon;
use Database\Factories\Company\CompanyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Billable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $vat
 * @property string|null $email
 * @property string|null $public_email
 * @property string|null $public_phone
 * @property string|null $industry
 * @property string|null $provider
 * @property string|null $logo
 * @property int|null $address_id
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property Carbon|null $trial_ends_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Address|null $address
 */
class Company extends Model
{
    use Billable;

    /** @use HasFactory<CompanyFactory> */
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'trial_ends_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Address, $this>
     */
    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    /**
     * @return BelongsToMany<User, $this, Pivot>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function isInSyncWithStripe(): bool
    {
        return isset($this->stripe_id);
    }

    public function shouldSyncWithStripe(): bool
    {
        return $this->provider === null;
    }

    protected static function booted(): void
    {
        static::created(function (Company $company) {
            if (! $company->hasStripeId() && $company->shouldSyncWithStripe()) {
                if (app()->environment('testing')) {
                    $company->syncStripeCustomerDetails();
                } else {
                    dispatch(function () use ($company) {
                        $company->syncStripeCustomerDetails();
                    })->afterResponse();
                }
            }
        });

        static::updated(function (Company $company) {
            if ($company->hasStripeId() && $company->shouldSyncWithStripe()) {
                if (app()->environment('testing')) {
                    $company->syncStripeCustomerDetails();
                } else {
                    dispatch(function () use ($company) {
                        $company->syncStripeCustomerDetails();
                    })->afterResponse();
                }
            }
        });

        static::deleting(function (Company $company) {
            if ($company->hasStripeId()) {
                if (app()->environment('testing')) {
                    $company->markStripeCustomerAsDeleted();
                } else {
                    dispatch(function () use ($company) {
                        $company->markStripeCustomerAsDeleted();
                    })->afterResponse();
                }
            }
        });
    }

    public function syncStripeCustomerDetails(): void
    {
        try {
            if (! $this->hasStripeId()) {
                $customer = $this->createAsStripeCustomer([
                    'name' => $this->stripeName(),
                    'email' => $this->stripeEmail(),
                    'phone' => $this->stripePhone(),
                    'address' => $this->stripeAddress(),
                    'metadata' => [
                        'company_id' => $this->id,
                        'application_deleted' => 'false',
                    ],
                ]);
                Log::info("Created Stripe customer for company {$this->id}: {$customer->id}");
            } else {
                $this->updateStripeCustomer([
                    'name' => $this->stripeName(),
                    'email' => $this->stripeEmail(),
                    'phone' => $this->stripePhone(),
                    'address' => $this->stripeAddress(),
                    'metadata' => [
                        'company_id' => $this->id,
                        'application_deleted' => 'false',
                    ],
                ]);
                Log::info("Updated Stripe customer for company {$this->id}: {$this->stripe_id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to sync Stripe customer for company {$this->id}: ".$e->getMessage());
        }
    }

    public function markStripeCustomerAsDeleted(): void
    {
        try {
            if ($this->hasStripeId()) {
                $this->updateStripeCustomer([
                    'metadata' => [
                        'company_id' => $this->id,
                        'application_deleted' => 'true',
                    ],
                ]);
                Log::info("Marked Stripe customer as deleted for company {$this->id}: {$this->stripe_id}");
            }
        } catch (\Exception $e) {
            Log::error("Failed to mark Stripe customer as deleted for company {$this->id}: ".$e->getMessage());
        }
    }

    public function stripeName(): string
    {
        return $this->name;
    }

    public function stripeEmail(): ?string
    {
        return $this->email;
    }

    public function stripePhone(): ?string
    {
        return $this->public_phone;
    }

    /**
     * @return array<string, string|null>
     */
    public function stripeAddress(): array
    {
        if (! $this->address) {
            return [];
        }

        return [
            'city' => $this->address->city->name ?? '',
            'country' => strtoupper($this->address->country_iso_code ?? 'DE'),
            'line1' => trim(($this->address->street->name ?? '').' '.($this->address->house_number ?? '')),
            'line2' => $this->address->address_addition,
            'postal_code' => $this->address->city->zip ?? '',
            'state' => $this->address->city->state->name ?? '',
        ];
    }
}

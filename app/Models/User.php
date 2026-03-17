<?php

namespace App\Models;

use App\Notifications\VerifyEmailNotification;
use App\Support\RolePermissionRegistry;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_SUPERADMIN = 'superadmin';
    public const ROLE_REGIONAL = 'user_regional';
    public const ROLE_PROVINCIAL = 'user_provincial';
    public const ROLE_LGU = 'user_lgu';

    public const ACCESS_SCOPE_ALL = 'crud:*';
    public const ACCESS_SCOPE_NONE = 'crud:none';
    public const ACCESS_PERMISSION_PREFIX = 'crud:';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tbusers';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'idno';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'agency',
        'position',
        'region',
        'province',
        'office',
        'emailaddress',
        'mobileno',
        'username',
        'password',
        'role',
        'status',
        'access',
        'verification_token',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'email_verified_at' => 'datetime',
        ];
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification()
    {
        return $this->emailaddress;
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified()
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Generate a verification token and save it.
     *
     * @return string
     */
    public function generateVerificationToken()
    {
        $token = Str::random(64);
        $this->verification_token = $token;
        $this->save();

        return $token;
    }

    /**
     * Verify the user's email using the token.
     *
     * @param string $token
     * @return bool
     */
    public function verifyEmailWithToken($token)
    {
        if ($this->verification_token === $token && $this->status === 'inactive') {
            $this->email_verified_at = now();
            $this->status = 'active';
            $this->verification_token = null;

            return $this->save();
        }

        return false;
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        try {
            if (!$this->verification_token) {
                $this->generateVerificationToken();
            }

            \Illuminate\Support\Facades\Mail::send(new \App\Mail\VerifyEmailMailable($this));

            \Illuminate\Support\Facades\Log::info('Verification email sent', [
                'user_id' => $this->id,
                'email' => $this->emailaddress,
                'timestamp' => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send verification email', [
                'user_id' => $this->id,
                'email' => $this->emailaddress,
                'error' => $e->getMessage(),
                'driver' => config('mail.default'),
            ]);

            $this->notify(new VerifyEmailNotification);
        }
    }

    /**
     * Get the email address to be used for password reset.
     *
     * @return string
     */
    public function getEmailForPasswordReset()
    {
        return $this->emailaddress;
    }

    /**
     * Find the user by username for authentication.
     *
     * @param string $username
     * @return mixed
     */
    public static function findByUsername($username)
    {
        return static::where('username', $username)->first();
    }

    public static function roleOptions(): array
    {
        return [
            self::ROLE_SUPERADMIN => 'Superadmin',
            self::ROLE_REGIONAL => 'Regional User',
            self::ROLE_PROVINCIAL => 'Provincial User',
            self::ROLE_LGU => 'LGU User',
        ];
    }

    public function normalizedRole(): string
    {
        return strtolower(trim((string) $this->role));
    }

    public function roleLabel(): string
    {
        return self::roleOptions()[$this->normalizedRole()] ?? ucwords(str_replace('_', ' ', $this->normalizedRole()));
    }

    public function isSuperAdmin(): bool
    {
        return $this->normalizedRole() === self::ROLE_SUPERADMIN;
    }

    public function isRegionalUser(): bool
    {
        return $this->normalizedRole() === self::ROLE_REGIONAL;
    }

    public function isProvincialUser(): bool
    {
        return $this->normalizedRole() === self::ROLE_PROVINCIAL;
    }

    public function isLguUser(): bool
    {
        return $this->normalizedRole() === self::ROLE_LGU;
    }

    public function defaultCrudPermissions(): array
    {
        return RolePermissionRegistry::permissionsForRole(
            $this->normalizedRole(),
            RolePermissionSetting::permissionsForRole($this->normalizedRole()),
        );
    }

    public function usesScopedCrudAccess(): bool
    {
        $access = strtolower(trim((string) $this->access));

        return $access === self::ACCESS_SCOPE_ALL
            || $access === self::ACCESS_SCOPE_NONE
            || str_starts_with($access, self::ACCESS_PERMISSION_PREFIX);
    }

    public function grantedCrudPermissions(): array
    {
        $access = strtolower(trim((string) $this->access));

        if ($access === self::ACCESS_SCOPE_ALL) {
            return ['*'];
        }

        if ($access === self::ACCESS_SCOPE_NONE || $access === '') {
            return [];
        }

        if (!str_starts_with($access, self::ACCESS_PERMISSION_PREFIX)) {
            return [];
        }

        $permissions = substr($access, strlen(self::ACCESS_PERMISSION_PREFIX));

        return RolePermissionRegistry::normalizePermissions(
            array_values(array_filter(array_map('trim', explode(',', $permissions))))
        );
    }

    public function effectiveCrudPermissions(): array
    {
        return $this->usesScopedCrudAccess()
            ? $this->grantedCrudPermissions()
            : $this->defaultCrudPermissions();
    }

    public function effectiveConcreteCrudPermissions(): array
    {
        $permissions = $this->effectiveCrudPermissions();

        if (in_array('*', $permissions, true)) {
            return RolePermissionRegistry::validPermissionKeys();
        }

        return RolePermissionRegistry::normalizePermissions($permissions);
    }

    public function hasCustomCrudPermissions(): bool
    {
        return $this->usesScopedCrudAccess();
    }

    public function permissionCandidateKeys(string $aspect, string $action): array
    {
        $normalizedAspect = strtolower(trim($aspect));
        $normalizedAction = strtolower(trim($action));
        $candidateActions = array_values(array_unique(array_filter([
            $normalizedAction,
            $normalizedAction === 'upload' ? 'add' : null,
            $normalizedAction === 'add' ? 'upload' : null,
            $normalizedAction === 'view' ? 'add' : null,
            $normalizedAction === 'view' ? 'upload' : null,
            $normalizedAction === 'view' ? 'update' : null,
            $normalizedAction === 'view' ? 'delete' : null,
        ])));

        return array_map(function ($candidateAction) use ($normalizedAspect) {
            return $normalizedAspect . '.' . $candidateAction;
        }, $candidateActions);
    }

    public function hasDefaultCrudPermission(string $aspect, string $action): bool
    {
        $defaultPermissions = $this->defaultCrudPermissions();

        if (in_array('*', $defaultPermissions, true)) {
            return true;
        }

        return count(array_intersect($this->permissionCandidateKeys($aspect, $action), $defaultPermissions)) > 0;
    }

    public function hasExplicitCrudPermission(string $aspect, string $action): bool
    {
        if (!$this->usesScopedCrudAccess()) {
            return false;
        }

        $explicitPermissions = $this->grantedCrudPermissions();

        if (in_array('*', $explicitPermissions, true)) {
            return true;
        }

        return count(array_intersect($this->permissionCandidateKeys($aspect, $action), $explicitPermissions)) > 0;
    }

    public function hasCrudPermission(string $aspect, string $action): bool
    {
        $permissions = $this->effectiveCrudPermissions();

        if (in_array('*', $permissions, true)) {
            return true;
        }

        $permissionKeys = $this->permissionCandidateKeys($aspect, $action);

        return count(array_intersect($permissionKeys, $permissions)) > 0;
    }
}

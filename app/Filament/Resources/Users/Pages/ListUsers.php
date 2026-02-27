<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Mail\UserInvitationMail;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\UserInvitation;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('inviteUser')
                ->label('Invite User')
                ->icon('heroicon-m-envelope')
                ->form([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255),
                    Select::make('role_id')
                        ->label('Role')
                        ->options(fn (): array => UserResource::roleOptions())
                        ->required()
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(function (mixed $state, callable $set): void {
                            if (! filled($state)) {
                                return;
                            }

                            $roleName = Role::query()->whereKey($state)->value('name');

                            if (! is_string($roleName)) {
                                return;
                            }

                            $set('permissions', UserResource::defaultPermissionsForRole($roleName));
                        }),
                    Select::make('organization_id')
                        ->label('Organization')
                        ->options(fn (): array => Organization::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray())
                        ->searchable()
                        ->preload(),
                    TextInput::make('new_organization_name')
                        ->label('Create Organization (optional)')
                        ->maxLength(255)
                        ->helperText('If selected organization is empty, this name will be used to create one.'),
                    CheckboxList::make('permissions')
                        ->label('Permissions')
                        ->options(\App\Support\CrmAccess::permissionOptions())
                        ->columns(2)
                        ->bulkToggleable()
                        ->default([]),
                ])
                ->action(function (array $data): void {
                    $inviter = auth()->user();

                    if (! $inviter || ! $inviter->isSuperAdmin()) {
                        Notification::make()
                            ->title('Only the main admin can invite users.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $role = Role::query()->find($data['role_id'] ?? null);

                    if (! $role) {
                        Notification::make()
                            ->title('Please select a valid role.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $organizationId = filled($data['organization_id'] ?? null)
                        ? (int) $data['organization_id']
                        : null;

                    if (! $organizationId && filled($data['new_organization_name'] ?? null)) {
                        $organization = Organization::query()->create([
                            'name' => (string) $data['new_organization_name'],
                            'slug' => Str::slug((string) $data['new_organization_name']).'-'.Str::lower(Str::random(6)),
                            'tenant_code' => Str::upper(Str::random(8)),
                            'type' => $role->name === 'manufacturer' ? 'manufacturer' : 'buyer',
                            'status' => 'active',
                            'currency_code' => 'USD',
                            'created_by' => $inviter->id,
                        ]);

                        $organizationId = $organization->id;
                    }

                    if (in_array($role->name, ['manufacturer', 'organization_admin', 'supplier'], true) && ! $organizationId) {
                        Notification::make()
                            ->title('Organization is required for this role.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $permissions = is_array($data['permissions'] ?? null) && $data['permissions'] !== []
                        ? $data['permissions']
                        : UserResource::defaultPermissionsForRole($role->name);

                    $invitation = UserInvitation::query()->create([
                        'name' => (string) $data['name'],
                        'email' => (string) $data['email'],
                        'role_id' => $role->id,
                        'role' => $role->name,
                        'organization_id' => $organizationId,
                        'permissions' => $permissions,
                        'invited_by' => $inviter->id,
                        'token' => Str::random(64),
                        'expires_at' => now()->addDays(7),
                    ]);

                    if ($role->name === 'supplier' && $organizationId) {
                        Supplier::query()->updateOrCreate(
                            [
                                'organization_id' => $organizationId,
                                'email' => (string) $data['email'],
                            ],
                            [
                                'name' => (string) $data['name'],
                                'status' => 'invited',
                            ]
                        );
                    }

                    $inviteUrl = route('user.invitation.show', $invitation->token);
                    $organizationName = $invitation->organization?->name;

                    Mail::to($invitation->email)->send(new UserInvitationMail(
                        inviteeName: $invitation->name,
                        roleName: str($invitation->role)->replace('_', ' ')->title()->value(),
                        inviteUrl: $inviteUrl,
                        organizationName: $organizationName,
                        inviterName: $inviter->name,
                    ));

                    AuditLog::query()->create([
                        'organization_id' => $organizationId,
                        'user_id' => $inviter->id,
                        'action' => 'user.invitation.sent',
                        'auditable_type' => UserInvitation::class,
                        'auditable_id' => $invitation->id,
                        'metadata' => [
                            'email' => $invitation->email,
                            'role' => $invitation->role,
                        ],
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'created_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Invitation sent to '.$invitation->email)
                        ->success()
                        ->send();
                }),
        ];
    }
}

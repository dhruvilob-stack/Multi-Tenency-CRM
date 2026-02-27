<?php

namespace App\Filament\Resources\Suppliers\Tables;

use App\Mail\SupplierInvitationMail;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\Supplier;
use App\Models\SupplierInvitation;
use App\Support\WorkflowNotifier;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SuppliersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('organization.name')
                    ->label('Organization')
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('phone')
                    ->toggleable(isToggledHiddenByDefault: true),
                BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'invited',
                        'success' => 'active',
                        'gray' => 'inactive',
                    ])
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                Action::make('inviteSupplier')
                    ->label('Invite Supplier')
                    ->icon('heroicon-m-envelope')
                    ->form([
                        Select::make('organization_id')
                            ->label('Organization')
                            ->options(fn (): array => Organization::query()
                                ->where('type', 'buyer')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        $user = auth()->user();

                        if (! $user) {
                            Notification::make()
                                ->title('Authentication required.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $organization = Organization::query()->find($data['organization_id']);

                        if (! $organization) {
                            Notification::make()
                                ->title('Organization not found.')
                                ->danger()
                                ->send();

                            return;
                        }

                        $supplier = Supplier::query()->firstOrCreate(
                            [
                                'organization_id' => $organization->id,
                                'email' => $data['email'],
                            ],
                            [
                                'name' => $data['name'],
                                'status' => 'invited',
                            ]
                        );

                        if ($supplier->name !== $data['name']) {
                            $supplier->forceFill(['name' => $data['name']])->save();
                        }

                        $invitation = SupplierInvitation::query()->create([
                            'organization_id' => $organization->id,
                            'supplier_id' => $supplier->id,
                            'invited_by' => $user->id,
                            'email' => $supplier->email,
                            'token' => Str::random(64),
                            'expires_at' => now()->addDays(7),
                        ]);

                        $inviteUrl = route('supplier.invitation.show', $invitation->token);

                        Mail::to($supplier->email)->send(new SupplierInvitationMail(
                            $organization->name,
                            $inviteUrl,
                        ));

                        AuditLog::query()->create([
                            'organization_id' => $organization->id,
                            'user_id' => $user->id,
                            'action' => 'supplier.invitation.sent',
                            'auditable_type' => SupplierInvitation::class,
                            'auditable_id' => $invitation->id,
                            'metadata' => ['email' => $supplier->email],
                            'ip_address' => request()->ip(),
                            'user_agent' => request()->userAgent(),
                            'created_at' => now(),
                        ]);

                        WorkflowNotifier::notifyOrganizationAdmins(
                            $organization->id,
                            'Supplier invitation sent',
                            sprintf('%s invited for %s.', $supplier->email, $organization->name),
                            '/manufacturer/suppliers',
                            'catalog'
                        );

                        Notification::make()
                            ->title('Invitation sent to '.$supplier->email)
                            ->success()
                            ->send();
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}

<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditProfile extends BaseEditProfile
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_path')
                    ->label('Profile image')
                    ->avatar()
                    ->image()
                    ->orientImagesFromExif()
                    ->disk('public')
                    ->directory('avatars')
                    ->visibility('public')
                    ->imageEditor()
                    ->circleCropper()
                    ->columnSpanFull(),
                $this->getNameFormComponent()
                    ->label($this->getAccountNameLabel()),
                $this->getEmailFormComponent()
                    ->disabled(),
                TextInput::make('phone')
                    ->label('Phone')
                    ->tel()
                    ->maxLength(50),
                TextInput::make('organization_name')
                    ->label('Organization Name')
                    ->maxLength(255)
                    ->visible(fn (): bool => $this->getUser()->isSupplier() && (bool) $this->getUser()->organization_id)
                    ->disabled()
                    ->dehydrated(false),
                $this->getPasswordFormComponent(),
                $this->getPasswordConfirmationFormComponent(),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data = parent::mutateFormDataBeforeFill($data);

        $user = $this->getUser();

        if ($user->isSupplier() && $user->supplier) {
            $data['name'] = $user->supplier->name;
        } elseif ($user->organization) {
            $data['name'] = $user->organization->name;
        }

        $data['organization_name'] = $user->organization?->name;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record = parent::handleRecordUpdate($record, $data);

        if ($record->isSupplier() && $record->supplier) {
            $record->supplier->forceFill([
                'name' => $record->name,
                'email' => $record->email,
            ])->save();
        } elseif ($record->organization && ($record->isOrganizationAdmin() || $record->organization?->type === 'manufacturer')) {
            $record->organization->forceFill([
                'name' => $record->name,
            ])->save();
        }

        return $record;
    }

    private function getAccountNameLabel(): string
    {
        $user = $this->getUser();

        if ($user->isSupplier()) {
            return 'Supplier Name';
        }

        if ($user->organization?->type === 'manufacturer') {
            return 'Manufacturer Name';
        }

        if ($user->organization_id) {
            return 'Organization Name';
        }

        return 'Name';
    }
}

<?php

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

trait HasRoleResourceAccess
{
    public static function canViewAny(): bool
    {
        return self::currentUserHasAnyRole(['admin', 'guru', 'siswa']);
    }

    public static function canView(Model $record): bool
    {
        return self::currentUserHasAnyRole(['admin', 'guru', 'siswa']);
    }

    public static function canCreate(): bool
    {
        return self::currentUserHasAnyRole(['admin', 'guru']);
    }

    public static function canEdit(Model $record): bool
    {
        return self::currentUserHasAnyRole(['admin', 'guru']);
    }

    public static function canDelete(Model $record): bool
    {
        return self::currentUserHasAnyRole(['admin']);
    }

    public static function canDeleteAny(): bool
    {
        return self::currentUserHasAnyRole(['admin']);
    }

    protected static function roleAwareTableActions(): array
    {
        return [
            \Filament\Actions\ViewAction::make(),
            \Filament\Actions\EditAction::make()
                ->visible(fn (): bool => self::currentUserHasAnyRole(['admin', 'guru'])),
            self::deniedAction('edit_denied', 'Ubah', 'Fitur ubah data hanya bisa digunakan oleh admin dan guru.')
                ->icon('heroicon-m-pencil-square')
                ->color('warning')
                ->visible(fn (): bool => self::currentUserHasAnyRole(['siswa'])),
            \Filament\Actions\DeleteAction::make()
                ->visible(fn (): bool => self::currentUserHasAnyRole(['admin'])),
            self::deniedAction('delete_denied', 'Hapus', 'Fitur hapus data hanya bisa digunakan oleh admin.')
                ->icon('heroicon-m-trash')
                ->color('danger')
                ->visible(fn (): bool => self::currentUserHasAnyRole(['guru', 'siswa'])),
        ];
    }

    protected static function roleAwareBulkActions(): array
    {
        return [
            \Filament\Actions\BulkActionGroup::make([
                \Filament\Actions\DeleteBulkAction::make()
                    ->visible(fn (): bool => self::currentUserHasAnyRole(['admin'])),
                \Filament\Actions\BulkAction::make('delete_bulk_denied')
                    ->label('Hapus terpilih')
                    ->icon('heroicon-m-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Akses ditolak')
                    ->modalDescription('Fitur hapus data hanya bisa digunakan oleh admin.')
                    ->action(fn () => self::sendDeniedNotification('Fitur hapus data hanya bisa digunakan oleh admin.'))
                    ->deselectRecordsAfterCompletion()
                    ->visible(fn (): bool => self::currentUserHasAnyRole(['guru', 'siswa'])),
            ]),
        ];
    }

    public static function createHeaderAction(): \Filament\Actions\Action
    {
        if (self::currentUserHasAnyRole(['admin', 'guru'])) {
            return \Filament\Actions\CreateAction::make();
        }

        return self::deniedAction('create_denied', 'Tambah Data', 'Fitur tambah data hanya bisa digunakan oleh admin dan guru.')
            ->icon('heroicon-m-plus-circle')
            ->color('warning');
    }

    public static function deleteHeaderAction(): \Filament\Actions\Action
    {
        if (self::currentUserHasAnyRole(['admin'])) {
            return \Filament\Actions\DeleteAction::make();
        }

        return self::deniedAction('delete_denied', 'Hapus', 'Fitur hapus data hanya bisa digunakan oleh admin.')
            ->icon('heroicon-m-trash')
            ->color('danger');
    }

    protected static function deniedAction(string $name, string $label, string $message): \Filament\Actions\Action
    {
        return \Filament\Actions\Action::make($name)
            ->label($label)
            ->action(fn () => self::sendDeniedNotification($message));
    }

    protected static function sendDeniedNotification(string $message): void
    {
        Notification::make()
            ->title('Akses ditolak')
            ->body($message)
            ->warning()
            ->send();
    }

    protected static function currentUserHasAnyRole(array $roles): bool
    {
        $user = auth()->user();

        return $user?->hasAnyRole($roles) ?? false;
    }
}

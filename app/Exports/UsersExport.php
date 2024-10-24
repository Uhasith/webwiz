<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Http\Request;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithEvents
{
    protected Request $request;

    // Accept the request object in the constructor
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection(): \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|array
    {
        // Apply the same query logic as in getUsersData
        $query = User::with('roles')->select(['id', 'name','status', 'email', 'contact', 'last_login_at']);

        // Exclude users with the 'superadmin' role
        $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'superadmin');
        });

        // Filter by roles if provided
        if ($this->request->has('roles')) {
            $roles = $this->request->get('roles');
            $query->whereHas('roles', function ($q) use ($roles) {
                $q->whereIn('name', $roles);
            });
        }

        // Apply search term if provided
        if ($this->request->has('search') && !empty($this->request->get('search'))) {
            $searchTerm = $this->request->get('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('contact', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Return the filtered collection
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Status',
            'Email',
            'Contact',
            'Last Login At',
        ];
    }

    public function map($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->status ?? '-',
            $user->email,
            $user->contact ?? '-',
            $user->last_login_at ? Carbon::parse($user->last_login_at)->format('Y-m-d') : '-',
        ];
    }

    // Apply cell styles to center the placeholders
    public function styles(Worksheet $sheet)
    {
        // Apply center alignment for all columns
        $sheet->getStyle('A1:F1000')->getAlignment()->setHorizontal('center'); // Modify range as needed
    }

    // Optional: Apply additional formatting after sheet creation
    // more styles to table and the data
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Auto-size columns for better readability
                foreach (range('A', 'F') as $column) {
                    $event->sheet->getColumnDimension($column)->setAutoSize(true);
                }
            }
        ];
    }
}

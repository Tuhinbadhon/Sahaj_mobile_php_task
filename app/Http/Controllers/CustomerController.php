<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
   
    public function index(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer|in:10,25,50,100',
            'page' => 'nullable|integer|min:1',
            'status' => 'nullable|string|in:all,active,pending,overdue,completed,rejected',
            'search' => 'nullable|string|max:100',
            'sort_by' => 'nullable|string|in:id,originate_date,applicant',
            'sort_order' => 'nullable|string|in:asc,desc'
        ]);

        $jsonData = $this->loadMockData();
        
        $allCustomers = collect($jsonData['data']);
        
        $allCustomers = $this->applySearchFilter($allCustomers, $request->search);
        
        $allCustomers = $this->applyStatusFilter($allCustomers, $request->status);
        
        $allCustomers = $this->applySorting(
            $allCustomers, 
            $request->get('sort_by', 'id'),
            $request->get('sort_order', 'asc')
        );
        
        $perPage = (int)$request->get('per_page', 10);
        $currentPage = (int)$request->get('page', 1);
        
        if ($currentPage < 1) {
            $currentPage = 1;
        }
        
        $totalRecords = $allCustomers->count();
        $totalPages = (int)ceil($totalRecords / $perPage);
        
        if ($currentPage > $totalPages && $totalPages > 0) {
            $currentPage = $totalPages;
        }
        
        $offset = ($currentPage - 1) * $perPage;
        
        $customers = $allCustomers->slice($offset, $perPage)->values();
        
        $pagination = [
            'total_records' => $totalRecords,
            'current_page' => (int)$currentPage,
            'per_page' => (int)$perPage,
            'total_pages' => $totalPages,
            'from' => $totalRecords > 0 ? $offset + 1 : 0,
            'to' => min($offset + $perPage, $totalRecords)
        ];
        
        return view('dashboard', [
            'customers' => $customers,
            'pagination' => $pagination,
        ]);
    }
    

    public function exportCSV(Request $request)
    {
        $request->validate([
            'status' => 'nullable|string|in:all,active,pending,overdue,completed,rejected',
            'search' => 'nullable|string|max:100',
            'sort_by' => 'nullable|string|in:id,originate_date,applicant',
            'sort_order' => 'nullable|string|in:asc,desc'
        ]);

        $jsonData = $this->loadMockData();
        
        $allCustomers = collect($jsonData['data']);
        
        $allCustomers = $this->applySearchFilter($allCustomers, $request->search);
        
        $allCustomers = $this->applyStatusFilter($allCustomers, $request->status);
        
        $allCustomers = $this->applySorting(
            $allCustomers,
            $request->get('sort_by', 'originate_date'),
            $request->get('sort_order', 'desc')
        );
        
        $csvData = [];
        
        $csvData[] = [
            'ID',
            'Originate Date',
            'Duration',
            'Package',
            'Applicant',
            'Telephone',
            'Shop Name',
            'Total Amount',
            'Installment',
            'Paid',
            'Due',
            'Last Payment Date',
            'Status'
        ];
        
        foreach ($allCustomers as $customer) {
            $csvData[] = [
                $customer['id'],
                $customer['originate_date'],
                $customer['month_week'],
                $customer['emi_package'],
                $customer['applicant'],
                $customer['telephone'],
                $customer['shop_name'],
                $customer['total_amount_display'],
                $customer['installment_display'],
                $customer['paid_display'],
                $customer['due_display'],
                $customer['last_pay_date'] ?? 'N/A',
                $customer['status']
            ];
        }
        
        $filename = 'sahaj_mobile_customers_' . date('Y-m-d_His') . '.csv';
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
    
    
    private function loadMockData()
    {
        $jsonPath = storage_path('data/OUTPUT.json');
        
        if (!file_exists($jsonPath)) {
            return [
                'response' => 'error',
                'status' => 0,
                'message' => 'Data file not found',
                'data' => []
            ];
        }
        
        $jsonContent = file_get_contents($jsonPath);
        return json_decode($jsonContent, true);
    }
    
  
    private function applySearchFilter($customers, $search)
    {
        if (empty($search)) {
            return $customers;
        }

        $searchTerm = strtolower($search);
        return $customers->filter(function($customer) use ($searchTerm) {
            return str_contains(strtolower($customer['applicant']), $searchTerm) ||
                   str_contains(strtolower($customer['telephone']), $searchTerm);
        });
    }


    private function applyStatusFilter($customers, $status)
    {
        if (empty($status) || $status === 'all') {
            return $customers;
        }

        return $customers->filter(function($customer) use ($status) {
            return strtolower($customer['status']) === strtolower($status);
        });
    }

 
    private function applySorting($customers, $sortBy, $sortOrder)
    {
        $sortCallback = function($customer) use ($sortBy) {
            $value = $customer[$sortBy] ?? '';
            
            if ($sortBy === 'originate_date' && !empty($value)) {
                return strtotime($value);
            }
            
            if ($sortBy === 'id') {
                return (int)$value;
            }
            
            return strtolower($value);
        };

        if ($sortOrder === 'desc') {
            return $customers->sortByDesc($sortCallback)->values();
        }
        
        return $customers->sortBy($sortCallback)->values();
    }
    
    
    public function getStatusOptions()
    {
        return config('app.emi_statuses', [
            'all' => 'All',
            'active' => 'Active',
            'pending' => 'Pending',
            'overdue' => 'Overdue',
            'completed' => 'Completed',
            'rejected' => 'Rejected'
        ]);
    }
}

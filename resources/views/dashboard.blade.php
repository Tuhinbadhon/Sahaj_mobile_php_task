<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sahaj Mobile - Customer EMI Management Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
        }
        .bg-gradient-primary {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        }
        .table thead th {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            white-space: nowrap;
        }
        .table tbody td {
            vertical-align: middle;
        }
        .sortable {
            cursor: pointer;
            user-select: none;
            transition: background-color 0.2s;
        }
        .sortable:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .sortable .sort-icon {
            font-size: 0.75rem;
            opacity: 0.6;
            margin-left: 0.25rem;
        }
        .sortable:hover .sort-icon {
            opacity: 1;
        }
        .card {
            border: none;
            border-radius: 0.5rem;
        }
        .badge {
            font-weight: 500;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }
        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }
        .page-link {
            border-radius: 0.375rem;
            margin: 0 0.125rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-0">
        <!-- Header -->
        <div class="bg-gradient-primary text-white py-4 shadow-sm mb-4">
            <div class="max-w-7xl mx-auto px-3">
                <h1 class="h3 mb-0 fw-bold">
                    <i class="bi bi-phone-fill me-2"></i>
                    Sahaj Mobile - Customer EMI Management
                </h1>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-3">
            <!-- Filters and Search -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('dashboard') }}" id="filterForm">
                        <div class="row g-3">
                            <!-- Search -->
                            <div class="col-md-4">
                                <label for="search" class="form-label">
                                    <i class="bi bi-search"></i> Search
                                </label>
                                <input 
                                    type="text" 
                                    class="form-control" 
                                    id="search" 
                                    name="search" 
                                    placeholder="Search by name or phone..." 
                                    value="{{ request('search') }}"
                                >
                            </div>

                            <!-- Status Filter -->
                            <div class="col-md-3">
                                <label for="status" class="form-label">
                                    <i class="bi bi-funnel"></i> Status
                                </label>
                                <select class="form-select" id="status" name="status">
                                    <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <!-- Records Per Page -->
                            <div class="col-md-2">
                                <label for="per_page" class="form-label">
                                    <i class="bi bi-list-ol"></i> Per Page
                                </label>
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>

                            <!-- Buttons -->
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="bi bi-funnel-fill"></i> Apply
                                </button>
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-arrow-clockwise"></i> Reset
                                </a>
                            </div>
                        </div>

                        <!-- Hidden fields for sorting -->
                        <input type="hidden" name="sort_by" id="sort_by" value="{{ request('sort_by', 'originate_date') }}">
                        <input type="hidden" name="sort_order" id="sort_order" value="{{ request('sort_order', 'desc') }}">
                        <input type="hidden" name="page" id="current_page" value="{{ request('page', 1) }}">
                    </form>
                </div>
            </div>

            <!-- Results Info and Export Buttons -->
            <div class="row mb-3 align-items-center">
                <div class="col-md-6">
                    <p class="text-muted mb-0">
                        Showing {{ $pagination['from'] }} to {{ $pagination['to'] }} of {{ $pagination['total_records'] }} entries
                    </p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('export.csv', request()->all()) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export All to CSV
                    </a>
                    <span class="text-muted small ms-2">
                        Page {{ $pagination['current_page'] }} of {{ $pagination['total_pages'] }}
                    </span>
                </div>
            </div>

            <!-- Customer Table -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="sortable" data-sort="id">
                                        ID 
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th class="sortable" data-sort="originate_date">
                                        Originate Date 
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th>Duration</th>
                                    <th>Package</th>
                                    <th class="sortable" data-sort="applicant">
                                        Applicant 
                                        <i class="bi bi-arrow-down-up sort-icon"></i>
                                    </th>
                                    <th>Telephone</th>
                                    <th>Shop Name</th>
                                    <th class="text-end">Total Amount</th>
                                    <th class="text-end">Installment</th>
                                    <th class="text-end">Paid</th>
                                    <th class="text-end">Due</th>
                                    <th>Last Payment</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $customer['id'] }}</td>
                                    <td>{{ date('d M Y', strtotime($customer['originate_date'])) }}</td>
                                    <td>{{ $customer['month_week'] }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $customer['emi_package'] }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong>{{ $customer['applicant'] }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <i class="bi bi-telephone"></i> {{ $customer['telephone'] }}
                                        </span>
                                    </td>
                                    <td>{{ $customer['shop_name'] }}</td>
                                    <td class="text-end">
                                        <strong>{{ $customer['total_amount_display'] }}</strong>
                                    </td>
                                    <td class="text-end">{{ $customer['installment_display'] }}</td>
                                    <td class="text-end text-success">{{ $customer['paid_display'] }}</td>
                                    <td class="text-end text-danger">{{ $customer['due_display'] }}</td>
                                    <td>
                                        @if($customer['last_pay_date'])
                                            {{ date('d M Y', strtotime($customer['last_pay_date'])) }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $statusClass = match(strtolower($customer['status_color'])) {
                                                'success' => 'bg-success',
                                                'warning' => 'bg-warning text-dark',
                                                'danger' => 'bg-danger',
                                                'info' => 'bg-info text-dark',
                                                'secondary' => 'bg-secondary',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $customer['status'] }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="13" class="text-center py-5">
                                        <i class="bi bi-inbox display-1 text-muted"></i>
                                        <p class="text-muted mt-3">No customers found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            @if($pagination['total_pages'] > 1)
            <div class="mt-4 mb-5">
                <nav aria-label="Customer pagination">
                    <ul class="pagination justify-content-center">
                        <!-- Previous -->
                        <li class="page-item {{ $pagination['current_page'] <= 1 ? 'disabled' : '' }}">
                            <a class="page-link" href="#" onclick="changePage({{ $pagination['current_page'] - 1 }}); return false;">
                                <i class="bi bi-chevron-left"></i> Previous
                            </a>
                        </li>

                        <!-- Page Numbers -->
                        @php
                            $start = max(1, $pagination['current_page'] - 2);
                            $end = min($pagination['total_pages'], $pagination['current_page'] + 2);
                        @endphp

                        @if($start > 1)
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="changePage(1); return false;">1</a>
                            </li>
                            @if($start > 2)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                        @endif

                        @for($i = $start; $i <= $end; $i++)
                            <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                <a class="page-link" href="#" onclick="changePage({{ $i }}); return false;">
                                    {{ $i }}
                                </a>
                            </li>
                        @endfor

                        @if($end < $pagination['total_pages'])
                            @if($end < $pagination['total_pages'] - 1)
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            @endif
                            <li class="page-item">
                                <a class="page-link" href="#" onclick="changePage({{ $pagination['total_pages'] }}); return false;">
                                    {{ $pagination['total_pages'] }}
                                </a>
                            </li>
                        @endif

                        <!-- Next -->
                        <li class="page-item {{ $pagination['current_page'] >= $pagination['total_pages'] ? 'disabled' : '' }}">
                            <a class="page-link" href="#" onclick="changePage({{ $pagination['current_page'] + 1 }}); return false;">
                                Next <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <footer class="bg-light border-top py-4 mt-5">
            <div class="container text-center">
                <p class="text-muted mb-0 small">
                    <i class="bi bi-c-circle me-1"></i>
                    2025 Sahaj Mobile - Customer EMI Management System
                </p>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sorting functionality
        document.querySelectorAll('.sortable').forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', function() {
                const sortBy = this.dataset.sort;
                const currentSortBy = document.getElementById('sort_by').value;
                const currentSortOrder = document.getElementById('sort_order').value;
                
                // Toggle sort order if clicking the same column
                let newSortOrder = 'asc';
                if (currentSortBy === sortBy && currentSortOrder === 'asc') {
                    newSortOrder = 'desc';
                }
                
                document.getElementById('sort_by').value = sortBy;
                document.getElementById('sort_order').value = newSortOrder;
                document.getElementById('current_page').value = 1; // Reset to first page
                document.getElementById('filterForm').submit();
            });
        });

        // Pagination
        function changePage(page) {
            if (page < 1 || page > {{ $pagination['total_pages'] }}) {
                return false;
            }
            document.getElementById('current_page').value = page;
            document.getElementById('filterForm').submit();
        }

        // Auto-submit on filter changes
        document.getElementById('status').addEventListener('change', function() {
            document.getElementById('current_page').value = 1;
            document.getElementById('filterForm').submit();
        });

        document.getElementById('per_page').addEventListener('change', function() {
            document.getElementById('current_page').value = 1;
            document.getElementById('filterForm').submit();
        });

        // Highlight current sort column
        document.addEventListener('DOMContentLoaded', function() {
            const currentSortBy = document.getElementById('sort_by').value;
            const currentSortOrder = document.getElementById('sort_order').value;
            
            document.querySelectorAll('.sortable').forEach(header => {
                if (header.dataset.sort === currentSortBy) {
                    header.classList.add('table-active');
                    const icon = header.querySelector('.sort-icon');
                    if (currentSortOrder === 'asc') {
                        icon.classList.remove('bi-arrow-down-up');
                        icon.classList.add('bi-arrow-up');
                    } else {
                        icon.classList.remove('bi-arrow-down-up');
                        icon.classList.add('bi-arrow-down');
                    }
                }
            });
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users Table</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-xl font-semibold text-gray-800">Users</h3>
                <button id="addNewUser" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded">
                    + Add New User
                </button>
            </div>
            <div class="p-6">
                <div class="mb-4">
                    <label for="role-filter" class="block text-sm font-medium text-gray-700">Filter by Role:</label>
                    <div class="flex items-center space-x-2">
                        <select id="role-filter" multiple class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                            @foreach($roles as $role)
                            <option value="{{ $role }}">{{ $role }}</option>
                            @endforeach
                        </select>
                        <button id="clear-filters" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded">
                            Clear Filters
                        </button>
                    </div>
                </div>
                <table id="users-table" class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Roles</th>
                            <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
    $(function() {
        let table = $('#users-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('test.paginate1') }}",
                data: function (d) {
                    d.roles = $('#role-filter').val();
                }
            },
            columns: [
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'roles', name: 'roles.name'},
                {data: 'action', name: 'action', orderable: false, searchable: false}
            ],
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                paginate: {
                    previous: "Previous",
                    next: "Next"
                },
                info: "Page _PAGE_ of _PAGES_"
            },
            dom: '<"flex justify-between items-center mb-4"<"flex items-center"l><"flex items-center"f>>rt<"flex justify-between items-center mt-4"ip>',
            pagingType: "simple",
            pageLength: 10,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            drawCallback: function(settings) {
                var api = this.api();
                var pageInfo = api.page.info();
                $('.dataTables_info').html('Page ' + (pageInfo.page + 1) + ' of ' + pageInfo.pages);
            }
        });

        // Role filter change event
        $('#role-filter').on('change', function() {
            updateFilterVisibility();
            table.ajax.reload();
        });

        // Clear filters button click event
        $('#clear-filters').on('click', function() {
            $('#role-filter').val(null).trigger('change');
            table.ajax.reload();
        });

        // Function to update filter visibility
        function updateFilterVisibility() {
            var filterValues = $('#role-filter').val();
            if (filterValues && filterValues.length > 0) {
                $('#clear-filters').show();
            } else {
                $('#clear-filters').hide();
            }
        }

        // Initial filter visibility
        updateFilterVisibility();

        // Custom styling for search input
        $('.dataTables_filter input').addClass('border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50');

        // Custom styling for length select
        $('.dataTables_length select').addClass('border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50');

        // Add New User button click event
        $('#addNewUser').on('click', function() {
            // Add your logic for adding a new user here
            console.log('Add New User clicked');
        });

        // Export Data button click event
        $('#exportData').on('click', function() {
            // Add your logic for exporting data here
            console.log('Export Data clicked');
        });

        // Initialize Select2 for role filter if you're using it
        // Uncomment the following line if you've included Select2
        // $('#role-filter').select2();
    });
    </script>

</body>

</html>
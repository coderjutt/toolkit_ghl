@extends('admin.layouts.index')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Users Module</h1>
            <!-- Add Location Button -->
            @if(isset($userPermissions['Locationcustomizer']) && in_array('Add', $userPermissions['Locationcustomizer']))
                <button onclick="document.getElementById('locationModal').classList.remove('hidden')"
                    class="bg-blue-600 text-white px-4 py-2 rounded">
                    Add Location
                </button>
            @endif
        </div>

        {{-- Location Table --}}
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                <thead class="bg-gray-100 text-left text-xs uppercase font-semibold text-gray-600">
                    <tr>
                        <th class="px-6 py-3">Location ID</th>
                        <th class="px-6 py-3">Location Name</th>
                        <th class="px-6 py-3 text-center">Enable/Disable</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($locationcustomizer as $locCustom)
                        <tr>
                            <td class="px-6 py-4">{{ $locCustom->location_id }}</td>
                            <td class="px-6 py-4">{{ $locCustom->location }}</td>
                            <td class="px-6 py-4 text-center">
                                <input type="checkbox" class="enable-checkbox" data-id="{{ $locCustom->id }}" {{ $locCustom->Enable ? 'checked' : '' }}>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <!-- Custom CSS Button -->
                                <button onclick="openCustomCssModal({{ $locCustom->id }})"
                                    class="bg-yellow-400 text-white px-3 py-1 rounded">Custom CSS</button>

                                @if(isset($userPermissions['Locationcustomizer']) && in_array('Edit', $userPermissions['Locationcustomizer']))
                                    <!-- Edit Button -->
                                    <button onclick="openEditModal({{ $locCustom->id }})"
                                        class="bg-blue-500 text-white px-3 py-1 rounded">Edit</button>
                                @endif

                                <!-- Delete Button -->
                                @if(isset($userPermissions['Locationcustomizer']) && in_array('Delete', $userPermissions['Locationcustomizer']))
                                    <form action="{{ route('admin.loc_custom.destroy', $locCustom->id) }}" method="POST"
                                        class="inline-block" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded">
                                            <i class="fa fa-trash" style="font-size:14px"></i>
                                        </button>
                                    </form>
                                 @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Location Modal -->
    <div id="locationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">
            <h2 class="text-xl font-bold mb-4">Add Location</h2>

            <form action="{{ route('admin.location_customizer.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Location ID</label>
                    <input type="text" name="location_id" class="w-full border-gray-300 rounded px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Location Name</label>
                    <input type="text" name="location" class="w-full border-gray-300 rounded px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="Enable" value="1" class="form-checkbox">
                        <span class="ml-2">Enable</span>
                    </label>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                    <button type="button" onclick="document.getElementById('locationModal').classList.add('hidden')"
                        class="ml-2 px-4 py-2 rounded border">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Location Modal -->
    <div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">
            <h2 class="text-xl font-bold mb-4">Edit Location</h2>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_id" name="id">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Location ID</label>
                    <input type="text" id="edit_location_id" name="location_id" class="w-full border-gray-300 rounded px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Location Name</label>
                    <input type="text" id="edit_location" name="location" class="w-full border-gray-300 rounded px-4 py-2">
                </div>
                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="edit_Enable" name="Enable" value="1" class="form-checkbox">
                        <span class="ml-2">Enable</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Update</button>
                    <button type="button" onclick="closeEditModal()"
                        class="ml-2 px-4 py-2 rounded border">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Custom CSS Modal -->
    <div id="customCssModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">
            <h2 class="text-xl font-bold mb-4">Custom CSS for Location</h2>
            <form id="customCssForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="customCssLocationId">
                <!-- All your CSS fields (as you already added) -->
                <!-- ... existing custom CSS fields ... -->
                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                    <button type="button" onclick="closeCustomCssModal()"
                        class="ml-2 px-4 py-2 rounded border">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Toggle Enable
        document.querySelectorAll('.enable-checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                let id = this.dataset.id;
                let enable = this.checked ? 1 : 0;

                fetch("{{ route('admin.location_customizer.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({ id: id, enable: enable })
                })
                .then(res => res.json())
                .then(data => {
                    if (!data.success) alert('Failed to update');
                })
                .catch(err => console.error(err));
            });
        });

        // Open Edit Modal
        function openEditModal(id) {
            fetch(`/admin/location-customizer/${id}/edit`)
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('edit_id').value = data.location.id;
                        document.getElementById('edit_location_id').value = data.location.location_id;
                        document.getElementById('edit_location').value = data.location.location;
                        document.getElementById('edit_Enable').checked = data.location.Enable == 1;
                        document.getElementById('editForm').action = `/admin/location-customizer/${id}`;
                        document.getElementById('editModal').classList.remove('hidden');
                    }
                });
        }
        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        // Custom CSS Modal
        function openCustomCssModal(locationId) {
            document.getElementById('customCssModal').classList.remove('hidden');
            document.getElementById('customCssLocationId').value = locationId;
            // fetch existing CSS code...
        }
        function closeCustomCssModal() {
            document.getElementById('customCssModal').classList.add('hidden');
        }
    </script>
@endsection

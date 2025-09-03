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
                                    <button onclick="deleteLocation({{ $locCustom->id }})"
                                        class="bg-red-600 text-white px-3 py-1 rounded">Delete</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add/Edit Location Modal -->
    <div id="locationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">
            <h2 class="text-xl font-bold mb-4">Add / Edit Location</h2>

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

    <!-- Custom CSS Modal -->
    <div id="customCssModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">
            <h2 class="text-xl font-bold mb-4">Custom CSS for Location</h2>

            <form id="customCssForm" method="POST">
                @csrf
                <input type="hidden" name="id" id="customCssLocationId">

                <div class="grid grid-cols-2 gap-4">
                    <!-- Existing fields -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Card Header Background</label>
                        <input type="color" name="card_header_background" id="card_header_background"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Card Header Color</label>
                        <input type="color" name="card_header_color" id="card_header_color" class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Top Header Icon Background</label>
                        <input type="color" name="top_header_icon_background" id="top_header_icon_background"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Top Header Icon Color</label>
                        <input type="color" name="top_header_icon_color" id="top_header_icon_color"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Background</label>
                        <input type="color" name="navebar_background" id="navebar_background" class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Color</label>
                        <input type="color" name="navebar_color" id="navebar_color" class="w-full h-10 rounded">
                    </div>

                    <!-- New fields -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Grouped Background</label>
                        <input type="color" name="navebar_grouped_background" id="navebar_grouped_background"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Grouped Color</label>
                        <input type="color" name="navebar_grouped_color" id="navebar_grouped_color"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Item Active Background</label>
                        <input type="color" name="navebar_item_active_background" id="navebar_item_active_background"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Item Active Color</label>
                        <input type="color" name="navebar_item_active_color" id="navebar_item_active_color"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Item Inactive Background</label>
                        <input type="color" name="navebar_item_inactive_background" id="navebar_item_inactive_background"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Item Inactive Color</label>
                        <input type="color" name="navebar_item_inactive_color" id="navebar_item_inactive_color"
                            class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Image Color</label>
                        <input type="color" name="navebar_image_color" id="navebar_image_color" class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Navbar Image Hover</label>
                        <input type="color" name="navebar_image_hover" id="navebar_image_hover" class="w-full h-10 rounded">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Item Border Radius (px)</label>
                        <input type="number" name="item_border_radius" id="item_border_radius" class="w-full h-10 rounded"
                            min="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Live Preview</label>
                        <select name="live_privew" id="live_privew" class="w-full h-10 rounded border">
                            <option value="0">Disabled</option>
                            <option value="1">Enabled</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium mb-1">Custom CSS</label>
                    <textarea name="custom_css" id="custom_css" rows="4" class="w-full border rounded px-4 py-2"></textarea>
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                    <button type="button" onclick="closeCustomCssModal()"
                        class="ml-2 px-4 py-2 rounded border">Cancel</button>
                </div>
            </form>

        </div>
    </div>

    <script>
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
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) console.log('Enable updated successfully');
                        else alert('Failed to update');
                    })
                    .catch(error => console.error('Error:', error));
            });
        });
        // Open modal and fetch existing CSS
        function openCustomCssModal(locationId) {
            document.getElementById('customCssModal').classList.remove('hidden');
            document.getElementById('customCssLocationId').value = locationId;

            fetch(`/admin/custom_css/${locationId}`) // GET
                .then(res => res.json())
                .then(data => {
                    document.getElementById('card_header_background').value = data.card_header_background || '#ffffff';
                    document.getElementById('card_header_color').value = data.card_header_color || '#000000';
                    document.getElementById('top_header_icon_background').value = data.top_header_icon_background || '#ffffff';
                    document.getElementById('top_header_icon_color').value = data.top_header_icon_color || '#000000';
                    document.getElementById('navebar_background').value = data.navebar_background || '#ffffff';
                    document.getElementById('navebar_color').value = data.navebar_color || '#000000';
                    document.getElementById('navebar_grouped_background').value = data.navebar_grouped_background || '#ffffff';
                    document.getElementById('navebar_grouped_color').value = data.navebar_grouped_color || '#000000';
                    document.getElementById('navebar_item_active_background').value = data.navebar_item_active_background || '#ffffff';
                    document.getElementById('navebar_item_active_color').value = data.navebar_item_active_color || '#000000';
                    document.getElementById('navebar_item_inactive_background').value = data.navebar_item_inactive_background || '#ffffff';
                    document.getElementById('navebar_item_inactive_color').value = data.navebar_item_inactive_color || '#000000';
                    document.getElementById('navebar_image_color').value = data.navebar_image_color || '#000000';
                    document.getElementById('navebar_image_hover').value = data.navebar_image_hover || '#000000';
                    document.getElementById('item_border_radius').value = data.item_border_radius || 0;
                    document.getElementById('live_privew').value = data.live_privew || 0;
                    document.getElementById('custom_css').value = data.custom_css || '';
                })
                .catch(err => console.error('Error fetching custom CSS:', err));
        }

        // Close modal
        function closeCustomCssModal() {
            document.getElementById('customCssModal').classList.add('hidden');
        }

        // Submit form via AJAX
        document.getElementById('customCssForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const locationId = document.getElementById('customCssLocationId').value;
            const formData = new FormData(this);

            fetch(`/admin/custom_css/${locationId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value },
                body: formData
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        alert('Custom CSS saved successfully!');
                        closeCustomCssModal();
                    }
                })
                .catch(err => console.error('Error saving custom CSS:', err));
        });

    </script>
@endsection
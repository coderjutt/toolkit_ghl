@extends('admin.layouts.index')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Location Customizer</h1>
            @if(isset($userPermissions['Locationcustomizer']) && in_array('Add', $userPermissions['Locationcustomizer']))
                <button onclick="openLocationModal()" class="bg-blue-600 text-white px-4 py-2 rounded">
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

                                <!-- Edit Button -->
                                @if(isset($userPermissions['Locationcustomizer']) && in_array('Edit', $userPermissions['Locationcustomizer']))
                                    <button onclick='openLocationModal(@json($locCustom))'
                                        class="bg-green-600 text-white px-3 py-1 rounded">Edit</button>
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

    <!-- Add/Edit Location Modal -->
    <div id="locationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-6">
            <h2 id="locationModalTitle" class="text-xl font-bold mb-4">Add Location</h2>

            <form id="locationForm" method="POST">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <input type="hidden" name="id" id="location_id_hidden">

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Location ID</label>
                    <input type="text" name="location_id" id="location_id_input"
                        class="w-full border-gray-300 rounded px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Location Name</label>
                    <input type="text" name="location" id="location_name_input"
                        class="w-full border-gray-300 rounded px-4 py-2">
                </div>

                <div class="mb-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="Enable" id="location_enable_input" value="1" class="form-checkbox">
                        <span class="ml-2">Enable</span>
                    </label>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save</button>
                    <button type="button" onclick="document.getElementById('locationModal').classList.add('hidden')"
                        class="ml-2 px-4 py-2 rounded border">Cancel
                    </button>
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
                    <!-- Your CSS fields (no changes from your version) -->
                    <!-- ... -->
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium mb-1">Custom CSS</label>
                    <textarea id="custom_css" rows="4" class="w-full border rounded px-4 py-2"></textarea>
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
        function openCustomCssModal(locationId) {
            document.getElementById('customCssModal').classList.remove('hidden');
            document.getElementById('customCssLocationId').value = locationId;
            const loader = document.getElementById("formLoader"); // loader reference

            fetch(`/admin/custom_css/${locationId}`)
                .then(res => res.json())
                .then(data => {
                    let fields = {};
                    try {
                        fields = JSON.parse(data.form_css || '{}');
                    } catch (e) { fields = {}; }

                    // populate fields safely
                    document.getElementById('form_css').value = fields.form_css || '#ffffff';
                    document.getElementById('card_header_color').value = fields.card_header_color || '#000000';
                    // ... other fields
                    document.getElementById('custom_css').value = data.custom_css || '';
                })
                .catch(err => console.error('Error fetching custom CSS:', err));
        }

        function closeCustomCssModal() {
            document.getElementById('customCssModal').classList.add('hidden');
        }

        document.getElementById('customCssForm').addEventListener('submit', function (e) {
            e.preventDefault();
            const locationId = document.getElementById('customCssLocationId').value;

            const jsonFields = {
                form_css: document.getElementById('form_css').value,
                card_header_color: document.getElementById('card_header_color').value,
                // ... other fields
            };

            const payload = {
                custom_css: document.getElementById('custom_css').value,
                ...jsonFields
            };

            fetch(`/admin/custom_css/${locationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify(payload)
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

        function openLocationModal(data = null) {

            const modal = document.getElementById('locationModal');
            const form = document.getElementById('locationForm');
            const title = document.getElementById('locationModalTitle');
            const methodInput = document.getElementById('formMethod');
            const hiddenId = document.getElementById('location_id_hidden');

            if (data) {
                // Edit mode
                title.textContent = "Edit Location";
                form.action = `/admin/location_customizer/${data.id}`;
                methodInput.value = "PUT";
                hiddenId.value = data.id;

                document.getElementById('location_id_input').value = data.location_id ?? '';
                document.getElementById('location_name_input').value = data.location ?? '';
                document.getElementById('location_enable_input').checked = data.Enable == 1;
            } else {
                // Add mode
                title.textContent = "Add Location";
                form.action = "{{ route('admin.location_customizer.store') }}";
                methodInput.value = "POST";
                hiddenId.value = "";

                document.getElementById('location_id_input').value = '';
                document.getElementById('location_name_input').value = '';
                document.getElementById('location_enable_input').checked = false;
            }
            // Loader bind (sirf ek dafa lagana hai)
            if (!form.dataset.loaderBound) {
                form.addEventListener("submit", function () {
                    loader.classList.remove("hidden");
                });
                form.dataset.loaderBound = true; // dobara na lage
            }

            modal.classList.remove('hidden');
        }
        $(document).on('change', '.enable-checkbox', function () {
            let id = $(this).data('id');
            let enable = $(this).is(':checked') ? 1 : 0;

            $.ajax({
                url: "{{ route('admin.location_customizer.toggle') }}",
                type: "POST",
                data: {
                    id: id,
                    enable: enable,
                    _token: "{{ csrf_token() }}"
                },
                success: function (res) {
                    console.log("Updated:", res);
                },
                error: function (err) {
                    console.error(err);
                }
            });
        });

    </script>
    <div id="formLoader" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50 hidden">
        <div class="flex flex-col items-center">
            <svg class="animate-spin h-10 w-10 text-white mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 
                                                                        0 0 5.373 0 12h4z"></path>
            </svg>
            <span class="text-white text-lg">......</span>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const loader = document.getElementById("formLoader");

            // Har form ke liye listener lagao
            document.querySelectorAll("form").forEach(form => {
                form.addEventListener("submit", function () {
                    loader.classList.remove("hidden"); // loader show
                });
            });
        });
    </script>
@endsection
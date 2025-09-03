@extends('admin.layouts.index')

@section('content')
    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded relative bg-green-100 border border-green-400 text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 px-4 py-3 rounded relative bg-red-100 border border-red-400 text-red-700">
            {{ session('error') }}
        </div>
    @endif

    <div x-data="{ open: false, openEdit: false, editData: {} }" class="max-w-7xl mx-auto px-4 py-6">
        @if(isset($userPermissions['Custommenulink']) && in_array('Add', $userPermissions['Custommenulink']))
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">Custom Menu Links</h1>
                <!-- Add Custom Menu Link Button -->
                <button @click="open=true" class="bg-blue-600 text-white px-4 py-2 rounded">
                    + Add CustomMenuLink
                </button>
            </div>
        @endif

        {{-- Table --}}
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                <thead class="bg-gray-100 text-left text-xs uppercase font-semibold text-gray-600">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Allowed Emails</th>
                        <th class="px-6 py-3">Restricted Emails</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($CMLink as $link)
                        <tr>
                            <td class="px-6 py-4">{{ $link->Title }}</td>
                            <td class="px-6 py-4">
                                @if(is_array($link->allowed_emails))
                                    {{ implode(', ', $link->allowed_emails) }}
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if(is_array($link->restricted_email))
                                    {{ implode(', ', $link->restricted_email) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(isset($userPermissions['Custommenulink']) && in_array('Edit', $userPermissions['Custommenulink']))
                                    <button class="text-blue-600 hover:underline"
                                        @click="openEdit = true; editData = {{ json_encode($link) }}">Edit</button>
                                @endif
                                @if(isset($userPermissions['Custommenulink']) && in_array('Delete', $userPermissions['Custommenulink']))
                                    <form action="{{ route('admin.custom-menu-links.destroy', $link->id) }}" method="POST"
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
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-gray-500">No custom menu links found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Add Modal -->
        <div x-show="open" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div @click.away="open = false" class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Add Custom Menu Link</h2>

                <form id="customMenuForm" class="space-y-4">
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" name="Title" class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
                    </div>

                    <!-- Allowed Emails -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Allowed Emails</label>
                        <input id="allowed_emails" name="allowed_emails" placeholder="Enter emails"
                            class="w-full border rounded-lg p-2">
                    </div>

                    <!-- Restricted Emails -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Restricted Emails</label>
                        <input id="restricted_email" name="restricted_email" placeholder="Enter emails"
                            class="w-full border rounded-lg p-2">
                    </div>

                    <!-- Action Dropdown -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Action</label>
                        <select name="action" class="w-full border rounded-lg p-2">
                            <option value="new_tab">New Tab</option>
                            <option value="same_tab">Same Tab</option>
                            <option value="iframe">Iframe</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="open = false"
                            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Modal -->
        <div x-show="openEdit" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div @click.away="openEdit = false" class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Update Custom Menu Link</h2>

                <form id="updateMenuForm" class="space-y-4">
                    <input type="hidden" name="id" x-model="editData.id">

                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" name="Title" x-model="editData.Title"
                            class="w-full border rounded-lg p-2 focus:ring focus:ring-blue-300">
                    </div>

                    <!-- Allowed Emails -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Allowed Emails</label>
                        <input id="edit_allowed_emails" name="allowed_emails" class="w-full border rounded-lg p-2">
                    </div>

                    <!-- Restricted Emails -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Restricted Emails</label>
                        <input id="edit_restricted_email" name="restricted_email" class="w-full border rounded-lg p-2">
                    </div>

                    <!-- Action Dropdown -->
                    <div>
                        <label class="block text-sm font-medium mb-1">Action</label>
                        <select name="action" x-model="editData.action" class="w-full border rounded-lg p-2">
                            <option value="new_tab">New Tab</option>
                            <option value="same_tab">Same Tab</option>
                            <option value="iframe">Iframe</option>
                        </select>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end space-x-2 mt-4">
                        <button type="button" @click="openEdit = false"
                            class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                            Update
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Include Tagify -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.9/tagify.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.9/tagify.css" />

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Tagify for Add
            let allowedTagify = new Tagify(document.querySelector("#allowed_emails"));
            let restrictedTagify = new Tagify(document.querySelector("#restricted_email"));

            // Add submit
            document.getElementById("customMenuForm").addEventListener("submit", async function (e) {
                e.preventDefault();

                let formData = {
                    Title: this.Title.value,
                    allowed_emails: allowedTagify.value.map(tag => tag.value),
                    restricted_email: restrictedTagify.value.map(tag => tag.value),
                    action: this.action.value
                };

                let response = await fetch("{{ route('admin.custom-menu-links.store') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(formData)
                });

                let result = await response.json();
                if (result.success) {
                    alert("Saved successfully!");
                    location.reload();
                } else {
                    alert("Error saving data");
                }
            });

            // Tagify for Edit
            let editAllowedTagify = new Tagify(document.querySelector("#edit_allowed_emails"));
            let editRestrictedTagify = new Tagify(document.querySelector("#edit_restricted_email"));

            // Update submit
            document.getElementById("updateMenuForm").addEventListener("submit", async function (e) {
                e.preventDefault();

                let id = this.id.value;
                let formData = {
                    Title: this.Title.value,
                    allowed_emails: editAllowedTagify.value.map(tag => tag.value),
                    restricted_email: editRestrictedTagify.value.map(tag => tag.value),
                    action: this.action.value
                };

                let response = await fetch("{{ url('admin/custom-menu-links') }}/" + id, {
                    method: "PUT",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify(formData)
                });

                let result = await response.json();
                if (result.success) {
                    alert("Updated successfully!");
                    location.reload();
                } else {
                    alert("Error updating data");
                }
            });

            // Prefill edit Tagify when Edit modal opens
            document.addEventListener("click", function (e) {
                if (e.target.matches("button.text-blue-600")) {
                    let data = JSON.parse(e.target.getAttribute("@click").split("editData = ")[1]);

                    editAllowedTagify.removeAllTags();
                    editRestrictedTagify.removeAllTags();

                    if (data.allowed_emails) {
                        editAllowedTagify.addTags(data.allowed_emails);
                    }
                    if (data.restricted_email) {
                        editRestrictedTagify.addTags(data.restricted_email);
                    }
                }
            });
        });
    </script>
@endsection
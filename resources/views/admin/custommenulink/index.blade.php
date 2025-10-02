@extends('admin.layouts.index')

@section('content')
    <div x-data="customMenuManager()" class="max-w-7xl mx-auto px-4 py-6">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 px-4 py-3 rounded bg-green-100 border border-green-400 text-green-700">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 px-4 py-3 rounded bg-red-100 border border-red-400 text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold">Custom Menu Links</h1>
            <button @click="openAdd=true" class="bg-blue-600 text-white px-4 py-2 rounded">+ Add CustomMenuLink</button>
        </div>

        <!-- Table -->
        <div class="bg-white shadow rounded-lg overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm text-gray-700">
                <thead class="bg-gray-100 text-left text-xs uppercase font-semibold text-gray-600">
                    <tr>
                        <th class="px-6 py-3">Title</th>
                        <th class="px-6 py-3">Restricted Emails</th>
                        <th class="px-6 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($CMLink as $link)
                        <tr>
                            <td class="px-6 py-4">{{ $link->Title }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $restricted = is_string($link->restricted_email)
                                        ? json_decode($link->restricted_email, true)
                                        : $link->restricted_email;
                                @endphp
                                @if(is_array($restricted))
                                    {{ implode(', ', $restricted) }}
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if(isset($userPermissions['Custommenulink']) && in_array('Delete', $userPermissions['Custommenulink']))
                                    <button class="text-white bg-blue-600 px-3 py-1 rounded"
                                        @click="openUpdate=true; loadData({{ json_encode($link) }})">
                                        Edit
                                    </button>
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
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- ADD Modal -->
        <div x-show="openAdd" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Add Custom Menu Link</h2>

                <!-- Checkbox -->
                <label class="inline-flex items-center mb-4">
                    <input type="checkbox" x-model="formAdd.useTitleDropdown" class="form-checkbox text-blue-600">
                    <span class="ml-2">Use Title Dropdown instead of Text + URL</span>
                </label>
                @php
                    $titles = ['Dashboard', 'Reports', 'Settings'];
                @endphp
                <!-- Title Dropdown -->
                <select x-show="formAdd.useTitleDropdown" x-model="formAdd.Title" class="w-full border rounded-lg p-2 mb-3">
                    <option value="">-- Select Title --</option>
                    @foreach ($titles as $title)
                        <option value="{{ $title }}">{{ $title }}</option>
                    @endforeach
                </select>

                <!-- Title + URL -->
                <template x-if="!formAdd.useTitleDropdown">
                    <div>
                        <label class="block text-sm font-medium mb-1">Title</label>
                        <input type="text" x-model="formAdd.Title" class="w-full border rounded-lg p-2 mb-3">
                        <label class="block text-sm font-medium mb-1">Url</label>
                        <input type="text" x-model="formAdd.Url" class="w-full border rounded-lg p-2 mb-3">
                    </div>
                </template>

                <!-- Restricted Emails -->
                <label class="block text-sm font-medium mb-1">Restricted Emails</label>
                <input id="add_restricted_email" type="text" class="w-full border rounded-lg p-2 mb-3">

                <!-- Action -->
                <div x-show="!formAdd.useTitleDropdown">
                    <label class="block text-sm font-medium mb-1">Action</label>
                    <select x-model="formAdd.action" class="w-full border rounded-lg p-2 mb-4">
                        <option value="new_tab">New Tab</option>
                        <option value="same_tab">Same Tab</option>
                        <option value="iframe">Iframe</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-2">
                    <button @click="openAdd=false"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Cancel</button>
                    <button @click="submitAdd()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Save</button>
                </div>
            </div>
        </div>

        <!-- UPDATE Modal -->
        <div x-show="openUpdate" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white rounded-2xl shadow-lg w-full max-w-lg p-6">
                <h2 class="text-xl font-semibold mb-4">Update Custom Menu Link</h2>

                <!-- Checkbox -->
                <label class="inline-flex items-center mb-4">
                    <input type="checkbox" x-model="form.useTitleDropdownEdit" class="form-checkbox text-blue-600">
                    <span class="ml-2">Use Title Dropdown instead of Text + URL</span>
                </label>

                <!-- Title Dropdown -->
                <select x-show="form.useTitleDropdownEdit" x-model="form.Title" class="w-full border rounded-lg p-2 mb-3">
                    <option value="">-- Select Title --</option>
                    <option value="Dashboard">Dashboard</option>
                    <option value="Reports">Reports</option>
                    <option value="Settings">Settings</option>
                </select>

                <!-- Title + URL -->
                <template x-if="!form.useTitleDropdownEdit">
                    <div>
                        <!-- Title + URL fields -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Title</label>
                            <input type="text" x-model="form.Title" class="w-full border rounded-lg p-2 mb-3">
                            <label class="block text-sm font-medium mb-1">Url</label>
                            <input type="text" x-model="form.Url" class="w-full border rounded-lg p-2 mb-3">
                        </div>

                        <!-- Action dropdown -->
                        <div>
                            <label class="block text-sm font-medium mb-1">Action</label>
                            <select x-model="form.action" class="w-full border rounded-lg p-2 mb-4">
                                <option value="new_tab">New Tab</option>
                                <option value="same_tab">Same Tab</option>
                                <option value="iframe">Iframe</option>
                            </select>
                        </div>
                    </div>
                </template>


                <!-- Restricted Emails -->
                <label class="block text-sm font-medium mb-1">Restricted Emails</label>
                <input id="edit_restricted_email" type="text" class="w-full border rounded-lg p-2 mb-3">

                <!-- Action -->

                <!-- Buttons -->
                <div class="flex justify-end space-x-2">
                    <button @click="openUpdate=false"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">Cancel</button>
                    <!-- <button @click="submitUpdate()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">Update</button>
                    </div> -->

                    <button @click="submitUpdate()"
                        class="bg-green-600 text-white px-6 py-2 rounded flex items-center justify-center gap-2"
                        onclick="this.querySelector('.spinner').classList.remove('hidden'); this.querySelector('.btn-text').classList.add('hidden');">
                        <span class="btn-text">Update</span>
                        <svg class="spinner hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                            </circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 
                                                                  0 0 5.373 0 12h4z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Tagify -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.9/tagify.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/tagify/4.17.9/tagify.css" />

        <script>
            function customMenuManager() {
                let addRestrictedTagify, editRestrictedTagify;
                const loader = document.getElementById("formLoader"); // loader reference

                return {
                    openAdd: false,
                    openUpdate: false,
                    formAdd: {
                        Title: '',
                        Url: '',
                        restricted_email: [],
                        action: 'new_tab',
                        useTitleDropdown: false,
                    },
                    form: {
                        id: null,
                        Title: '',
                        Url: '',
                        restricted_email: [],
                        action: 'new_tab',
                        useTitleDropdownEdit: false,
                    },
                    init() {
                        addRestrictedTagify = new Tagify(document.querySelector("#add_restricted_email"), {
                            delimiters: " ,",
                        });

                        editRestrictedTagify = new Tagify(document.querySelector("#edit_restricted_email"), {
                            delimiters: " ,",
                        });
                    },

                    submitAdd() {
                        this.formAdd.restricted_email = addRestrictedTagify.value.map(tag => tag.value);

                        loader.classList.remove("hidden"); // loader show

                        fetch("{{ route('admin.custom-menu-links.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(this.formAdd)
                        })
                            .then(res => res.json())
                            .then(data => {
                                loader.classList.add("hidden"); // loader hide
                                if (data.success) {
                                  
                                    this.openAdd = false;
                                    window.location.reload();
                                }
                            })
                            .catch(() => loader.classList.add("hidden")); // error case
                    },

                    loadData(link) {
                        this.form.id = link.id;
                        this.form.Title = link.Title;
                        this.form.Url = link.Url;
                        this.form.restricted_email = Array.isArray(link.restricted_email)
                            ? link.restricted_email
                            : JSON.parse(link.restricted_email || "[]");
                        this.form.action = link.action ?? 'new_tab';
                        this.form.useTitleDropdownEdit = (link.Url === null);

                        editRestrictedTagify.removeAllTags();
                        editRestrictedTagify.addTags(this.form.restricted_email);
                    },

                    submitUpdate() {
                        this.form.restricted_email = editRestrictedTagify.value.map(tag => tag.value);

                        loader.classList.remove("hidden"); // loader show

                        fetch(`/admin/custom-menu-links/${this.form.id}`, {
                            method: "PUT",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(this.form)
                        })
                            .then(res => res.json())
                            .then(data => {
                                loader.classList.add("hidden"); // loader hide
                                if (data.success) {
                                   
                                    this.openUpdate = false;
                                    window.location.reload();
                                }
                            })
                            .catch(() => loader.classList.add("hidden")); // error case
                    }
                }
            }
      
      </script>


        <div id="formLoader" class="fixed inset-0 bg-gray-800 bg-opacity-60 flex items-center justify-center z-50 hidden">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-10 w-10 text-white mb-3" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 
                                                                0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-white text-lg">Saving...</span>
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
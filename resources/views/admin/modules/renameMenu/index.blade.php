@extends('admin.layouts.index')

@section('content')
<div class="max-w-full px-6 py-6 relative">
    <h2 class="text-2xl font-bold mb-6">Rename Menu</h2>

    <form id="renameMenuForm">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach ($menus as $menu)
                @php
                    $saved = $savedMenus[$menu] ?? null;

                @endphp
                <div class="bg-white border rounded-lg p-4 shadow-sm">
                    <label class="block font-semibold text-gray-700 mb-2">{{ $menu }}</label>

                    {{-- Label Rename --}}
                    <input type="text" name="menus[{{ $menu }}][label]"
                        value="{{ old("menus.$menu.label", $saved['renamed_menu'] ?? '') }}"
                        class="w-full border-gray-300 rounded px-4 py-2 mb-2 focus:outline-none focus:ring focus:border-blue-300"
                        placeholder="Rename for {{ $menu }}">

                    {{-- Image URL --}}
                    <input type="text" name="menus[{{ $menu }}][image]"
                        value="{{ old("menus.$menu.image", $saved['image_url'] ?? '') }}"
                        class="w-full border-gray-300 rounded px-4 py-2 focus:outline-none focus:ring focus:border-blue-300"
                        placeholder="Image URL for {{ $menu }}">
                </div>
            @endforeach
        </div>

        {{-- Floating Save Button --}}
        <div class="fixed bottom-6 right-6 z-50">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded shadow-lg">
                Save Changes
            </button>
        </div>
    </form>

    <div id="successMessage" class="hidden mt-6 text-green-600 font-semibold"></div>
</div>

{{-- JavaScript --}}
<script>
    document.getElementById('renameMenuForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch("{{ route('admin.renamemenu.store') }}", {
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('successMessage').textContent = 'Menu names saved successfully!';
                document.getElementById('successMessage').classList.remove('hidden');
            } else {
                alert('Something went wrong!');
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Failed to save. Please try again.');
        });
    });
</script>
@endsection

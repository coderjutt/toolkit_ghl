<div class="mb-3">
    <label class="block font-semibold "> Title <span class="text-red-600">*</span> </label>
    <input type="text" name="title" value="{{ old('title', $contact->title ?? '') }}" required
        class="w-full border rounded p-2">
</div>
<div class="mb-3 js-action-field">
    <label class="block font-semibold">Action</label>
    <input type="text" name="action" value='{{ old("action", $contact->action ?? "") }}'
        class="w-full border rounded p-2 js-tagify-action">
</div>
<div class="mb-3 js-url-field">
    <label class="block font-semibold">URL</label>
    <input type="url" name="url" value="{{ old('url', $contact->url ?? '') }}"
        class="w-full border rounded p-2 js-tagify-url">
    {{-- value='@json(isset($contact) ? collect(json_decode($contact->url ?? "[]", true))->pluck("value") : [])' --}}
</div>
<div class="mb-3 js-url-field flex items-center gap-2">
    <input type="checkbox" name="iframe" value="1" {{ old('iframe', $contact->iframe ?? false) ? 'checked' : '' }}>
    <label>Open in Iframe</label>
</div>

<div class="mb-3">
    <label class="block font-semibold">Classes</label>
    <input type="text" name="classes" value="{{ old('classes', $contact->classes ?? '') }}"
        class="w-full border rounded p-2">
</div>

<div class="mb-3">
    <label class="block font-semibold">Location id</label>
    <input type="text" name="locations" value="{{ old('locations', $contact->locations ?? '') }}"
        class="w-full border rounded p-2">
</div>

<div class="mb-3">
    <label class="block font-semibold">Folder</label>
    <select name="folder" class="w-full border rounded p-2">
        <option value="">No Folder</option>
        <option value="folder1" {{ (old('folder', $contact->folder ?? '') == 'folder1') ? 'selected' : '' }}>Folder 1
        </option>
        <option value="folder2" {{ (old('folder', $contact->folder ?? '') == 'folder2') ? 'selected' : '' }}>Folder 2
        </option>
    </select>
</div>

<div class="mb-3">
    <label class="block font-semibold">Color</label>
    <input type="color" name="color" value="{{ old('color', $contact->color ?? '#000000') }}"
        class="w-60 h-10 border rounded p-2">
</div>

<div class="mb-3">
    <label class="block font-semibold">Background</label>
    <input type="color" name="background" value="{{ old('background', $contact->background ?? '#ffffff') }}"
        class="w-60 h-10 border rounded p-2">
</div>

<div class="flex justify-end gap-2">
    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Add New</button>
    <button type="submit" name="back" value="1" class="px-4 py-2 bg-blue-600 text-white rounded">Add New & Back</button>
    <button type="button" @click="openCreate=false; openEdit=null" class="px-4 py-2 border rounded">Cancel</button>
</div>


@once
    <!-- Tagify CSS/JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css">
    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
@endonce

<script>
    (function () {
        function initTagify() {
            document.querySelectorAll('input.js-tagify-action').forEach(function (el) {
                if (el.tagify) return;
                el.tagify = new Tagify(el, {
                    whitelist: ["url", "tag"],
                    enforceWhitelist: false,
                    dropdown: {
                        enabled: 0,
                        maxItems: 10,
                        closeOnSelect: false
                    }
                });
                function toggleFields() {
                    let values = el.tagify.value.map(v => v.value);
                    let hideFields = values.includes("tag") && !values.includes("url");
                    document.querySelectorAll(".js-url-field").forEach(f => {
                        f.style.display = hideFields ? "none" : "block";
                    });
                    document.querySelectorAll(".js-iframe-field").forEach(f => {
                        f.style.display = hideFields ? "none" : "flex";
                    });
                }
                el.tagify.on("add", toggleFields);
                el.tagify.on("remove", toggleFields);
                toggleFields();
            });
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initTagify);
        } else {
            initTagify();
        }
    })();
</script>
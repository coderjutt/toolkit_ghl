<div class="mb-3">
    <label class="block font-semibold">Title <span class="text-red-600">*</span></label>
    <input type="text" name="title" value="{{ old('title', $contact->title ?? '') }}" required
        class="w-full border rounded p-2">
</div>

<!-- Action -->
<div class="mb-3 js-action-field">
    <label class="block font-semibold">Action</label>
    <select name="action" id="actionSelect" class="w-full border rounded p-2">
        <option value="">Select Action</option>
        <option value="url" {{ trim(strtolower(old('action', $contact->action ?? ''))) === 'url' ? 'selected' : '' }}>URL</option>
        <option value="tag" {{ trim(strtolower(old('action', $contact->action ?? ''))) === 'tag' ? 'selected' : '' }}>Tag</option>
    </select>
</div>

<!-- URL Field -->
<div class="mb-3 js-url-field">
    <label class="block font-semibold">URL</label>
    <input type="url" name="url" value="{{ old('url', $contact->url ?? '') }}" class="w-full border rounded p-2">
</div>

<!-- Iframe Field -->
<div class="mb-3 flex items-center gap-2 js-iframe-field">
    <input type="checkbox" name="iframe" value="1" {{ old('iframe', $contact->iframe ?? false) ? 'checked' : '' }}>
    <label>Open in Iframe</label>
</div>

<!-- Classes -->
<div class="mb-3">
    <label class="block font-semibold">Classes</label>
    <input type="text" name="classes" value="{{ old('classes', $contact->classes ?? '') }}" class="w-full border rounded p-2">
</div>

<!-- Location ID -->
<div class="mb-3">
    <label class="block font-semibold">Location ID</label>
    <input type="text" name="locations" value="{{ old('locations', $contact->locations ?? '') }}" class="w-full border rounded p-2">
</div>

<!-- Folder -->
<div class="mb-3">
    <label class="block font-semibold">Folder</label>
    <select name="folder" class="w-full border rounded p-2">
        <option value="">No Folder</option>
        <option value="folder1" {{ (old('folder', $contact->folder ?? '') == 'folder1') ? 'selected' : '' }}>Folder 1</option>
        <option value="folder2" {{ (old('folder', $contact->folder ?? '') == 'folder2') ? 'selected' : '' }}>Folder 2</option>
    </select>
</div>

<!-- Color -->
<div class="mb-3">
    <label class="block font-semibold">Color</label>
    <input type="color" name="color" value="{{ old('color', $contact->color ?? '#000000') }}"
        class="w-60 h-10 border rounded p-2">
</div>

<!-- Background -->
<div class="mb-3">
    <label class="block font-semibold">Background</label>
    <input type="color" name="background" value="{{ old('background', $contact->background ?? '#ffffff') }}"
        class="w-60 h-10 border rounded p-2">
</div>

<!-- Buttons -->
<div class="flex justify-end gap-2">
    @if(isset($editMode) && $editMode)
        <!-- Edit Mode -->
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Update</button>
        <button type="button" @click="openEdit=false" class="px-4 py-2 border rounded">Cancel</button>
    @else
        <!-- Create Mode -->
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Add New</button>
        <button type="submit" name="back" value="1" class="px-4 py-2 bg-blue-600 text-white rounded">Add New & Back</button>
        <button type="button" @click="openCreate=false" class="px-4 py-2 border rounded">Cancel</button>
    @endif
</div>

@once
    <!-- jQuery (if not already included) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endonce

<script>
   $(document).ready(function() {
        $(".js-action-field select").on("change", function() {
            const action = $(this).val().trim().toLowerCase();

            const urlFieldWrapper = $(".js-url-field");
            const urlInput = urlFieldWrapper.find("input[type='url']");

            const iframeWrapper = $(".js-iframe-field");
            const iframeInput = iframeWrapper.find("input[type='checkbox']");

            if(action === "tag") {
                urlFieldWrapper.hide();
                urlInput.prop("disabled", true);

                iframeWrapper.hide();
                iframeInput.prop("disabled", true);
            } else {
                urlFieldWrapper.show();
                urlInput.prop("disabled", false);

                iframeWrapper.show();
                iframeInput.prop("disabled", false);
            }
        });

        // On page load, run check
        $(".js-action-field select").trigger("change");
    });
</script>

@php
    $inputClass = 'w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500';
    $errorClass = 'mt-1 text-xs text-red-600';
    $labelClass = 'block text-sm font-medium text-gray-700 mb-1';
    $isEdit = $user !== null;
@endphp

<div class="space-y-5">

    <div>
        <label class="{{ $labelClass }}">Name</label>
        <input type="text" name="name"
               value="{{ old('name', $user?->name) }}"
               class="{{ $inputClass }} @error('name') border-red-400 @enderror"
               required>
        @error('name') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $labelClass }}">Email</label>
        <input type="email" name="email"
               value="{{ old('email', $user?->email) }}"
               class="{{ $inputClass }} @error('email') border-red-400 @enderror"
               required>
        @error('email') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $labelClass }}">
            Password
            @if ($isEdit)
                <span class="font-normal text-gray-400">(leave blank to keep current)</span>
            @endif
        </label>
        <input type="password" name="password"
               class="{{ $inputClass }} @error('password') border-red-400 @enderror"
               autocomplete="{{ $isEdit ? 'new-password' : 'new-password' }}"
               @if (!$isEdit) required @endif>
        @error('password') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="{{ $labelClass }}">Confirm Password</label>
        <input type="password" name="password_confirmation"
               class="{{ $inputClass }}"
               @if (!$isEdit) required @endif>
    </div>

    <div>
        <label class="{{ $labelClass }}">Office</label>
        <select name="office_id"
                class="{{ $inputClass }} @error('office_id') border-red-400 @enderror"
                required>
            <option value="">— select office —</option>
            @foreach ($offices as $office)
                <option value="{{ $office->id }}"
                        @selected(old('office_id', $user?->office_id) == $office->id)>
                    {{ $office->name }}
                </option>
            @endforeach
        </select>
        @error('office_id') <p class="{{ $errorClass }}">{{ $message }}</p> @enderror
    </div>

</div>

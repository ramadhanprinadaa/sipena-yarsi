<select wire:model.live="selectedStatusKehadiran"
    class="border border-gray-300 text-gray-900 text-sm rounded-md focus:ring-teal-500 focus:border-teal-500 block p-2.5">
    <option value="">Semua Status</option>
    @foreach ($this->statusKehadiranList as $status)
        <option value="{{ $status }}">{{ $status }}</option>
    @endforeach
</select>

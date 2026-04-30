<div class="bg-white w-2xl mx-auto p-6 rounded-xl shadow-lg">

  <!-- Header -->
  <header class="flex items-center justify-between pb-4 mb-5 border-b border-gray-300">
    <div class="flex items-center gap-3">
        <i class="fa-solid fa-building-circle-check text-2xl text-indigo-500"></i>
        <h2 class="text-lg font-semibold text-gray-700">
            Detail Unit Kerja
        </h2>
    </div>

    <!-- CLOSE -->
    <button
        type="button"
        @click="$dispatch('close-modal-detail')"
        class="w-7 h-7 flex items-center justify-center rounded-md bg-red-400 hover:bg-red-500 text-white cursor-pointer">
        <i class="fa-solid fa-xmark text-sm"></i>
    </button>
  </header>

</div>

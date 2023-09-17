<x-filament::page> - <strong>{{$this->record->name}}</strong>
 <form wire:submit.prevent=''>
    {{ $this->form }}

    <button type="submit" wire:click="save_data" class="filament-button filament-button-size-md inline-flex items-center justify-center py-1 gap-1 font-medium rounded-lg border transition-colors outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action"
    type="button"
          wire:loading.attr="disabled"
          wire:loading.class.delay="opacity-70 cursor-wait"
          x-data="{
              form: null,
              isUploadingFile: false,
          }"
                      x-bind:class="{ 'opacity-70 cursor-wait': isUploadingFile }"
                  x-bind:disabled="isUploadingFile"
          x-init="
              form = $el.closest('form')

              form?.addEventListener('file-upload-started', () => {
                  isUploadingFile = true
              })

              form?.addEventListener('file-upload-finished', () => {
                  isUploadingFile = false
              })
          ">
        Save Data
    </button>
</form>
</x-filament::page>

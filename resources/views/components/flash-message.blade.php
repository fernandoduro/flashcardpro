<div
    x-data="{
        show: false,
        message: '',
        type: 'success',
        timeout: null,
        showFlash(event) {
            // THE FIX IS HERE: Access the first element of event.detail
            const detail = event.detail[0] || {}; // Use a fallback for safety

            this.type = detail.type || 'success';
            this.message = detail.message || 'An unknown error occurred.'; // Add fallback message
            this.show = true;

            clearTimeout(this.timeout);
            this.timeout = setTimeout(() => this.show = false, 5000);
        }
    }"
    x-on:flash-message.window="showFlash($event)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-y-2"
    x-transition:enter-end="opacity-100 transform translate-y-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-y-0"
    x-transition:leave-end="opacity-0 transform translate-y-2"
    class="fixed bottom-24 right-8 z-50 px-4 py-3 rounded-lg shadow-lg border-l-4"
    :class="{
        'bg-green-100 border-green-400 text-green-700': type === 'success',
        'bg-red-100 border-red-400 text-red-700': type === 'error',
    }"
    role="alert"
    style="display: none;"
>
    <div class="flex items-center">
        <div class="py-1">
            <template x-if="type === 'success'">
                <i class="fa-solid fa-circle-check mr-3"></i>
            </template>
            <template x-if="type === 'error'">
                <i class="fa-solid fa-circle-exclamation mr-3"></i>
            </template>
        </div>
        <div>
            <p class="font-bold" x-text="message"></p>
        </div>
    </div>
</div>
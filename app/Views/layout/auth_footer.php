</div> <!-- End container -->

<!-- Toast Container -->
<div id="toast-container" class="fixed top-5 right-5 z-50 space-y-3 w-full max-w-sm">
    <div id="toast-template" class="hidden transform transition-all duration-300 ease-in-out">
        <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <ion-icon id="toast-icon" name="checkmark-circle" class="h-6 w-6"></ion-icon>
                    </div>
                    <div class="ml-3 w-0 flex-1 pt-0.5">
                        <p id="toast-message" class="text-sm font-medium text-gray-900"></p>
                    </div>
                    <div class="ml-4 flex-shrink-0 flex">
                        <button type="button" id="toast-close" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500">
                            <ion-icon name="close" class="h-5 w-5"></ion-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        const template = document.getElementById('toast-template');
        const newToast = template.cloneNode(true);
        newToast.id = '';

        const icon = newToast.querySelector('#toast-icon');
        const text = newToast.querySelector('#toast-message');
        const closeButton = newToast.querySelector('#toast-close');

        icon.id = '';
        text.id = '';
        closeButton.id = '';
        text.textContent = message;

        icon.classList.remove('text-green-500', 'text-red-500', 'text-blue-500');

        if (type === 'success') {
            icon.setAttribute('name', 'checkmark-circle');
            icon.classList.add('text-green-500');
        } else if (type === 'error') {
            icon.setAttribute('name', 'alert-circle');
            icon.classList.add('text-red-500');
        } else {
            icon.setAttribute('name', 'information-circle');
            icon.classList.add('text-blue-500');
        }

        newToast.classList.add('opacity-0', '-translate-y-12');
        newToast.classList.remove('hidden');
        container.appendChild(newToast);

        setTimeout(() => {
            newToast.classList.remove('opacity-0', '-translate-y-12');
            newToast.classList.add('opacity-100', 'translate-y-0');
        }, 100);

        closeButton.addEventListener('click', () => hideToast(newToast));
        setTimeout(() => hideToast(newToast), 4000);
    }

    function hideToast(toastElement) {
        toastElement.classList.remove('opacity-100', 'translate-y-0');
        toastElement.classList.add('opacity-0', '-translate-y-12');
        setTimeout(() => {
            if (toastElement.parentNode) {
                toastElement.remove();
            }
        }, 350);
    }
</script>

<?php \display_flash_message(); ?>
</body>

</html>
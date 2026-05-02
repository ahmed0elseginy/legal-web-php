</main>
</div>

<!-- Toast -->
<div id="toast" class="hidden fixed bottom-6 left-1/2 -translate-x-1/2 z-[100]">
    <div class="bg-dark-800 text-white px-6 py-3 rounded-xl shadow-xl flex items-center gap-3">
        <span class="iconify text-gold-400" id="toast-icon" data-icon="lucide:check-circle"></span>
        <p class="text-sm" id="toast-message">Action completed</p>
    </div>
</div>

<script>
function showToast(message, icon = 'lucide:check-circle') {
    const toast = document.getElementById('toast');
    const msg = document.getElementById('toast-message');
    const iconEl = document.getElementById('toast-icon');
    
    msg.textContent = message;
    iconEl.setAttribute('data-icon', icon);
    toast.classList.remove('hidden');
    
    setTimeout(() => {
        toast.classList.add('hidden');
    }, 3000);
}
</script>
</body>
</html>
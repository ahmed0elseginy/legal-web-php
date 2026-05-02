/**
 * MIZAN Platform - Main JavaScript (P5 & D2)
 * Demonstrates AJAX (Fetch API), structured code, and interactive UI.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Chat auto-scroll
    const chatMessages = document.querySelector(".chat-messages");
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Form debouncing/loading prevention
    document.querySelectorAll("form:not(.no-prevent)").forEach((form) => {
        form.addEventListener("submit", function () {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                setTimeout(() => (submitBtn.disabled = false), 2000);
            }
        });
    });

    initEmailCheck();
    initLiveSearch();
    initJQueryDelete();
});

/**
 * M3 Implementation: Using jQuery and JSON for AJAX deletion
 */
function initJQueryDelete() {
    $('.btn-delete-case').on('click', function(e) {
        e.preventDefault();
        const row = $(this).closest('tr');
        const caseId = $(this).data('id');

        if (confirm('هل أنت متأكد من حذف هذه القضية؟')) {
            $.ajax({
                url: `${SITE_URL}/actions/delete_case.php`,
                method: 'POST',
                data: { id: caseId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        row.fadeOut(400, function() { $(this).remove(); });
                    } else {
                        alert('حدث خطأ: ' + response.message);
                    }
                },
                error: function() {
                    alert('فشل الاتصال بالخادم');
                }
            });
        }
    });
}

/**
 * Initializes live case search using AJAX (D2)
 */
function initLiveSearch() {
    const searchInput = document.querySelector('.search-box input');
    if (!searchInput) return;

    // Create a results container dynamically
    const resultsDiv = document.createElement('div');
    resultsDiv.className = 'search-results-dropdown';
    searchInput.parentElement.style.position = 'relative';
    searchInput.parentElement.appendChild(resultsDiv);

    searchInput.addEventListener('input', debounce(async (e) => {
        const query = e.target.value.trim();
        if (query.length < 2) {
            resultsDiv.innerHTML = '';
            resultsDiv.style.display = 'none';
            return;
        }

        try {
            const baseUrl = typeof SITE_URL !== 'undefined' ? SITE_URL : '';
            const response = await fetch(`${baseUrl}/actions/search_cases.php?q=${encodeURIComponent(query)}`);
            const cases = await response.json();

            if (cases.length > 0) {
                resultsDiv.innerHTML = cases.map(c => `
                    <div class="search-item">
                        <strong>${c.title}</strong>
                        <span>${c.client_name} - ${c.status}</span>
                    </div>
                `).join('');
                resultsDiv.style.display = 'block';
            } else {
                resultsDiv.innerHTML = '<div class="search-item">لا توجد نتائج</div>';
                resultsDiv.style.display = 'block';
            }
        } catch (error) {
            console.error('Search error:', error);
        }
    }, 300));

    // Close on click outside
    document.addEventListener('click', (e) => {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.style.display = 'none';
        }
    });
}

/**
 * Initializes the real-time email availability check using AJAX (D2)
 */
function initEmailCheck() {
    const emailInput = document.getElementById('register-email');
    const feedback = document.getElementById('email-feedback');

    if (!emailInput || !feedback) return;

    emailInput.addEventListener('input', debounce(async (e) => {
        const email = e.target.value;
        if (email.length < 5 || !email.includes('@')) {
            feedback.textContent = '';
            return;
        }

        try {
            // SITE_URL should be defined globally in the template
            const baseUrl = typeof SITE_URL !== 'undefined' ? SITE_URL : '';
            const response = await fetch(`${baseUrl}/actions/check_email.php?email=${encodeURIComponent(email)}`);
            const data = await response.json();

            if (data.available) {
                feedback.textContent = 'البريد الإلكتروني متاح';
                feedback.className = 'feedback-text success';
            } else {
                feedback.textContent = 'البريد الإلكتروني مستخدم بالفعل';
                feedback.className = 'feedback-text error';
            }
        } catch (error) {
            console.error('Error checking email:', error);
        }
    }, 500));
}

/**
 * Utility: Debounce function to limit API calls (P5)
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

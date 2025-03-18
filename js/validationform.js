document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll("form");
    const successPopup = document.getElementById("success-popup");
    const successMessage = document.getElementById("success-message");
    const successClose = document.getElementById("success-close");
    const errorMessageElement = document.getElementById("error-message");
    const errorTextElement = errorMessageElement ? errorMessageElement.querySelector(".error-text") : null;
    const closeErrorButton = errorMessageElement ? errorMessageElement.querySelector(".close-btn") : null;
    let isSubmitting = false;
    // отримання всіх елементів з dom 
    if (!successPopup || !successMessage || !successClose || !errorMessageElement || !errorTextElement || !closeErrorButton) {
        console.error("Деякі елементи попапів не знайдено:", {
            successPopup, successMessage, successClose, errorMessageElement, errorTextElement, closeErrorButton
        });
        return;
    }

    const params = new URLSearchParams(window.location.search);
    const trackingData = {
        fbp: params.get("fbp") || "",
        ggl: params.get("ggl") || ""
    };
    // отримання токенів
    const usedPhoneNumbers = new Set();
    // валідація форми
    function validateFormData(formData) {
        const requiredFields = ["phone", "name", "last_name", "email", "select_service", "select_price"];
        for (const field of requiredFields) {
            if (!formData.has(field) || !formData.get(field).trim()) {
                return "Fill in all fields";
            }
        }
        const phone = formData.get("phone").replace(/[\s\-\(\)]/g, ''); // Видаляємо пробіли, тире, дужки
        if (!/^\+1\d{10}$/.test(phone)) {
            return "Incorrect phone number";
        }
        if (usedPhoneNumbers.has(phone)) {
            return "This phone number has already been used";
        }
        return null;
    }

    function showError(message) {
        errorTextElement.textContent = message;
        errorMessageElement.style.display = "flex";
        errorMessageElement.classList.add("show");
    }

    function hideError() {
        errorMessageElement.classList.remove("show");
        setTimeout(() => errorMessageElement.style.display = "none", 300);
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successPopup.style.display = "block";
        successPopup.classList.add("show");
    }
    // надсилання форми
    async function sendForm(event) {
        event.preventDefault();
        if (isSubmitting) return;
        isSubmitting = true;

        const formData = new FormData(event.target);
        const validationError = validateFormData(formData);
        if (validationError) {
            showError(validationError);
            isSubmitting = false;
            return;
        }

        for (const key in trackingData) {
            formData.append(key, trackingData[key]);
            console.log(`Додано до FormData: ${key}=${trackingData[key]}`);
        }

        try {
            const response = await fetch("submit.php", {
                method: "POST",
                body: formData
            });
            if (!response.ok) throw new Error("Сервер повернув помилку");
            const result = await response.json();
            if (result.success) {
                trackLeadSuccess();
                showSuccess(result.message);
                setTimeout(() => window.location.href = result.redirect_url || "success.php", 2000);
            } else {
                showError(result.message || "Виникла проблема з мережею");
            }
        } catch (error) {
            console.error("Помилка відправки форми:", error);
            showError("There is a problem with the network");
        } finally {
            isSubmitting = false;
        }
    }
    // надання нового статусу токенам
    if (trackingData.fbp) {
        !function (f, b, e, v, n, t, s) {
            if (f.fbq) return; n = f.fbq = function () {
                n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments)
            };
            if (!f._fbq) f._fbq = n; n.push = n; n.loaded = !0; n.version = '2.0';
            n.queue = []; (t = b.createElement(e)).async = !0;
            t.src = v; s = b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t, s)
        }(window, document, 'script', 'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', trackingData.fbp);
        fbq('track', 'PageView');
    }

    if (trackingData.ggl) {
        let script = document.createElement('script');
        script.async = true;
        script.src = `https://www.googletagmanager.com/gtag/js?id=${trackingData.ggl}`;
        script.onload = function () {
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', trackingData.ggl);
        };
        document.head.appendChild(script);
    }

    function trackLeadSuccess() {
        if (trackingData.fbp && typeof fbq === "function") fbq("track", "Lead");
        if (trackingData.ggl && typeof gtag === "function") gtag("event", "conversion", { send_to: trackingData.ggl + "/lead" });
    }

    closeErrorButton.addEventListener("click", hideError);
    successClose.addEventListener("click", () => {
        successPopup.classList.remove("show");
        window.location.href = "success.php";
    });

    // Ініціалізація Bootstrap Select
    $(document).ready(function () {
        $('.selectpicker').selectpicker();
    });

    forms.forEach((form) => form.addEventListener("submit", sendForm));
});



// робота з попап
document.querySelectorAll(".confirmation-popup, .error-message, .success-popup").forEach(popup => {
    popup.addEventListener("click", (e) => {
        if (e.target === popup) {
            popup.classList.remove("show");
        }
    });
});

function showPopup(popup) {
    popup.classList.add("show");
    document.body.classList.add("popup-open");
}

function hidePopup(popup) {
    popup.classList.remove("show");
    document.body.classList.remove("popup-open");
}
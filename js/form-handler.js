document.addEventListener("DOMContentLoaded", () => {
    const forms = document.querySelectorAll("form");

    // Отримуємо параметри з URL (fbp, ggl)
    const params = new URLSearchParams(window.location.search);
    const trackingData = {
        fbp: params.get("fbp") || "",
        ggl: params.get("ggl") || "",
        country: "",
        city: "",
        ip: ""
    };

    // Функція отримання геолокації
    async function fetchGeoData() {
        try {
            const response = await fetch("https://ip-api.com/json/");
            const data = await response.json();
            trackingData.country = data.country || "Невідомо";
            trackingData.city = data.city || "Невідомо";
            trackingData.ip = data.query || "Невідомо";
        } catch (error) {
            console.error("Помилка отримання геоданих", error);
        }
    }
    fetchGeoData();

    // Валідація даних форми
    function validateFormData(formData) {
        const formType = formData.get("form_type");

        if (formType === "form1") {
            if (!formData.get("name")) return "Введіть ім'я";
            if (!formData.get("email") || !/\S+@\S+\.\S+/.test(formData.get("email"))) return "Некоректний email";
        }

        if (formType === "form2") {
            if (!formData.get("phone") || !/^\+?[0-9\s\-\(\)]{7,20}$/.test(formData.get("phone"))) return "Некоректний номер телефону";
            if (!formData.get("comment")) return "Введіть коментар";
        }

        return null; // Якщо помилок немає
    }

    // Функція для відправки форми
    async function sendForm(event) {
        event.preventDefault();
        const form = event.target;
        const formData = new FormData(form);

        // Додаємо загальні дані (не дублюємо)
        for (const key in trackingData) {
            formData.append(key, trackingData[key]);
        }

        // Валідація перед відправкою
        const validationError = validateFormData(formData);
        if (validationError) {
            alert(validationError);
            return;
        }

        try {
            const response = await fetch("form-handler.php", {
                method: "POST",
                body: formData
            });

            const result = await response.json();
            alert(result.message);

            if (result.success) {
                trackLeadSuccess(); // Відправляємо статус ліда
                if (result.redirect_url) {
                    window.location.href = result.redirect_url;
                }
            }
        } catch (error) {
            console.error("Помилка відправки форми", error);
        }
    }

    // Функція фіксації успішного ліда в GTM і FB Pixel
    function trackLeadSuccess() {
        if (trackingData.fbp) {
            console.log("Відправлено lead статус у Facebook Pixel:", trackingData.fbp);
            fbq("track", "Lead");
        }
        if (trackingData.ggl) {
            console.log("Відправлено lead статус у Google Tag Manager:", trackingData.ggl);
            gtag("event", "conversion", { send_to: trackingData.ggl });
        }
    }

    // Додаємо обробник подій для кожної форми
    forms.forEach((form) => {
        form.addEventListener("submit", sendForm);
    });
});

window.addEventListener("load", () => {
    console.log("✅ Login page loaded");

    const form = document.querySelector("#login-form");
    const errorBox = document.createElement("div");
    errorBox.style.color = "red";
    errorBox.style.marginTop = "10px";
    form.appendChild(errorBox);

    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        console.log("✅ Login form submitted");

        errorBox.textContent = "⏳ Sending...";

        const formData = new FormData(e.target);

        try {
            const response = await sendAjax({
                url: "/crm/pages/packs/login/ajax/login.php",
                data: formData
            });

            console.log("✅ Server responded:", response);

            // Afișăm log-ul serverului dacă există
            if (response.log) {
                console.groupCollapsed("📜 Server Log");
                response.log.forEach((line, i) => {
                    console.log(`#${i + 1}:`, line);
                });
                console.groupEnd();
            }

            // Verificăm succesul
            if (response.success) {
                errorBox.textContent = "✅ Login successful! Redirect...";
                showToast(response.message ?? "Authentication successful!", "success");

                // Redirect
                setTimeout(() => {
                    location.href = response.redirect;
                }, 500);

            } else {
                // Eroare de la server
                const msg = response.message ?? "❌ error.";
                errorBox.textContent = msg;
                showToast(msg, "error");
            }

        } catch (err) {
            // Problema pe rețea / server
            console.error("❌ Fetch error:", err);
            errorBox.textContent = "❌ The request could not be sent. Please check your connection or try again later.";
            showToast("❌ Network or server error", "error");
        }
    });
});

window.addEventListener("load", () => {
    document.querySelector("#password-change-form").addEventListener("submit", async (e) => {
        e.preventDefault()
        const formData = new FormData(e.target);

        try {
            await sendAjax({url: "/crm/pages/packs/settings/ajax/change_password.php", data: formData})
        } catch (e) {}
    })
})
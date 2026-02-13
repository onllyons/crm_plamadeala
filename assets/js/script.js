function showToast(text, type = "success") {
    toastr.options = {
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "4000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }

    if (type === "error") toastr.error(text);
    else if (type === "warning") toastr.warning(text);
    else toastr.success(text);
}

async function sendAjax(data) {
    data = {url: "", data: null, showSuccess: true, showError: true, ...data}

    try {
        if (!data.url || !data.data) {
            if (data.showError) showToast("Error", "error")

            return Promise.reject({"message": "Error"})
        }

        const response = await fetch(data.url, {
            method: 'POST',
            body: data.data
        });

        if (!response.ok) {
            if (data.showError) showToast(`HTTP error! status: ${response.status}`, "error")

            return Promise.reject({"message": `HTTP error! status: ${response.status}`})
        }

        const rawText = await response.text();
        console.log("Raw server response:", rawText);

        let result;
        try {
            result = JSON.parse(rawText);
        } catch (parseError) {
            console.error("JSON parsing error:", parseError);
            console.error("Server sent:", rawText);
            if (data.showError) showToast("Invalid JSON from server", "error");
            return Promise.reject({"message": "Invalid JSON from server", "raw": rawText});
        }


        if (result.action === "reload") {
            location.reload()
            return Promise.reject({})
        }

        if (result.success !== undefined) {
            if (result.success) {
                if (data.showSuccess && result.message) showToast(result.message, "success")

                return result
            } else {
                if (data.showError && result.message) showToast(result.message, "error")

                return Promise.reject(result)
            }
        }

        if (data.showError) showToast("Undefined error, try again later", "error")

        return Promise.reject({"message": "Undefined error, try again later"})
    } catch (error) {
        if (data.showError && error.message) showToast("Undefined error from server, try again later", "error")
        console.error('Fetch error:', error);

        return Promise.reject(error)
    }
}
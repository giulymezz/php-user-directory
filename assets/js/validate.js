document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector(".filter-form");
    const fromInput = document.getElementById("from");
    const toInput = document.getElementById("to");

    const fromError = document.getElementById("error-from");
    const toError = document.getElementById("error-to");

    /**
     * Valid formats:
     *  - d/m/Y
     *  - d/m/Y H:i
     *  - d/m/Y H:i:s
     */
    function isValidDate(value) {
        value = value.trim();
        if (!value) {
            return true;
        }

        let datePart = "";
        let timePart = "";

        const parts = value.split(" ");

        if (parts.length === 1) {
            // Format: d/m/Y
            datePart = parts[0];
            timePart = "00:00:00";
        } else if (parts.length === 2) {
            // Format: d/m/Y H:i or d/m/Y H:i:s
            datePart = parts[0];
            timePart = parts[1];
        } else {
            return false;
        }

        const [d, m, y] = datePart.split("/").map(Number);

        if ([d, m, y].some(isNaN)) {
            return false;
        }

        let [hh, mm, ss] = timePart.split(":").map(Number);

        if (isNaN(hh) || isNaN(mm)) {
            return false;
        }

        if (isNaN(ss)) {
            ss = 0;
        }

        const jsDate = new Date(y, m - 1, d, hh, mm, ss);

        return (
            jsDate.getFullYear() === y &&
            jsDate.getMonth() === m - 1 &&
            jsDate.getDate() === d &&
            jsDate.getHours() === hh &&
            jsDate.getMinutes() === mm &&
            jsDate.getSeconds() === ss
        );
    }

    form.addEventListener("submit", function (e) {
        let valid = true;

        fromError.textContent = "";
        toError.textContent = "";
        fromError.classList.remove("visible");
        toError.classList.remove("visible");

        if (!isValidDate(fromInput.value)) {
            fromError.textContent = "Invalid date";
            fromError.classList.add("visible");
            valid = false;
        }

        if (!isValidDate(toInput.value)) {
            toError.textContent = "Invalid date";
            toError.classList.add("visible");
            valid = false;
        }

        if (!valid) {
            e.preventDefault();
        }
    });
});
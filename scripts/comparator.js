function init() {
    const containers = document.getElementsByClassName("comparator-site");

    Array.from(containers).forEach(container => {
        const slider = container.getElementsByClassName("slider-comparator")[0];
        let isDragging = false;

        const initialSize = parseFloat(container.dataset.comparatorSize);
        if (!isNaN(initialSize)) {
            container.style.setProperty("--site-comparator-size", Math.max(0, Math.min(initialSize, 100)) + "%");
        } else {
            container.style.setProperty("--site-comparator-size", "50%");
        }

        function updatePosition(clientX) {
            const rect = container.getBoundingClientRect();
            let x = clientX - rect.left;

            x = Math.max(0, Math.min(x, rect.width));

            const percent = (x / rect.width) * 100;
            container.style.setProperty("--site-comparator-size", percent + "%");
        }

        slider.addEventListener("mousedown", () => isDragging = true);
        window.addEventListener("mouseup", () => isDragging = false);
        window.addEventListener("mousemove", e => {
            if (isDragging) updatePosition(e.clientX);
        });

        slider.addEventListener("touchstart", () => isDragging = true);
        window.addEventListener("touchend", () => isDragging = false);
        window.addEventListener("touchmove", e => {
            if (isDragging) updatePosition(e.touches[0].clientX);
        });
    });
}

init();
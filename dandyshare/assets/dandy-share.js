document.addEventListener("DOMContentLoaded", () => {

    if (!navigator.share) {
        return;
    }

    document.querySelectorAll(".dandy-share").forEach(button => {

        button.hidden = false;

        button.addEventListener("click", async () => {

            try {

                await navigator.share({
                    title: button.dataset.title,
                    text: button.dataset.text,
                    url: button.dataset.url
                });

            } catch (e) {
                // User cancelled. Ignore.
            }

        });

    });

});